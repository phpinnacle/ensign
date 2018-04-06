<?php
/**
 * This file is part of PHPinnacle/Ensign.
 *
 * (c) PHPinnacle Team <dev@phpinnacle.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPinnacle\Ensign\Tests\Resolver;

use PHPinnacle\Ensign\Arguments;
use PHPinnacle\Ensign\Dispatcher;
use PHPinnacle\Ensign\HandlerRegistry;
use PHPinnacle\Ensign\Resolver;
use PHPinnacle\Ensign\SignalDispatcher;
use PHPinnacle\Ensign\Tests\EnsignTest;
use Psr\Container\ContainerInterface;

class ContainerResolverTest extends EnsignTest
{
    /**
     * @test
     *
     * Test that Resolver try resolve arguments for callable
     */
    public function testResolve()
    {
        $container = self::createMock(ContainerInterface::class);
        $container
            ->method('has', Dispatcher::class)
            ->willReturn(true)
        ;
        $container
            ->method('get', Dispatcher::class)
            ->willReturn($dispatcher = new SignalDispatcher())
        ;

        $resolver = new Resolver\ContainerResolver($container);

        $arguments = $resolver->resolve(function (Dispatcher $dispatcher, int $value) {
            return [$dispatcher, $value];
        });

        self::assertInstanceOf(Arguments::class, $arguments);
        self::assertEquals([$dispatcher], iterator_to_array($arguments));
    }
}
