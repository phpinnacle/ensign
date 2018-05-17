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

use Amp\Success;
use PHPinnacle\Ensign\Task;
use PHPinnacle\Ensign\Tests\EnsignTest;
use PHPinnacle\Ensign\Token;

class TaskTest extends EnsignTest
{
    /**
     * Test that Task can be canceled
     *
     * @test
     * @expectedException \Amp\CancelledException
     */
    public function cancel()
    {
        self::loop(function () {
            $task = new Task(new Success(), new Token());
            $task->cancel();

            yield $task;
        });
    }
}
