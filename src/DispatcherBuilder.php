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

use Amp\Promise;

final class DispatcherBuilder
{
    /**
     * @var HandlerFactory
     */
    private $factory;

    /**
     * @var callable[][]
     */
    private $handlers = [];

    /**
     * @param HandlerFactory $factory
     */
    public function __construct(HandlerFactory $factory = null)
    {
        $this->factory = $factory ?: new HandlerFactory;
    }

    /**
     * @param string   $signal
     * @param callable $handler
     *
     * @return self
     */
    public function register(string $signal, callable $handler): self
    {
        $this->handlers[$signal][] = $handler;

        return $this;
    }

    /**
     * @return Dispatcher
     */
    public function build(): Dispatcher
    {
        $registry = new HandlerRegistry;

        foreach ($this->handlers as $signal => $handlers) {
            $handlers = \array_map(function (callable $handler) {
                return $this->factory->create($handler);
            }, $handlers);

            $registry->add($signal, static function (...$arguments) use ($handlers) {
                $promises = [];

                foreach ($handlers as $handler) {
                    $promises[] = yield $handler => $arguments;
                }

                return \count($promises) === 1 ? \current($promises) : Promise\all($promises);
            });
        }

        return new Dispatcher($registry);
    }
}
