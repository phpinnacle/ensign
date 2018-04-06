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
     * @var Handler[]
     */
    private $handlers = [];

    /**
     * @param string   $name
     * @param callable $handler
     *
     * @return self
     */
    public function register(string $name, callable $handler): self
    {
        $this->handlers[$name] = $handler instanceof Handler ? $handler : new Handler($handler);

        return $this;
    }

    /**
     * @param string $name
     *
     * @return Handler|null
     */
    public function acquire(string $name): ?Handler
    {
        return $this->handlers[$name] ?? Handler::unknown($name);
    }
}
