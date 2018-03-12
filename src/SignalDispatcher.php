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

final class SignalDispatcher implements Dispatcher
{
    /**
     * @var HandlerMap
     */
    private $handlers;

    /**
     * @param HandlerMap $handlers
     */
    public function __construct(HandlerMap $handlers)
    {
        $this->handlers = $handlers;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(string $signal, ...$arguments): Task
    {
        $handler = $this->handler($signal);
        $channel = Channel::open($signal);

        $promise = \future(function () use ($channel, $handler, $arguments) {
            $result = $handler(...$arguments);

            if ($result instanceof \Generator) {
                $result = \coroutine($channel->attach($result));
            }

            return $result;
        });

        return new Task($promise, $channel);
    }

    /**
     * @param string $signal
     *
     * @return Handler
     */
    private function handler(string $signal): Handler
    {
        return $this->handlers->get($signal) ?: Handler::unknown($signal);
    }
}
