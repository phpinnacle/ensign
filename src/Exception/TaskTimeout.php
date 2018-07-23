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

final class TaskTimeout extends EnsignException
{
    /**
     * @param int $task
     */
    public function __construct(int $task)
    {
        parent::__construct(sprintf('Task %d timed out.', $task));
    }
}
