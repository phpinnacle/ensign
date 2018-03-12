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

class ObjectResolverTest extends EnsignTest
{
    /**
     * @test
     *
     * Test that Resolver try resolve arguments for callable
     */
    public function testResolve()
    {
        $object = new \stdClass;
        $object->data = 42;

        $resolver = new Resolver\ObjectResolver([$object]);

        $arguments = $resolver->resolve(function (\stdClass $object, int $value) {
            return [$object, $value];
        });

        self::assertInstanceOf(Arguments::class, $arguments);
        self::assertEquals([$object], iterator_to_array($arguments));
    }
}
