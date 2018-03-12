<?php
/**
 * This file is part of PHPinnacle/Ensign.
 *
 * (c) PHPinnacle Team <dev@phpinnacle.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace PHPinnacle\Ensign;

use Amp\Deferred;
use Amp\Promise;

final class Channel implements \IteratorAggregate
{
    private const
        STATUS_ACTIVE = 1,
        STATUS_CLOSED = 0
    ;

    /**
     * @var string
     */
    private $signal;

    /**
     * @var int
     */
    private $status = self::STATUS_ACTIVE;

    /**
     * @var array
     */
    private $buffer = [];

    /**
     * @var Deferred[][]
     */
    private $readers = [];

    /**
     * @param string $signal
     */
    private function __construct(string $signal)
    {
        $this->signal = $signal;
    }

    /**
     * @param string $signal
     *
     * @return self
     */
    public static function open(string $signal): self
    {
        return new self($signal);
    }

    /**
     * @param string $signal
     *
     * @return Promise
     * @throws Exception\ClosedChannel
     */
    public function wait(string $signal): Promise
    {
        if ($this->isClosed()) {
            throw new Exception\ClosedChannel($this->signal);
        }

        if (!empty($this->buffer[$signal])) {
            $item = \array_shift($this->buffer[$signal]);

            return \success($item);
        }

        $this->readers[$signal][] = $deferred = new Deferred();

        return $deferred->promise();
    }

    /**
     * @param \Generator $generator
     *
     * @return mixed
     * @throws Exception\ClosedChannel
     */
    public function attach(\Generator $generator)
    {
        if ($this->isClosed()) {
            throw new Exception\ClosedChannel($this->signal);
        }

        while ($generator->valid()) {
            $key     = $generator->key();
            $current = $generator->current();

            if (!\is_promise($current)) {
                if (\is_object($current)) {
                    $key = \get_class($current);
                }

                if (\is_string($key)) {
                    $this->emit($key, $current);
                }
            }

            try {
                $generator->send(yield \promise($current));
            } catch (\Exception $error) {
                /** @scrutinizer ignore-call */
                $generator->throw($error);
            }
        }

        return $generator->getReturn();
    }

    /**
     * @return void
     */
    public function close(): void
    {
        $this->status = self::STATUS_CLOSED;

        $signals = $this->buffer;

        foreach ($signals as $signal => $list) {
            foreach ($list as $item) {
                $this->emit($signal, $item);
            }
        }

        foreach ($this->readers as $signal => $readers) {
            foreach ($readers as $reader) {
                $reader->fail(new Exception\MissingSignal($signal));
            }
        }

        $this->buffer  = [];
        $this->readers = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        yield from $this->buffer;
    }

    /**
     * @return bool
     */
    private function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }

    /**
     * @param string $signal
     * @param mixed  $value
     */
    private function emit(string $signal, $value): void
    {
        if (empty($this->readers[$signal])) {
            $this->buffer[$signal][] = $value;

            return;
        }

        $reader = \array_shift($this->readers[$signal]);
        $reader->resolve($value);
    }
}
