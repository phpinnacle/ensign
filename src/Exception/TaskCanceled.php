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

final class TaskCanceled extends EnsignException
{
    /**
     * @param int    $task
     * @param string $reason
     */
    public function __construct(int $task, string $reason)
    {
        parent::__construct($reason ?: sprintf('Task %d was cancelled', $task));
    }
}
