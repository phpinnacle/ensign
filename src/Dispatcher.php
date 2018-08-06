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

final class Dispatcher
{
    /**
     * @var Processor
     */
    private $processor;

    /**
     * @var callable[]
     */
    private $handlers = [];

    /**
     * @param Processor $processor
     */
    public function __construct(Processor $processor = null)
    {
        $this->processor = $processor ?: new Processor();
    }

    /**
     * @param string   $signal
     * @param callable $handler
     *
     * @return self
     */
    public function register(string $signal, callable $handler): self
    {
        $this->handlers[$signal] = $handler;

        $this->processor->interrupt($signal, function (...$arguments) use ($signal) {
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

        $handler = $this->handlers[$signal] ?? function () use ($signal) {
            throw new Exception\UnknownSignal($signal);
        };

        return $this->processor->execute($handler, $arguments);
    }
}
