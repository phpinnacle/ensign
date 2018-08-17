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

interface Processor
{
    /**
     * @param callable $handler
     * @param array    $arguments
     *
     * @return mixed
     */
    public function execute(callable $handler, array $arguments);
}
