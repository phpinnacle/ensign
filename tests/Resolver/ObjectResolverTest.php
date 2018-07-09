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

class ObjectResolverTest extends EnsignTest
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

        $resolver = new Resolver\ObjectResolver([
            \stdClass::class => $object
        ]);

        $closure = function (\stdClass $object, int $value) {
            return [$object, $value];
        };

        $arguments = $resolver->resolve(Handler::recognize($closure));

        self::assertArray($arguments);
        self::assertEquals([$object], $arguments);
    }
}
