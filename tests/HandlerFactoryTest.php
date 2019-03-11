<?php
/**
 * This file is part of PHPinnacle/Ensign.
 *
 * (c) PHPinnacle Team <dev@phpinnacle.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPinnacle\Ensign\Tests;

use PHPinnacle\Ensign\HandlerFactory;
use PHPinnacle\Ensign\HandlerWrapper;

class HandlerFactoryTest extends EnsignTest
{
    /**
     * Test that HandlerFactory use wrappers to create handler
     */
    public function testWrap()
    {
        $factory = new HandlerFactory;

        $handler = function () {
            return 1;
        };

        $wrapper = new class implements HandlerWrapper {
            public function wrap(callable $handler): callable
            {
                return function () use ($handler) {
                    return $handler() + 1;
                };
            }
        };

        $factory->with($wrapper);

        self::assertSame($factory, $factory->with($wrapper));
        self::assertIsCallable($wrapped = $factory->create($handler));
        self::assertEquals(3, $wrapped());
    }
}
