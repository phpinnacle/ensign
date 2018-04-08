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

use PHPinnacle\Ensign\SignalDispatcher;

class SignalDispatcherTest extends EnsignTest
{
    /**
     * @test
     *
     * Test that Dispatcher can dispatch proper signals
     */
    public function dispatchSignals()
    {
        self::loop(function () {
            $dispatcher = new SignalDispatcher();
            $dispatcher
                ->register('upper', function ($text) {
                    return strtoupper($text);
                })
                ->register('lower', function ($text) {
                    return strtolower($text);
                })
            ;

            self::assertPromise($upperTask = $dispatcher->dispatch('upper', 'test'));
            self::assertPromise($lowerTask = $dispatcher->dispatch('lower', 'TEST'));

            self::assertEquals('TEST', yield $upperTask);
            self::assertEquals('test', yield $lowerTask);
        });
    }

    /**
     * @test
     *
     * Test that Dispatcher can dispatch object as signal
     */
    public function dispatchObject()
    {
        self::loop(function () {
            $dispatcher = new SignalDispatcher();
            $dispatcher
                ->register(Stub\SimpleEvent::class, function (Stub\SimpleEvent $event) {
                    return strtoupper($event->data);
                })
            ;

            self::assertPromise($task = $dispatcher->dispatch(new Stub\SimpleEvent('test')));
            self::assertEquals('TEST', yield $task);
        });
    }

    /**
     * @test
     *
     * Test that Dispatcher can dispatch signal from coroutine
     */
    public function dispatchSignalFromCoroutine()
    {
        self::loop(function () {
            $dispatcher = new SignalDispatcher();
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

            self::assertPromise($task = $dispatcher->dispatch('coroutine', 3));
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
            $dispatcher = new SignalDispatcher();

            self::assertPromise($failure = $dispatcher->dispatch('unknown'));

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
            $dispatcher = new SignalDispatcher();
            $dispatcher
                ->register('invalid', function () {
                    yield 'test';
                })
            ;

            yield $dispatcher->dispatch('invalid');
        });
    }
}
