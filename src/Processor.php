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

use Amp\LazyPromise;
use Amp\Coroutine;
use PHPinnacle\Identity\UUID;

final class Processor
{
    /**
     * @var callable[]
     */
    private $interruptions = [];

    /**
     * @param string   $interrupt
     * @param callable $handler
     *
     * @return self
     */
    public function interrupt(string $interrupt, callable $handler): self
    {
        $this->interruptions[$interrupt] = $handler;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(callable $handler, array $arguments): Task
    {
        $id    = UUID::random();
        $token = new Token($id);

        return new Task($id, new LazyPromise(function () use ($handler, $arguments, $token) {
            return $this->adapt($handler(...$arguments), $token);
        }), $token);
    }

    /**
     * @param mixed $value
     * @param Token $token
     *
     * @return mixed
     */
    private function adapt($value, Token $token)
    {
        return $value instanceof \Generator ? new Coroutine($this->recoil($value, $token)) : $value;
    }

    /**
     * @param \Generator $generator
     * @param Token      $token
     *
     * @return mixed
     */
    private function recoil(\Generator $generator, Token $token)
    {
        while ($generator->valid()) {
            $token->guard();

            try {
                $value = $this->intercept($generator->key(), $generator->current());

                $generator->send(yield $this->adapt($value, $token));
            } catch (\Exception $error) {
                /** @scrutinizer ignore-call */
                $generator->throw($error);
            }
        }

        return $this->adapt($generator->getReturn(), $token);
    }

    /**
     * @param int|string $key
     * @param mixed      $value
     *
     * @return mixed
     */
    private function intercept($key, $value)
    {
        $interrupt = \is_string($key) ? $key : $value;

        if (\is_object($interrupt)) {
            $interrupt = \get_class($interrupt);
        }

        if (!\is_string($interrupt) || !isset($this->interruptions[$interrupt])) {
            return $value;
        }

        $interceptor = $this->interruptions[$interrupt];

        $value = \is_array($value) ? $value : [$value];

        return $interceptor(...$value);
    }
}
