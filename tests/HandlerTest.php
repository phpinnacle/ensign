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

use PHPinnacle\Ensign\Arguments;
use PHPinnacle\Ensign\Handler;

class HandlerTest extends EnsignTest
{
    /**
     * Test that Handler can be invoked
     *
     * @test
     */
    public function invoke()
    {
        $handler = new Handler(function ($value, $num = 1) {
            return $value + $num;
        });

        $resultOne = $handler(1, 2);
        $resultTwo = $handler(2);

        self::assertEquals(3, $resultOne);
        self::assertEquals(3, $resultTwo);
    }

    /**
     * Test that Handler can be invoked
     *
     * @test
     * @expectedException \Exception
     * @expectedExceptionCode 1
     * @expectedExceptionMessage Test
     */
    public function error()
    {
        $handler = Handler::error(new \Exception('Test', 1));
        $handler();
    }

    /**
     * Test that Handler can be invoked
     *
     * @test
     * @expectedException \PHPinnacle\Ensign\Exception\UnknownSignal
     * @expectedExceptionMessage Signal "test" has no handler registered
     */
    public function unknown()
    {
        $handler = Handler::unknown('test');
        $handler();
    }
}
