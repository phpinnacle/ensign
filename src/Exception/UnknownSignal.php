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

final class UnknownSignal extends EnsignException
{
    /**
     * @param string $signal
     */
    public function __construct(string $signal)
    {
        parent::__construct(sprintf('Signal "%s" has no handler registered.', $signal));
    }
}
