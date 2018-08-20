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
use Amp\Success;
use PHPinnacle\Ensign\Action;
use PHPinnacle\Ensign\Tests\EnsignTest;
use PHPinnacle\Ensign\Token;
use PHPinnacle\Identity\UUID;

class ActionTest extends EnsignTest
{
    /**
     * Test that Action can be canceled
     *
     * @test
     * @expectedException \PHPinnacle\Ensign\Exception\ActionCanceled
     */
    public function cancel()
    {
        self::loop(function () {
            $id   = UUID::random();
            $action = new Action($id, new Success(), new Token($id));
            $action->cancel();

            yield $action;
        });
    }

    /**
     * Test that Action throw exception when timeout reached
     *
     * @test
     * @expectedException \PHPinnacle\Ensign\Exception\ActionTimeout
     */
    public function timeout()
    {
        self::loop(function () {
            $id   = UUID::random();
            $action = new Action($id, new Delayed(100, true), new Token($id));
            $action->timeout(10);

            yield $action;
        });
    }

    /**
     * Test that Action can has timeout
     *
     * @test
     */
    public function fast()
    {
        self::loop(function () {
            $id   = UUID::random();
            $action = new Action($id, new Success(true), new Token($id));
            $action->timeout(100);

            self::assertTrue(yield $action);
        });
    }
}
