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

final class ActionTimeout extends EnsignException
{
    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        parent::__construct(sprintf('Action "%s" timed out.', $id));
    }
}
