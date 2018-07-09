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

use PHPinnacle\Ensign\Dispatcher;
use PHPinnacle\Ensign\Handler;
use PHPinnacle\Ensign\Resolver;
use PHPinnacle\Ensign\Tests\EnsignTest;
use Psr\Container\ContainerInterface;

class ContainerResolverTest extends EnsignTest
{
    /**
     * Test that Resolver try resolve arguments for callable
     *
     * @test
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
            ->willReturn($dispatcher = Dispatcher::instance())
        ;

        $resolver = new Resolver\ContainerResolver($container);
        $closure  = function (Dispatcher $dispatcher, int $value) {
            return [$dispatcher, $value];
        };

        $arguments = $resolver->resolve(Handler::recognize($closure));

        self::assertArray($arguments);
        self::assertEquals([$dispatcher], $arguments);
    }
}
