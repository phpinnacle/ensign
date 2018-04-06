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
     * @var TaskProcessor
     */
    private $processor;

    /**
     * @var HandlerRegistry
     */
    private $handlers;

    /**
     * @param TaskProcessor   $processor
     */
    public function __construct(TaskProcessor $processor = null)
    {
        $this->processor = $processor ?: new TaskProcessor();
        $this->handlers  = new HandlerRegistry();
    }

    /**
     * @param string   $signal
     * @param callable $handler
     *
     * @return self
     */
    public function register(string $signal, callable $handler): self
    {
        $this->handlers->register($signal, $handler);
        $this->processor->intercept($signal, function (...$arguments) use ($signal) {
            return $this->dispatch($signal, ...$arguments);
        });

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($signal, ...$arguments): Task
    {
        if (\is_object($signal)) {
            \array_unshift($arguments, $signal);

            $signal = \get_class($signal);
        }

        return $this->processor->execute($this->handlers->acquire($signal), ...$arguments);
    }
}
