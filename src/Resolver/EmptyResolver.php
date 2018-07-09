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

namespace PHPinnacle\Ensign\Resolver;

use PHPinnacle\Ensign\Handler;
use PHPinnacle\Ensign\Resolver;

final class EmptyResolver implements Resolver
{
    /**
     * {@inheritdoc}
     */
    public function resolve(Handler $handler): array
    {
        return [];
    }
}
