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

final class ChainResolver implements Resolver
{
    /**
     * @var Resolver[]
     */
    private $resolvers;

    /**
     * @param Resolver[] ...$resolvers
     */
    public function __construct(Resolver ...$resolvers)
    {
        $this->resolvers = $resolvers;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(Handler $handler): array
    {
        $arguments = [];

        foreach ($this->resolvers as $resolver) {
            $arguments = \array_replace($arguments, $resolver->resolve($handler));
        }

        return $arguments;
    }
}
