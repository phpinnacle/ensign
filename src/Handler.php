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
     * @var Arguments
     */
    private $arguments;

    /**
     * @param callable  $callable
     * @param Arguments $arguments
     */
    public function __construct(callable $callable, Arguments $arguments = null)
    {
        $this->callable  = $callable;
        $this->arguments = $arguments ?: Arguments::empty();
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
        $callable  = $this->callable;
        $arguments = (new Arguments($arguments))->inject($this->arguments);

        return $callable(...$arguments);
    }
}
