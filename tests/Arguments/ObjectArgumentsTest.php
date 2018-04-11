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

class ObjectArgumentsTest extends EnsignTest
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

        $resolver = new Arguments\ObjectArguments([
            \stdClass::class => $object
        ]);

        $arguments = $resolver->resolve(function (\stdClass $object, int $value) {
            return [$object, $value];
        });

        self::assertArray($arguments);
        self::assertEquals([$object], $arguments);
    }
}
