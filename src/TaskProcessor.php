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

final class TaskProcessor implements Processor
{
    /**
     * @var ArgumentsResolver
     */
    private $resolver;

    /**
     * @var callable[]
     */
    private $interruptions = [];

    /**
     * @param ArgumentsResolver $resolver
     */
    public function __construct(ArgumentsResolver $resolver = null)
    {
        $this->resolver = $resolver ?: new Resolver\EmptyResolver();
    }

    /**
     * @param string   $interrupt
     * @param callable $interrupter
     */
    public function intercept(string $interrupt, callable $interrupter): void
    {
        $this->interruptions[$interrupt] = $interrupter;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(callable $callable, ...$arguments): Task
    {
        $token     = new TaskToken();
        $arguments = (new Arguments($arguments))->inject($this->resolver->resolve($callable));

        return new Task(new LazyPromise(function () use ($callable, $arguments, $token) {
            $result = $callable(...$arguments);

            return $result instanceof \Generator ? new Coroutine($this->recoil($result, $token)) : $result;
        }), $token);
    }

    /**
     * @param \Generator $generator
     * @param TaskToken  $token
     *
     * @return mixed
     */
    private function recoil(\Generator $generator, TaskToken $token)
    {
        while ($generator->valid()) {
            $token->guard();

            try {
                $key     = $generator->key();
                $current = $generator->current();

                $generator->send(yield $this->interrupt($key, $current));
            } catch (\Exception $error) {
                /** @scrutinizer ignore-call */
                $generator->throw($error);
            }
        }

        return $generator->getReturn();
    }

    /**
     * @param int|string $key
     * @param mixed      $value
     *
     * @return mixed
     */
    private function interrupt($key, $value)
    {
        if (isset($this->interruptions[$key])) {
            $interceptor = $this->interruptions[$key];

            $value = \is_array($value) ? $value : [$value];
            $value = $interceptor(...$value);
        }

        return $value;
    }
}
