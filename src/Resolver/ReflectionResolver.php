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

use PHPinnacle\Ensign\Handler;
use PHPinnacle\Ensign\Resolver;

abstract class ReflectionResolver implements Resolver
{
    /**
     * {@inheritdoc}
     */
    public function resolve(Handler $handler): array
    {
        $arguments = [];

        foreach ($handler->parameters() as $parameter) {
            if (!$class = $parameter->getClass()) {
                continue;
            }

            $expected = $class->getName();
            $instance = $this->resolveArgument($class);

            if (\is_object($instance) && $instance instanceof $expected) {
                $arguments[$parameter->getPosition()] = $instance;
            }
        }

        return $arguments;
    }

    /**
     * @param \ReflectionClass $class
     *
     * @return object
     */
    abstract protected function resolveArgument(\ReflectionClass $class);
}
