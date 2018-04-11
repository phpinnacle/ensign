<?php
/**
 * This file is part of PHPinnacle/Ensign.
 *
 * (c) PHPinnacle Team <dev@phpinnacle.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPinnacle\Ensign\Tests\Arguments;

use PHPinnacle\Ensign\Arguments;
use PHPinnacle\Ensign\Tests\EnsignTest;
use PHPinnacle\Ensign\Tests\Stub;

class MultiArgumentsTest extends EnsignTest
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

        $resolver = new Arguments\MultiArguments(
            new Arguments\ObjectArguments([
                \stdClass::class => $object,
            ]),
            new Arguments\ObjectArguments([
                Stub\SimpleEvent::class => $event,
            ])
        );

        $arguments = $resolver->resolve(function (Stub\SimpleEvent $event, \stdClass $object, int $int) {});

        self::assertArray($arguments);
        self::assertCount(2, $arguments);
        self::assertEquals([$event, $object], $arguments);
    }
}
