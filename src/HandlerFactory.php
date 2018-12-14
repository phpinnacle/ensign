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

final class HandlerFactory
{
    /**
     * @var HandlerWrapper[]
     */
    private $wrappers = [];

    /**
     * @param HandlerWrapper $wrapper
     *
     * @return self
     */
    public function with(HandlerWrapper $wrapper): self
    {
        $this->wrappers[] = $wrapper;

        return $this;
    }

    /**
     * @param callable $handler
     *
     * @return callable
     */
    public function create(callable $handler): callable
    {
        foreach ($this->wrappers as $wrapper) {
            $handler = $wrapper->wrap($handler);
        }

        return $handler;
    }
}
