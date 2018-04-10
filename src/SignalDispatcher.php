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
     * @var Processor
     */
    private $processor;

    /**
     * @var ArgumentsResolver
     */
    private $resolver;

    /**
     * @var callable[]
     */
    private $handlers = [];

    /**
     * @param Processor         $processor
     * @param ArgumentsResolver $resolver
     */
    public function __construct(Processor $processor, ArgumentsResolver $resolver)
    {
        $this->processor = $processor;
        $this->resolver  = $resolver;
    }

    /**
     * @param ArgumentsResolver $resolver
     *
     * @return self
     */
    public static function amp(ArgumentsResolver $resolver = null): self
    {
        return new self(new Amp\AmpProcessor(), $resolver ?: new Resolver\EmptyResolver());
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

        $handler   = $this->handlers[$signal] ?? $this->unknown($signal);
        $arguments = (new Arguments($arguments))->inject($this->resolver->resolve($handler));

        return $this->processor->execute($handler, ...$arguments);
    }

    /**
     * @param string $signal
     *
     * @return callable
     */
    private function unknown(string $signal): callable
    {
        return function () use ($signal) {
            throw new Exception\UnknownSignal($signal);
        };
    }
}
