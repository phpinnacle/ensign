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

use PHPinnacle\Ensign\Arguments;
use PHPinnacle\Ensign\Dispatcher;

class DispatcherTest extends EnsignTest
{
    /**
     * Test that Dispatcher can dispatch proper signals
     *
     * @test
     */
    public function dispatchSignals()
    {
        self::loop(function () {
            $dispatcher = Dispatcher::amp();
            $dispatcher
                ->register('upper', function ($text) {
                    return strtoupper($text);
                })
                ->register('lower', function ($text) {
                    return strtolower($text);
                })
            ;

            self::assertTask($upperTask = $dispatcher->dispatch('upper', 'test'));
            self::assertTask($lowerTask = $dispatcher->dispatch('lower', 'TEST'));

            self::assertEquals('TEST', yield $upperTask);
            self::assertEquals('test', yield $lowerTask);
        });
    }

    /**
     * Test that Dispatcher can resolve arguments for handler
     *
     * @test
     */
    public function dispatchWithArgumentsResolving()
    {
        self::loop(function () {
            $object = new \stdClass();
            $object->id = 1;

            $dispatcher = Dispatcher::amp(new Arguments\ObjectArguments([
                \stdClass::class => $object,
            ]));
            $dispatcher
                ->register('resolve', function (\stdClass $object) {
                    return $object;
                })
            ;

            self::assertTask($task = $dispatcher->dispatch('resolve'));
            self::assertSame($object, yield $task);
        });
    }

    /**
     * Test that Dispatcher can dispatch object as signal
     *
     * @test
     */
    public function dispatchObject()
    {
        self::loop(function () {
            $dispatcher = Dispatcher::amp();
            $dispatcher
                ->register(Stub\SimpleEvent::class, function (Stub\SimpleEvent $event) {
                    return strtoupper($event->data);
                })
            ;

            self::assertTask($task = $dispatcher->dispatch(new Stub\SimpleEvent('test')));
            self::assertEquals('TEST', yield $task);
        });
    }

    /**
     * Test that Dispatcher can dispatch signal from coroutine
     *
     * @test
     */
    public function dispatchSignalFromCoroutine()
    {
        self::loop(function () {
            $dispatcher = Dispatcher::amp();
            $dispatcher
                ->register('coroutine', function ($count) {
                    try {
                        yield 'error' => $count;
                    } catch (\Exception $error) {
                        self::assertInstanceOf(\InvalidArgumentException::class, $error);
                        self::assertEquals('3', $error->getMessage());
                    }

                    yield new Stub\SimpleEvent($count + 1);

                    return $count * 2;
                })
                ->register('error', function ($num) {
                    throw new \InvalidArgumentException((string) $num);
                })
                ->register(Stub\SimpleEvent::class, function (Stub\SimpleEvent $event) {
                    self::assertEquals(4, $event->data);
                })
            ;

            self::assertTask($task = $dispatcher->dispatch('coroutine', 3));
            self::assertEquals(6, yield $task);
        });
    }

    /**
     * Test that Dispatcher can handle not registered signals
     *
     * @test
     * @expectedException \PHPinnacle\Ensign\Exception\UnknownSignal
     */
    public function dispatchUnknownSignal()
    {
        self::loop(function () {
            $dispatcher = Dispatcher::amp();

            self::assertTask($failure = $dispatcher->dispatch('unknown'));

            yield $failure;
        });
    }

    /**
     * Test that Dispatcher can handle not registered signals
     *
     * @test
     * @expectedException \Amp\InvalidYieldError
     */
    public function invalidYieldValue()
    {
        self::loop(function () {
            $dispatcher = Dispatcher::amp();
            $dispatcher
                ->register('invalid', function () {
                    yield 'test';
                })
            ;

            yield $dispatcher->dispatch('invalid');
        });
    }
}
