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
}
