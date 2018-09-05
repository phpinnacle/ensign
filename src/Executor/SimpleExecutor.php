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

namespace PHPinnacle\Ensign\Executor;

use PHPinnacle\Ensign\Executor;

final class SimpleExecutor implements Executor
{
    /**
     * {@inheritdoc}
     */
    public function execute(callable $handler, array $arguments)
    {
        return $handler(...$arguments);
    }
}