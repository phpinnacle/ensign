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

use PHPinnacle\Ensign\Task;
use PHPinnacle\Ensign\Amp\AmpProcessor;
use PHPinnacle\Ensign\Tests\EnsignTest;

class AmpProcessorTest extends EnsignTest
{
    /**
     * Test that Processor can execute callable
     *
     * @test
     */
    public function execute()
    {
        $this->loop(function () {
            $processor = new AmpProcessor();

            $task = $processor->execute(function ($value) {
                return $value * 2;
            }, 21);

            self::assertInstanceOf(Task::class, $task);
            self::assertEquals(42, yield $task);
        });
    }
}
