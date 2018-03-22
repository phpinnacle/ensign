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

use PHPinnacle\Ensign\Signal;

class SignalTest extends EnsignTest
{
    /**
     * Test that Signal can be created
     *
     * @test
     */
    public function create()
    {
        $signal = new Signal('signal');

        self::assertEquals('signal', (string) $signal);
        self::assertEquals('signal', $signal->name());
        self::assertEquals([], $signal->arguments());
        self::assertCount(0, $signal);
        self::assertEquals([], \iterator_to_array($signal));

        $event  = new Stub\SimpleEvent();
        $signal = Signal::create($event);

        self::assertEquals(Stub\SimpleEvent::class, $signal->name());
        self::assertEquals([$event], $signal->arguments());
        self::assertCount(1, $signal);
        self::assertEquals([$event], \iterator_to_array($signal));
    }
}
