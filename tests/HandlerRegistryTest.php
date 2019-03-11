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

use PHPinnacle\Ensign\Exception;
use PHPinnacle\Ensign\HandlerRegistry;

class HandlerRegistryTest extends EnsignTest
{
    /**
     * Test that HandlerRegistry can store and access handlers
     */
    public function testAddAndGet()
    {
        $registry = new HandlerRegistry;

        self::assertFalse($registry->has('signal'));

        $handler = function () {};

        self::assertSame($registry, $registry->add('signal', $handler));
        self::assertTrue($registry->has('signal'));
        self::assertSame($handler, $registry->get('signal'));
    }

    /**
     * Test that HandlerRegistry return default handler
     */
    public function testUnknownHandler()
    {
        $registry = new HandlerRegistry;

        self::assertFalse($registry->has('signal'));
        self::assertIsCallable($handler = $registry->get('signal'));

        self::expectException(Exception\UnknownSignal::class);

        $handler();
    }
}
