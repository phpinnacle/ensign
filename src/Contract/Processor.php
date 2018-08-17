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

interface Processor
{
    /**
     * @param string   $interrupt
     * @param callable $handler
     *
     * @return void
     */
    public function interrupt(string $interrupt, callable $handler): void;

    /**
     * @param callable $handler
     * @param array    $arguments
     *
     * @return Action
     */
    public function execute(callable $handler, array $arguments): Action;
}
