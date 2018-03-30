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

final class SignalDispatcher implements Dispatcher
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
     * {@inheritdoc}
     */
    public function dispatch($signal, ...$arguments): Task
    {
        if (!$signal instanceof Signal) {
            $signal = Signal::create($signal, $arguments);
        }

        $token = new TaskToken();

        return new Task(new LazyPromise(function () use ($signal, $token) {
            $handler = $this->handler($signal);
            $result  = $handler(...$signal->arguments());

            return $result instanceof \Generator ? new Coroutine($this->recoil($result, $token)) : $result;
        }), $token);
    }

    /**
     * @param Signal $signal
     *
     * @return Handler
     */
    private function handler(Signal $signal): Handler
    {
        $name = $signal->name();

        return $this->handlers->get($name) ?: Handler::unknown($name);
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

                $generator->send(yield $this->adapt($key, $current));
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
    private function adapt($key, $value)
    {
        if ($value instanceof Signal) {
            return $this->dispatch($value);
        }

        if (\is_string($key)) {
            $current = \is_array($value) ? \array_values($value) : [$value];

            return $this->dispatch($key, ...$current);
        }

        return $value;
    }
}
