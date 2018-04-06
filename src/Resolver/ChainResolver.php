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

final class ChainResolver implements ArgumentsResolver
{
    /**
     * @var ArgumentsResolver[]
     */
    private $resolvers;

    /**
     * @param ArgumentsResolver ...$resolvers
     */
    public function __construct(ArgumentsResolver ...$resolvers)
    {
        $this->resolvers = $resolvers;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(callable $callable): Arguments
    {
        $arguments = Arguments::empty();

        foreach ($this->resolvers as $resolver) {
            $resolved  = $resolver->resolve($callable);
            $arguments = $arguments->merge($resolved);
        }

        return $arguments;
    }
}
