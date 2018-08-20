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
            $dispatcher = new Dispatcher();
            $dispatcher->register('upper', function ($text) {
                return strtoupper($text);
            });
            $dispatcher->register('lower', function ($text) {
                return strtolower($text);
            });

            self::assertAction($upperAction = $dispatcher->dispatch('upper', 'test'));
            self::assertAction($lowerAction = $dispatcher->dispatch('lower', 'TEST'));

            self::assertEquals('TEST', yield $upperAction);
            self::assertEquals('test', yield $lowerAction);
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
            $dispatcher = new Dispatcher();
            $dispatcher->register(Stub\SimpleEvent::class, function (Stub\SimpleEvent $event) {
                return strtoupper($event->data);
            });

            self::assertAction($action = $dispatcher->dispatch(new Stub\SimpleEvent('test')));
            self::assertEquals('TEST', yield $action);
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
            $dispatcher = new Dispatcher();
            $dispatcher->register('coroutine', function ($count) {
                try {
                    yield 'error' => $count;
                } catch (\Exception $error) {
                    self::assertInstanceOf(\InvalidArgumentException::class, $error);
                    self::assertEquals('3', $error->getMessage());
                }

                yield new Stub\SimpleEvent($count + 1);

                return $count * 2;
            });
            $dispatcher->register('error', function ($num) {
                throw new \InvalidArgumentException((string) $num);
            });
            $dispatcher->register(Stub\SimpleEvent::class, function (Stub\SimpleEvent $event) {
                self::assertEquals(4, $event->data);
            });

            self::assertAction($action = $dispatcher->dispatch('coroutine', 3));
            self::assertEquals(6, yield $action);
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
            $dispatcher = new Dispatcher();

            self::assertAction($failure = $dispatcher->dispatch('unknown'));

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
            $dispatcher = new Dispatcher();
            $dispatcher->register('invalid', function () {
                yield 'test';
            });

            yield $dispatcher->dispatch('invalid');
        });
    }
}
