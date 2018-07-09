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

use PHPinnacle\Ensign\Handler;
use PHPinnacle\Ensign\Resolver;
use PHPinnacle\Ensign\Tests\EnsignTest;
use PHPinnacle\Ensign\Tests\Stub;

class ChainResolverTest extends EnsignTest
{
    /**
     * Test that Resolver try resolve arguments for callable
     *
     * @test
     */
    public function testResolve()
    {
        $object = new \stdClass;
        $object->data = 42;

        $event = new Stub\SimpleEvent();
        $event->data = 'test';

        $resolver = new Resolver\ChainResolver(
            new Resolver\ObjectResolver([
                \stdClass::class => $object,
            ]),
            new Resolver\ObjectResolver([
                Stub\SimpleEvent::class => $event,
            ])
        );

        $closure   = function (Stub\SimpleEvent $event, \stdClass $object, int $int) {};
        $arguments = $resolver->resolve(Handler::recognize($closure));

        self::assertArray($arguments);
        self::assertCount(2, $arguments);
        self::assertEquals([$event, $object], $arguments);
    }
}
