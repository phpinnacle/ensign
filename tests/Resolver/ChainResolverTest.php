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
use PHPinnacle\Ensign\Tests\Stub;

class ChainResolverTest extends EnsignTest
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

        $event = new Stub\SimpleEvent();
        $event->data = 'test';

        $resolver = new Resolver\ChainResolver(
            new Resolver\ObjectResolver([$object]),
            new Resolver\ObjectResolver([$event])
        );

        $arguments = $resolver->resolve(function (Stub\SimpleEvent $event, \stdClass $object, int $int) {});

        self::assertInstanceOf(Arguments::class, $arguments);
        self::assertCount(2, $arguments);
        self::assertEquals([$event, $object], iterator_to_array($arguments));
    }
}
