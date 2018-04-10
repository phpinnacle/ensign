<?php
/**
 * This file is part of PHPinnacle/Ensign.
 *
 * (c) PHPinnacle Team <dev@phpinnacle.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPinnacle\Ensign\Tests\Resolver;

use PHPinnacle\Ensign\Arguments;
use PHPinnacle\Ensign\Resolver;
use PHPinnacle\Ensign\Tests\EnsignTest;

class EmptyResolverTest extends EnsignTest
{
    /**
     * Test that Resolver try resolve arguments for callable
     *
     * @test
     */
    public function testResolve()
    {
        $resolver = new Resolver\EmptyResolver();

        $arguments = $resolver->resolve(function (string $string, int $int) {});

        self::assertInstanceOf(Arguments::class, $arguments);
        self::assertCount(0, $arguments);
    }
}
