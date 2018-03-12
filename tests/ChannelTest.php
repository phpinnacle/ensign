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

use PHPinnacle\Ensign\Channel;

class ChannelTest extends EnsignTest
{
    /**
     * Test that Task can be waited for specified signal
     *
     * @test
     */
    public function waitSignal()
    {
        self::loop(function () {
            $channel = Channel::open('test');
            $emitter = function () {
                yield 'sig_1' => 1;

                try {
                    yield failure($error = new \InvalidArgumentException());
                } catch (\InvalidArgumentException $catch) {
                    self::assertSame($error, $catch);
                }

                yield new Stub\SimpleEvent();
            };

            $sigOne = $channel->wait('sig_1');
            $sigTwo = $channel->wait(Stub\SimpleEvent::class);

            yield coroutine($channel->attach($emitter()));

            self::assertPromise($sigOne);
            self::assertPromise($sigTwo);

            self::assertEquals(1, yield $sigOne);
            self::assertInstanceOf(Stub\SimpleEvent::class, yield $sigTwo);
        });
    }

    /**
     * Test that waiting for not emitted signals fails
     *
     * @test
     * @expectedException \PHPinnacle\Ensign\Exception\MissingSignal
     */
    public function failSignal()
    {
        self::loop(function () {
            $channel = Channel::open('test');
            $emitter = function () {
                yield 'sig_1' => 1;
                yield 'sig_2' => 2;
            };

            $signal = $channel->wait('sig_3');

            yield coroutine($channel->attach($emitter()));

            self::assertPromise($signal);

            $channel->close();

            yield $signal;
        });
    }

    /**
     * Test that waiting on closed channel fails
     *
     * @test
     * @expectedException \PHPinnacle\Ensign\Exception\ClosedChannel
     */
    public function waitOnClosed()
    {
        self::loop(function () {
            $channel = Channel::open('test');
            $emitter = function () {
                yield 'sig_1' => 1;
                yield 'sig_2' => 2;
            };

            yield coroutine($channel->attach($emitter()));

            $channel->close();

            $signal = $channel->wait('sig_3');

            yield $signal;
        });
    }

    /**
     * Test that attach generator to closed channel fails
     *
     * @test
     * @expectedException \PHPinnacle\Ensign\Exception\ClosedChannel
     */
    public function attachToClosed()
    {
        self::loop(function () {
            $channel = Channel::open('test');
            $emitter = function () {
                yield 'sig_1' => 1;
                yield 'sig_2' => 2;
            };

            $channel->close();

            yield coroutine($channel->attach($emitter()));
        });
    }

    /**
     * Test that Task can be iterated with emitted values
     *
     * @test
     */
    public function iterate()
    {
        self::loop(function () {
            $channel = Channel::open('test');
            $emitter = function () {
                yield 'sig_1' => 1;
                yield 'sig_1' => 1.1;
                yield 'sig_2' => 2;
                yield 'sig_2' => 2.1;
                yield 'sig_3' => 3;
            };

            yield coroutine($channel->attach($emitter()));

            self::assertEquals([
                'sig_1' => [1, 1.1],
                'sig_2' => [2, 2.1],
                'sig_3' => [3],
            ], iterator_to_array($channel));
        });
    }
}
