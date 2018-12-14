<?php
/**
 * This file is part of PHPinnacle/Pinnacle.
 *
 * (c) PHPinnacle Team <dev@phpinnacle.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace PHPinnacle\Ensign\Wrapper;

use PHPinnacle\Ensign\HandlerWrapper;
use Psr\Container\ContainerInterface;

final class ContainerWrapper implements HandlerWrapper
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function wrap(callable $handler): callable
    {
        $parameters = $this->getParameters($handler);
        $resolved = [];

        foreach ($parameters as $i => $parameter) {
            if (!$class = $parameter->getClass()) {
                continue;
            }

            $instance = $this->resolveArgument($class->getName());

            if (\is_object($instance) && $class->isInstance($instance)) {
                $resolved[$i] = $instance;
            }
        }

        return function (...$arguments) use ($handler, $resolved) {
            return $handler(...array_replace($arguments, $resolved));
        };
    }

    /**
     * @param callable $handler
     *
     * @return \ReflectionParameter[]
     * @throws \ReflectionException
     */
    private function getParameters(callable $handler): array
    {
        return (new \ReflectionMethod(\Closure::fromCallable($handler), '__invoke'))->getParameters();
    }

    /**
     * @param string $class
     *
     * @return mixed
     */
    private function resolveArgument(string $class)
    {
        return $this->container->has($class) ? $this->container->get($class) : null;
    }
}
