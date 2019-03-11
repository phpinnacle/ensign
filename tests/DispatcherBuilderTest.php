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

use PHPinnacle\Ensign\Dispatcher;
use PHPinnacle\Ensign\DispatcherBuilder;

class DispatcherBuilderTest extends EnsignTest
{
    /**
     * Test that DispatcherBuilder can build Dispatcher
     */
    public function testSimpleBuild()
    {
        self::loop(function () {
            $builder = new DispatcherBuilder;
            $handler = function () {
                return 1;
            };

            self::assertSame($builder, $builder->register('signal', $handler));
            self::assertInstanceOf(Dispatcher::class, $dispatcher = $builder->build());
            self::assertEquals(1, yield $dispatcher->dispatch('signal'));
        });
    }

    /**
     * Test that DispatcherBuilder can build with multiple handlers for one signal
     */
    public function testBuildMultipleHandlers()
    {
        self::loop(function () {
            $builder = new DispatcherBuilder;

            $handlerOne = function (int $arg) {
                return $arg + 1;
            };
            $handlerTwo = function (int $arg, int $default = 1) {
                return $arg + $default + 2;
            };

            self::assertSame($builder, $builder->register('signal', $handlerOne));
            self::assertSame($builder, $builder->register('signal', $handlerTwo));
            self::assertInstanceOf(Dispatcher::class, $dispatcher = $builder->build());
            self::assertEquals([3, 5], yield $dispatcher->dispatch('signal', 2));
        });
    }
}
