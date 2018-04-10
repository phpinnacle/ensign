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
use PHPinnacle\Ensign\Amp\AmpTask;
use PHPinnacle\Ensign\Amp\AmpToken;
use PHPinnacle\Ensign\Tests\EnsignTest;

class AmpTaskTest extends EnsignTest
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
            $task = new AmpTask(new Success(), new AmpToken());
            $task->cancel();

            yield $task;
        });
    }

    /**
     * Test that Task are thenable
     *
     * @test
     */
    public function then()
    {
        self::loop(function () {
            $task = new AmpTask(new Success(1), new AmpToken());
            $task->then(function (\Exception $error = null, $value = null) {
                $this->assertEquals(1, $value);
            });

            yield $task;
        });
    }
}
