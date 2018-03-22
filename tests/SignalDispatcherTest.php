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

use PHPinnacle\Ensign\HandlerRegistry;
use PHPinnacle\Ensign\Signal;
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
            $handlers = new HandlerRegistry();
            $handlers
                ->register('upper', function ($text) {
                    return strtoupper($text);
                })
                ->register('lower', function ($text) {
                    return strtolower($text);
                })
            ;

            $dispatcher = new SignalDispatcher($handlers);

            self::assertPromise($upperTask = $dispatcher->dispatch('upper', 'test'));
            self::assertPromise($lowerTask = $dispatcher->dispatch('lower', 'TEST'));

            self::assertEquals('TEST', yield $upperTask);
            self::assertEquals('test', yield $lowerTask);
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
            $handlers = new HandlerRegistry();
            $handlers
                ->register('coroutine', function ($count) {
                    try {
                        yield 'error' => $count;
                    } catch (\Exception $error) {
                        self::assertInstanceOf(\InvalidArgumentException::class, $error);
                        self::assertEquals('3', $error->getMessage());
                    }

                    yield new Signal('signal', $count - 1);

                    yield 'event' => $count + 1;

                    return $count * 2;
                })
                ->register('error', function ($num) {
                    throw new \InvalidArgumentException((string) $num);
                })
                ->register('signal', function ($num) {
                    self::assertEquals(2, $num);
                })
                ->register('event', function ($num) {
                    self::assertEquals(4, $num);
                })
            ;

            $dispatcher = new SignalDispatcher($handlers);

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
            $dispatcher = new SignalDispatcher(new HandlerRegistry());

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
            $handlers = new HandlerRegistry();
            $handlers
                ->register('invalid', function () {
                    yield 'test';
                })
            ;

            $dispatcher = new SignalDispatcher($handlers);

            yield $dispatcher->dispatch('invalid');
        });
    }
}
