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
use PHPinnacle\Ensign\Task;

class TaskTest extends EnsignTest
{
    /**
     * Test that Task can be awaited
     *
     * @test
     */
    public function yielding()
    {
        self::loop(function () {
            $task = new Task(success(true), Channel::open('test'));

            self::assertTrue(yield $task);
        });
    }

    /**
     * Test that Task can be waited for specified signal
     *
     * @test
     */
    public function waiting()
    {
        self::loop(function () {
            $channel = Channel::open('test');
            $emitter = function () {
                yield 'sig_1' => 1;
                yield 'sig_0' => 3;
                yield 'sig_2' => 2;
            };

            yield coroutine($channel->attach($emitter()));

            $task   = new Task(success(true), $channel);
            $sigOne = $task->wait('sig_1');
            $sigTwo = $task->wait('sig_2');

            self::assertPromise($sigOne);
            self::assertPromise($sigTwo);

            self::assertEquals(1, yield $sigOne);
            self::assertEquals(2, yield $sigTwo);
        });
    }

    /**
     * Test that waiting on canceled task fails
     *
     * @test
     * @expectedException \PHPinnacle\Ensign\Exception\ClosedChannel
     */
    public function waitOnCanceled()
    {
        self::loop(function () {
            $channel = Channel::open('test');
            $emitter = function () {
                yield 'sig_1' => 1;
                yield 'sig_2' => 2;
            };

            yield coroutine($channel->attach($emitter()));

            $task = new Task(success(true), $channel);
            $task->cancel();

            $signal = $task->wait('sig_3');

            yield $signal;
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

            $task = new Task(success(), $channel);

            self::assertEquals([
                'sig_1' => [1, 1.1],
                'sig_2' => [2, 2.1],
                'sig_3' => [3],
            ], iterator_to_array($task));
        });
    }
}
