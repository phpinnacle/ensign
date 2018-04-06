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
    private $callable;

    /**
     * @param callable  $callable
     */
    public function __construct(callable $callable)
    {
        $this->callable  = $callable;
    }

    /**
     * @param \Exception $error
     *
     * @return self
     */
    public static function error(\Exception $error): self
    {
        return new self(function () use ($error) {
            throw $error;
        });
    }

    /**
     * @param string $signal
     *
     * @return self
     */
    public static function unknown(string $signal): self
    {
        return self::error(new Exception\UnknownSignal($signal));
    }

    /**
     * @param mixed ...$arguments
     *
     * @return mixed
     */
    public function __invoke(...$arguments)
    {
        $callable = $this->callable;

        return $callable(...$arguments);
    }
}
