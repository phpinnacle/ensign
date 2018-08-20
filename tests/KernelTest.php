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

use Amp\Delayed;
use PHPinnacle\Ensign\Kernel;

class KernelTest extends EnsignTest
{
    /**
     * Test that Processor can execute callable
     *
     * @test
     */
    public function execute()
    {
        $this->loop(function () {
            $kernel = new Kernel();

            $action = $kernel->execute(function ($value) {
                return $value * 2;
            }, [ 21 ]);

            self::assertAction($action);
            self::assertEquals(42, yield $action);
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
            $kernel = new Kernel();

            $action = $kernel->execute(function ($value) {
                yield new Delayed(10);

                return $value * 2;
            }, [ 21 ]);

            self::assertAction($action);
            self::assertEquals(42, yield $action);
        });
    }
}
