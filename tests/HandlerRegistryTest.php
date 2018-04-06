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

use PHPinnacle\Ensign\Handler;
use PHPinnacle\Ensign\HandlerRegistry;
use PHPinnacle\Ensign\Resolver;

class HandlerRegistryTest extends EnsignTest
{
    /**
     * Test that HandlerRegistry can register callable and return Handler
     *
     * @param $name
     * @param $value
     *
     * @test
     * @dataProvider handlersList
     */
    public function registerCallable($name, $value)
    {
        $handlers = new HandlerRegistry();
        $handlers->register($name, function ($value) {
            return $value + 1;
        });

        self::assertInstanceOf(Handler::class, $handler = $handlers->acquire($name));
        self::assertEquals($value + 1, $handler($value));
    }

    /**
     * Test that HandlerRegistry can register Handler
     *
     * @test
     */
    public function registerHandler()
    {
        $handler = new Handler(function () {
            return 'test';
        });

        $handlers = new HandlerRegistry();
        $handlers->register('test', $handler);

        self::assertSame($handler, $handlers->acquire('test'));
    }

    /**
     * @return array
     */
    public function handlersList()
    {
        return [
            'one' => ['one', 1],
            'two' => ['two', 2],
        ];
    }
}
