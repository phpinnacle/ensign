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

use Amp\Delayed;
use PHPinnacle\Ensign\Processor;
use PHPinnacle\Ensign\Task;
use PHPinnacle\Ensign\Tests\EnsignTest;

class ProcessorTest extends EnsignTest
{
    /**
     * Test that Processor can execute callable
     *
     * @test
     */
    public function execute()
    {
        $this->loop(function () {
            $processor = new Processor();

            $task = $processor->execute(function ($value) {
                return $value * 2;
            }, 21);

            self::assertInstanceOf(Task::class, $task);
            self::assertEquals(1, $task->id());
            self::assertEquals(42, yield $task);
        });
    }

    /**
     * Test that Processor can execute coroutine
     *
     * @test
     */
    public function coroutine()
    {
        $this->loop(function () {
            $processor = new Processor();

            $task = $processor->execute(function ($value) {
                yield new Delayed(10);

                return $value * 2;
            }, 21);

            self::assertInstanceOf(Task::class, $task);
            self::assertEquals(1, $task->id());
            self::assertEquals(42, yield $task);
        });
    }
}
