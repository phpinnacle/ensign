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

final class ActionCanceled extends EnsignException
{
    /**
     * @param string $id
     * @param string $reason
     */
    public function __construct(string $id, string $reason = null)
    {
        parent::__construct($reason ?: sprintf('Action "%s" was cancelled.', $id));
    }
}
