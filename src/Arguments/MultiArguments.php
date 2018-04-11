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

namespace PHPinnacle\Ensign\Arguments;

use PHPinnacle\Ensign\Arguments;

final class MultiArguments implements Arguments
{
    /**
     * @var Arguments[]
     */
    private $resolvers;

    /**
     * @param Arguments ...$resolvers
     */
    public function __construct(Arguments ...$resolvers)
    {
        $this->resolvers = $resolvers;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(callable $callable): array
    {
        $arguments = [];

        foreach ($this->resolvers as $resolver) {
            $arguments = \array_replace($arguments, $resolver->resolve($callable));
        }

        return $arguments;
    }
}
