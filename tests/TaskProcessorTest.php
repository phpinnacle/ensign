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
use PHPinnacle\Ensign\TaskProcessor;
use PHPinnacle\Ensign\Resolver;

class TaskProcessorTest extends EnsignTest
{
    /**
     * Test that TaskProcessor try resolving Arguments for callable
     *
     * @test
     */
    public function executeWithArgumentsResolving()
    {
        $this->loop(function () {
            $object = new \stdClass;
            $object->data = 41;

            $processor = new TaskProcessor(new Resolver\ObjectResolver([
                \stdClass::class => $object
            ]));

            $task = $processor->execute(function (\stdClass $object, $value) {
                return $object->data + $value;
            }, 1);

            self::assertInstanceOf(Task::class, $task);
            self::assertEquals(42, yield $task);
        });
    }
}
