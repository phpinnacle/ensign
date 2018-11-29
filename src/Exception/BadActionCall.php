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

final class BadActionCall extends EnsignException
{
    /**
     * @param int        $step
     * @param \Throwable $error
     */
    public function __construct(int $step, \Throwable $error)
    {
        parent::__construct(sprintf('Bad action call at step "%d".', $step), 0, $error);
    }
}
