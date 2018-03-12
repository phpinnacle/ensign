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

use PHPinnacle\Ensign\Task;
use PHPinnacle\Ensign\HandlerMap;
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
            $handlers = new HandlerMap();
            $handlers
                ->register('upper', function ($text) {
                    return strtoupper($text);
                })
                ->register('lower', function ($text) {
                    return strtolower($text);
                })
            ;

            $dispatcher = new SignalDispatcher($handlers);

            self::assertInstanceOf(Task::class, $upperTask = $dispatcher->dispatch('upper', 'test'));
            self::assertInstanceOf(Task::class, $lowerTask = $dispatcher->dispatch('lower', 'TEST'));

            self::assertEquals('TEST', yield $upperTask);
            self::assertEquals('test', yield $lowerTask);
        });
    }

    /**
     * @test
     *
     * Test that Dispatcher can dispatch signal to coroutine
     */
    public function dispatchSignalToCoroutine()
    {
        self::loop(function () {
            $handlers = new HandlerMap();
            $handlers
                ->register('coroutine', function ($count) {
                    static $n = 1;

                    for ($i = 1; $i <= $count; $i++) {
                        $n *= $i;

                        yield 'event_' . $i => $n;
                    }

                    return $n;
                })
            ;

            $dispatcher = new SignalDispatcher($handlers);

            self::assertInstanceOf(Task::class, $task = $dispatcher->dispatch('coroutine', 3));
            self::assertEquals(6, yield $task);
            self::assertEquals([
                'event_1' => [1],
                'event_2' => [2],
                'event_3' => [6],
            ], iterator_to_array($task));
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
            $dispatcher = new SignalDispatcher(new HandlerMap());

            self::assertInstanceOf(Task::class, $failure = $dispatcher->dispatch('unknown'));

            yield $failure;
        });
    }
}
