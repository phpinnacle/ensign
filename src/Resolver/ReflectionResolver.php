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

namespace PHPinnacle\Ensign\Resolver;

use PHPinnacle\Ensign\Arguments;
use PHPinnacle\Ensign\ArgumentsResolver;

abstract class ReflectionResolver implements ArgumentsResolver
{
    /**
     * {@inheritdoc}
     */
    public function resolve(callable $callable): Arguments
    {
        $arguments  = [];

        $closure    = \Closure::fromCallable($callable);
        $reflection = new \ReflectionMethod($closure, '__invoke');
        $parameters = $reflection->getParameters();

        foreach ($parameters as $parameter) {
            $class = $parameter->getClass();

            if ($class && $instance = $this->resolveArgument($class)) {
                $arguments[$parameter->getPosition()] = $instance;
            }
        }

        return new Arguments($arguments);
    }

    /**
     * @param \ReflectionClass $class
     *
     * @return object
     */
    abstract protected function resolveArgument(\ReflectionClass $class): ?object;
}
