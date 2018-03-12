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

class ArgumentsTest extends EnsignTest
{
    /**
     * @test
     *
     * Test that Arguments can be created with no data
     */
    public function empty()
    {
        $arguments = Arguments::empty();

        self::assertInstanceOf(Arguments::class, $arguments);
        self::assertEquals([], iterator_to_array($arguments));
    }

    /**
     * @test
     *
     * Test that Arguments can be created from array
     */
    public function list()
    {
        $list = [
            1,
            'string',
            true,
            fopen('php://memory', 'r'),
            new \stdClass(),
            ['key' => 'value'],
        ];

        $arguments = Arguments::list($list);

        self::assertInstanceOf(Arguments::class, $arguments);
        self::assertEquals($list, iterator_to_array($arguments));
    }

    /**
     * @test
     *
     * Test that Arguments can be injected with others
     */
    public function inject()
    {
        $arguments = Arguments::list(['one', 'two', 'three']);
        $arguments = $arguments->inject(Arguments::list([1 => 'replace']));

        self::assertEquals(['one', 'replace', 'two', 'three'], iterator_to_array($arguments));
    }

    /**
     * @test
     *
     * Test that Arguments can counted
     */
    public function count()
    {
        $arguments = Arguments::list(['one', 'two', 'three']);

        self::assertCount(3, $arguments);
    }
}
