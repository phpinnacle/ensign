<?php
/**
 * This file is part of PHPinnacle/Ensign.
 *
 * (c) PHPinnacle Team <dev@phpinnacle.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPinnacle\Ensign\Tests\Amp;

use Amp\Deferred;
use Amp\Delayed;
use Amp\Success;
use PHPinnacle\Ensign\Task;
use PHPinnacle\Ensign\Tests\EnsignTest;
use PHPinnacle\Ensign\Token;
use PHPinnacle\Identity\UUID;

class TaskTest extends EnsignTest
{
    /**
     * Test that Task can be canceled
     *
     * @test
     * @expectedException \PHPinnacle\Ensign\Exception\TaskCanceled
     */
    public function cancel()
    {
        self::loop(function () {
            $id   = UUID::random();
            $task = new Task($id, new Success(), new Token($id));
            $task->cancel();

            yield $task;
        });
    }

    /**
     * Test that Task throw exception when timeout reached
     *
     * @test
     * @expectedException \PHPinnacle\Ensign\Exception\TaskTimeout
     */
    public function timeout()
    {
        self::loop(function () {
            $id   = UUID::random();
            $task = new Task($id, new Delayed(100, true), new Token($id));
            $task->timeout(10);

            yield $task;
        });
    }

    /**
     * Test that Task can has timeout
     *
     * @test
     */
    public function fast()
    {
        self::loop(function () {
            $id   = UUID::random();
            $task = new Task($id, new Success(true), new Token($id));
            $task->timeout(100);

            self::assertTrue(yield $task);
        });
    }
}
