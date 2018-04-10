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

namespace PHPinnacle\Ensign\Amp;

use Amp\LazyPromise;
use Amp\Coroutine;
use PHPinnacle\Ensign\Processor;
use PHPinnacle\Ensign\Task;

final class AmpProcessor implements Processor
{
    /**
     * @var callable[]
     */
    private $interruptions = [];

    /**
     * @param string   $interrupt
     * @param callable $interrupter
     */
    public function interrupt(string $interrupt, callable $interrupter): void
    {
        $this->interruptions[$interrupt] = $interrupter;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(callable $callable, ...$arguments): Task
    {
        $token = new AmpToken();

        return new AmpTask(new LazyPromise(function () use ($callable, $arguments, $token) {
            return $this->adapt($callable(...$arguments), $token);
        }), $token);
    }

    /**
     * @param mixed     $value
     * @param AmpToken $token
     *
     * @return mixed
     */
    private function adapt($value, AmpToken $token)
    {
        return $value instanceof \Generator ? new Coroutine($this->recoil($value, $token)) : $value;
    }

    /**
     * @param \Generator $generator
     * @param AmpToken  $token
     *
     * @return mixed
     */
    private function recoil(\Generator $generator, AmpToken $token)
    {
        while ($generator->valid()) {
            $token->guard();

            try {
                $key   = $generator->key();
                $value = $this->intercept($key, $generator->current());

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
        if (!\is_string($key) && \is_object($value)) {
            $key = \get_class($value);
        }

        if (isset($this->interruptions[$key])) {
            $interceptor = $this->interruptions[$key];

            $value = \is_array($value) ? $value : [$value];
            $value = $interceptor(...$value);
        }

        return $value;
    }
}
