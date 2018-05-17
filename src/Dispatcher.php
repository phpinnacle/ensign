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
     * @var Arguments
     */
    private $resolver;

    /**
     * @var callable[]
     */
    private $handlers = [];

    /**
     * @param Processor $processor
     * @param Arguments $resolver
     */
    public function __construct(Processor $processor, Arguments $resolver)
    {
        $this->processor = $processor;
        $this->resolver  = $resolver;
    }

    /**
     * @param Arguments $resolver
     *
     * @return self
     */
    public static function instance(Arguments $resolver = null): self
    {
        return new self(new Processor(), $resolver ?: new Arguments\EmptyArguments());
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

        $handler   = $this->handlers[$signal] ?? $this->error(new Exception\UnknownSignal($signal));
        $arguments = $this->resolve($handler, $arguments);

        return $this->processor->execute($handler, ...$arguments);
    }

    /**
     * @param callable $handler
     * @param array    $arguments
     *
     * @return array
     */
    private function resolve(callable $handler, array $arguments): array
    {
        $resolved = $this->resolver->resolve($handler);

        foreach ($resolved as $position => $argument) {
            \array_splice($arguments, $position, 0, [$argument]);
        }

        return $arguments;
    }

    /**
     * @param \Exception $exception
     *
     * @return callable
     */
    private function error(\Exception $exception): callable
    {
        return function () use ($exception) {
            throw $exception;
        };
    }
}
