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

use Amp\Coroutine;
use Amp\Failure;
use Amp\InvalidYieldError;
use Amp\Promise;
use Amp\Success;

final class Dispatcher
{
    /**
     * @var HandlerRegistry
     */
    private $handlers;

    /**
     * @param HandlerRegistry $handlers
     */
    public function __construct(HandlerRegistry $handlers)
    {
        $this->handlers = $handlers;
    }

    /**
     * @param string|object    $signal
     * @param mixed[]       ...$arguments
     *
     * @return Promise
     */
    public function dispatch($signal, ...$arguments): Promise
    {
        if (\is_object($signal)) {
            \array_unshift($arguments, $signal);

            $signal = \get_class($signal);
        }

        return $this->call($this->handlers->get($signal), $arguments);
    }

    /**
     * @param callable $handler
     * @param mixed[]  $arguments
     *
     * @return Promise
     */
    private function call(callable $handler, array $arguments): Promise
    {
        try {
            $result = $handler(...$arguments);
        } catch (\Throwable $error) {
            return new Failure($error);
        }

        if ($result instanceof \Generator) {
            return new Coroutine($this->recoil($result));
        }

        return $result instanceof Promise ? $result : new Success($result);
    }

    /**
     * @param \Generator $generator
     *
     * @return \Generator
     */
    private function recoil(\Generator $generator): \Generator
    {
        $step = 0;

        while ($generator->valid()) {
            try {
                // yield new Promise
                // yield [$promiseOne, $promiseTwo]

                // yield new Signal
                // yield new Signal => 'arg'
                // yield new Signal => ['arg', 'two']
                // yield Signal::class => [new Signal, 'arg', 'two']
                // yield 'signal'
                // yield 'signal' => 'arg'
                // yield 'signal' => ['arg', 'two']

                // yield function () {...}
                // yield function () {...} => ['arg', 'two']
                $current = $generator->current();
                $key     = $generator->key();

                if ($key === $step) {
                    $interrupt = $current;
                    $arguments = [];
                } else {
                    $interrupt = $key;
                    $arguments = \is_array($current) ? $current : [$current];

                    $step--;
                }

                if (\is_callable($interrupt)) {
                    $result = $this->call($interrupt, $arguments);
                } elseif (\is_array($interrupt) || $interrupt instanceof Promise) {
                    $result = yield $interrupt;
                } elseif ($this->isKnownSignal($interrupt)) {
                    $result = yield $this->dispatch($interrupt, ...$arguments);
                } else {
                    throw new Exception\InvalidYield($interrupt);
                }

                $generator->send($result);
            } catch (\Throwable $error) {
                $this->throw($generator, $error, $step);
            } finally {
                $step++;
            }
        };

        return $generator->getReturn();
    }

    /**
     * @param \Generator $generator
     * @param \Throwable $error
     * @param int        $step
     *
     * @return void
     */
    private function throw(\Generator $generator, \Throwable $error, int $step): void
    {
        try {
            $generator->throw($error);
        } catch (\Throwable $error) {
            throw new Exception\BadActionCall($step, $error);
        }
    }

    private function isKnownSignal($interrupt)
    {
        if (\is_object($interrupt)) {
            $interrupt = \get_class($interrupt);
        }

        if (false === \is_string($interrupt)) {
            return false;
        }

        return $this->handlers->has($interrupt);
    }
}
