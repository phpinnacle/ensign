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

final class HandlerRegistry
{
    /**
     * @var callable[]
     */
    private $handlers = [];

    /**
     * @param string   $signal
     * @param callable $handler
     *
     * @return self
     */
    public function add(string $signal, callable $handler): self
    {
        $this->handlers[$signal] = $handler;

        return $this;
    }

    /**
     * @param string $signal
     *
     * @return bool
     */
    public function has(string $signal): bool
    {
        return isset($this->handlers[$signal]);
    }

    /**
     * @param string $signal
     *
     * @return callable
     */
    public function get(string $signal): callable
    {
        return $this->handlers[$signal] ?? static function () use ($signal) {
            throw new Exception\UnknownSignal($signal);
        };
    }
}
