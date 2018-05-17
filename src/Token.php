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

namespace PHPinnacle\Ensign;

use Amp\CancellationTokenSource;

final class Token
{
    /**
     * @var CancellationTokenSource
     */
    private $source;

    /**
     * @var \Amp\CancellationToken
     */
    private $token;

    /**
     * @var int
     */
    private $task;

    /**
     * @var string
     */
    private $reason;

    /**
     * TaskToken constructor.
     */
    public function __construct()
    {
        $this->source = new CancellationTokenSource();
        $this->token  = $this->source->getToken();
    }

    /**
     * @param int    $task
     * @param string $reason
     * @return void
     */
    public function cancel(int $task, string $reason): void
    {
        $this->task   = $task;
        $this->reason = $reason;

        $this->source->cancel();
    }

    /**
     * @return void
     */
    public function guard(): void
    {
        if ($this->token->isRequested()) {
            throw new Exception\TaskCanceled($this->task, $this->reason);
        };
    }
}
