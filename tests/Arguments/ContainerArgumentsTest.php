<?php
/**
 * This file is part of PHPinnacle/Ensign.
 *
 * (c) PHPinnacle Team <dev@phpinnacle.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPinnacle\Ensign\Tests\Arguments;

use PHPinnacle\Ensign\Dispatcher;
use PHPinnacle\Ensign\Arguments;
use PHPinnacle\Ensign\Tests\EnsignTest;
use Psr\Container\ContainerInterface;

class ContainerArgumentsTest extends EnsignTest
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
            ->willReturn($dispatcher = Dispatcher::amp())
        ;

        $resolver = new Arguments\ContainerArguments($container);

        $arguments = $resolver->resolve(function (Dispatcher $dispatcher, int $value) {
            return [$dispatcher, $value];
        });

        self::assertArray($arguments);
        self::assertEquals([$dispatcher], $arguments);
    }
}
