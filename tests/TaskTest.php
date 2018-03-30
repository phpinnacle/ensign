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

use Amp\Success;
use PHPinnacle\Ensign\Task;
use PHPinnacle\Ensign\TaskToken;

class TaskTest extends EnsignTest
{
    /**
     * Test that Signal can be created
     *
     * @test
     * @expectedException \Amp\CancelledException
     */
    public function cancel()
    {
        self::loop(function () {
            $task = new Task(new Success(), new TaskToken());
            $task->cancel();

            yield $task;
        });
    }
}
