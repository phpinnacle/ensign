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

namespace PHPinnacle\Ensign\Exception;

final class UnknownInterrupt extends EnsignException
{
    /**
     * @param string $interrupt
     */
    public function __construct(string $interrupt)
    {
        parent::__construct(sprintf('Interrupt "%s" has no handler registered.', $interrupt));
    }
}
