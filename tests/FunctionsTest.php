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

class FunctionsTest extends EnsignTest
{
    /**
     * Test that Dispatcher can dispatch proper signals
     *
     * @test
     */
    public function dispatchSignal()
    {
        self::loop(function () {
            ensign_signal('upper', function ($text) {
                return strtoupper($text);
            });

            self::assertAction($upperAction = ensign_dispatch('upper', 'test'));
            self::assertEquals('TEST', yield $upperAction);
        });
    }

    /**
     * Test acquire signals list
     *
     * @test
     */
    public function pcntlSignalList()
    {
        $exclude = ['SIGHUP', 'SIGINT'];
        $signals = pcntl_signal_list();

        $this->assertEquals($exclude, \array_diff($signals, pcntl_signal_list($exclude)));
    }
}
