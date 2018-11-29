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
use Amp\Promise;

final class Subroutine implements Promise
{
    /**
     * @var Executor
     */
    private $generator;

    /**
     * @var callable
     */
    private $resolver;

    /**
     * @param \Generator $generator
     * @param callable   $resolver
     */
    public function __construct(\Generator $generator, callable $resolver)
    {
        $this->generator = $generator;
        $this->resolver  = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function onResolve(callable $handler)
    {
        $coroutine = new Coroutine($this->recoil());
        $coroutine->onResolve($handler);
    }

    /**
     * @return \Generator
     */
    private function recoil(): \Generator
    {
        $step = 0;

        while ($this->generator->valid()) {
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
                $current = $this->generator->current();
                $key     = $this->generator->key();

                if ($key === $step) {
                    $interrupt = $current;
                    $arguments = [];
                } else {
                    $interrupt = $key;
                    $arguments = \is_array($current) ? $current : [$current];

                    $step--;
                }

                if (\is_array($interrupt)) {
                    $interrupt = Promise\all($interrupt);
                }

                if (!$interrupt instanceof Promise) {
                    $interrupt = $this->intercept($interrupt, $arguments);
                }

                $this->generator->send(yield $interrupt);
            } catch (\Throwable $error) {
                $this->throw($error, $step);
            } finally {
                $step++;
            }
        };

        return $this->generator->getReturn();
    }

    /**
     * @param \Throwable $error
     * @param int        $step
     *
     * @return void
     */
    private function throw(\Throwable $error, int $step): void
    {
        try {
            $this->generator->throw($error);
        } catch (\Throwable $error) {
            throw new Exception\BadActionCall($step, $error);
        }
    }

    /**
     * @param mixed $interrupt
     * @param array $arguments
     *
     * @return mixed
     */
    private function intercept($interrupt, array $arguments)
    {
        if (\is_object($interrupt)) {
            \array_unshift($arguments, $interrupt);

            $interrupt = \get_class($interrupt);
        }

        return \call_user_func($this->resolver, $interrupt, $arguments);
    }
}
