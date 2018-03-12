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
use PHPinnacle\Ensign\HandlerMap;
use PHPinnacle\Ensign\Resolver;

class HandlerMapTest extends EnsignTest
{
    /**
     * Test that HandlerMap can register callable and return handler
     *
     * @param $name
     * @param $value
     *
     * @test
     * @dataProvider handlersList
     */
    public function registerCallable($name, $value)
    {
        $handlers = new HandlerMap();
        $handlers->register($name, function ($value) {
            return $value + 1;
        });

        self::assertInstanceOf(Handler::class, $handler = $handlers->get($name));
        self::assertEquals($value + 1, $handler($value));
    }

    /**
     * Test that HandlerMap can register Handler
     *
     * @test
     */
    public function registerHandler()
    {
        $handler = Handler::define(function () {
            return 'test';
        });

        $handlers = new HandlerMap();
        $handlers->register('test', $handler);

        self::assertSame($handler, $handlers->get('test'));
    }

    /**
     * Test that HandlerMap try resolving Arguments for handler
     *
     * @param $name
     * @param $value
     *
     * @test
     * @dataProvider handlersList
     */
    public function registerHandlerWithArgumentsResolving($name, $value)
    {
        $object = new \stdClass;
        $object->data = 42;

        $resolver = new Resolver\ObjectResolver([
            \stdClass::class => $object
        ]);

        $handlers = new HandlerMap($resolver);
        $handlers->register($name, function (\stdClass $object, $value) {
            return $object->data + $value;
        });

        self::assertInstanceOf(Handler::class, $handler = $handlers->get($name));
        self::assertEquals(42 + $value, $handler($value));
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
