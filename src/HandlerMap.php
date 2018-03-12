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

final class HandlerMap
{
    /**
     * @var ArgumentsResolver
     */
    private $resolver;

    /**
     * @var Handler[]
     */
    private $handlers = [];

    /**
     * @param ArgumentsResolver $resolver
     */
    public function __construct(ArgumentsResolver $resolver = null)
    {
        $this->resolver = $resolver ?: new Resolver\EmptyResolver();
    }

    /**
     * @param string   $name
     * @param callable $callable
     *
     * @return self
     */
    public function register(string $name, callable $callable): self
    {
        $this->handlers[$name] = $this->handler($callable);

        return $this;
    }

    /**
     * @param string $name
     *
     * @return Handler|null
     */
    public function get(string $name): ?Handler
    {
        return $this->handlers[$name] ?? null;
    }

    /**
     * @param callable $callable
     *
     * @return Handler
     */
    private function handler(callable $callable): Handler
    {
        if ($callable instanceof Handler) {
            return $callable;
        }

        return Handler::define($callable, $this->resolver->resolve($callable));
    }
}
