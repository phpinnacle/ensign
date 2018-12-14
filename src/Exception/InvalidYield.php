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

use Amp\Promise;

final class InvalidYield extends EnsignException
{
    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        parent::__construct(\sprintf(
            "Unexpected yield; Expected an signal, callable, instance of %s or an array of such instances. Got: %s",
            Promise::class,
            \is_object($value) ? \get_class($value) : \gettype($value)
        ));
    }
}
