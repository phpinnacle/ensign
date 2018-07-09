<?php
/**
 * This file is part of PHPinnacle/Ensign.
 *
 * (c) PHPinnacle Team <dev@phpinnacle.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace PHPinnacle\Ensign;

final class Handler
{
    /**
     * @var callable
     */
    private $action;

    /**
     * @var \ReflectionParameter[]
     */
    private $parameters;

    /**
     * @param callable $action
     * @param array    $parameters
     */
    public function __construct(callable $action, array $parameters = [])
    {
        $this->action     = $action;
        $this->parameters = $parameters;
    }

    /**
     * @param callable $action
     *
     * @return self
     */
    public static function recognize(callable $action): self
    {
        $reflection = new \ReflectionMethod(\Closure::fromCallable($action), '__invoke');

        return new self($action, $reflection->getParameters());
    }

    /**
     * @param \Throwable $error
     *
     * @return self
     */
    public static function error(\Throwable $error): self
    {
        return new self(function () use ($error) {
            throw $error;
        });
    }

    /**
     * @return \ReflectionParameter[]
     */
    public function parameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param mixed ...$arguments
     *
     * @return mixed
     */
    public function __invoke(...$arguments)
    {
        $action = $this->action;

        return $action(...$arguments);
    }
}
