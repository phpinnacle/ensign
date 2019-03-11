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

use PHPinnacle\Ensign\Tests\Stub\SimpleService;
use PHPinnacle\Ensign\Wrapper\ContainerWrapper;
use Psr\Container\ContainerInterface;

class ContainerWrapperTest extends EnsignTest
{
    /**
     * Test that ContainerWrapper resolve services through ContainerInterface
     */
    public function testResolveArguments()
    {
        $container = new class implements ContainerInterface {
            public function get($id)
            {
                if (!$this->has($id)) {
                    throw new \Exception('Service not found.');
                }

                return new SimpleService;
            }

            public function has($id)
            {
                return $id === SimpleService::class;
            }
        };

        $wrapper = new ContainerWrapper($container);
        $handler = function (int $arg, SimpleService $service) {
            return $service->test() + $arg;
        };

        self::assertNotSame($handler, $wrapped = $wrapper->wrap($handler));
        self::assertEquals(2, $wrapped(1));
    }
}
