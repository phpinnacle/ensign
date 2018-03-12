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

class ObjectResolver extends ReflectionResolver
{
    /**
     * @var object[]
     */
    private $objects;

    /**
     * @param array $objects
     */
    public function __construct(array $objects)
    {
        $objects = \array_filter($objects, 'is_object');

        $this->objects = \array_combine(\array_map(function ($object) {
            return \get_class($object);
        }, $objects), $objects);
    }

    /**
     * {@inheritdoc}
     */
    protected function resolveArgument(\ReflectionClass $class): ?object
    {
        return $this->objects[$class->getName()] ?? null;
    }
}
