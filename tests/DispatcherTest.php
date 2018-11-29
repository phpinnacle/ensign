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

            self::assertPromise($upperAction = $dispatcher->dispatch('upper', 'test'));
            self::assertPromise($lowerAction = $dispatcher->dispatch('lower', 'TEST'));

            self::assertEquals('TEST', yield $upperAction);
            self::assertEquals('test', yield $lowerAction);
        });
    }

    /**
     * Test that Dispatcher can dispatch any type of signals
     *
     * @test
     */
    public function dispatchAnyTypeSignals()
    {
        self::loop(function () {
            $dispatcher = new Dispatcher();
            $dispatcher->register('run', function () {
                // string syntax
                self::assertEquals(1, yield 'one');
                // string syntax with args
                self::assertEquals(1, yield 'num' => 1);

                // object short syntax
                self::assertEquals(1, yield new Stub\SimpleEvent(1));
                // object full syntax
                self::assertEquals(1, yield Stub\SimpleEvent::class => new Stub\SimpleEvent(1));

                // object short syntax with args
                self::assertEquals(['key' => 1], yield new Stub\SimpleCommand('key') => 1);
                // object short syntax with args array
                self::assertEquals(['key' => 1], yield new Stub\SimpleCommand('key') => [1]);
                // object full syntax with args array
                self::assertEquals(['key' => 1], yield Stub\SimpleCommand::class => [new Stub\SimpleCommand('key'), 1]);
            });
            $dispatcher->register('one', function () {
                return 1;
            });
            $dispatcher->register('num', function (int $num) {
                return $num;
            });
            $dispatcher->register(Stub\SimpleEvent::class, function (Stub\SimpleEvent $event) {
                return $event->data;
            });
            $dispatcher->register(Stub\SimpleCommand::class, function (Stub\SimpleCommand $command, $arg) {
                return [
                    $command->data => $arg,
                ];
            });

            yield $dispatcher->dispatch('run');
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

            self::assertPromise($action = $dispatcher->dispatch(new Stub\SimpleEvent('test')));
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

            self::assertPromise($action = $dispatcher->dispatch('coroutine', 3));
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

            self::assertPromise($failure = $dispatcher->dispatch('unknown'));

            yield $failure;
        });
    }

    /**
     * Test that Dispatcher can handle not registered signals
     *
     * @test
     * @expectedException \PHPinnacle\Ensign\Exception\BadActionCall
     */
    public function yieldNotSignal()
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
