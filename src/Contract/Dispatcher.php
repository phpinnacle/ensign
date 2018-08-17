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

namespace PHPinnacle\Ensign\Contract;

use PHPinnacle\Ensign\Action;

interface Dispatcher
{
    /**
     * @param string   $signal
     * @param callable $handler
     *
     * @return self
     */
    public function register(string $signal, callable $handler): self;

    /**
     * @param mixed       $signal
     * @param mixed    ...$arguments
     *
     * @return Action
     */
    public function dispatch($signal, ...$arguments): Action;
}
