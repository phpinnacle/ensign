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
use PHPinnacle\Identity\UUID;

final class Token
{
    /**
     * @var UUID
     */
    private $id;

    /**
     * @var CancellationTokenSource
     */
    private $source;

    /**
     * @var string
     */
    private $reason;

    /**
     * @param UUID $id
     */
    public function __construct(UUID $id)
    {
        $this->id     = $id;
        $this->source = new CancellationTokenSource();
    }

    /**
     * @param string $reason
     * @return void
     */
    public function cancel(string $reason = null): void
    {
        $this->reason = $reason;

        $this->source->cancel();
    }

    /**
     * @return void
     */
    public function guard(): void
    {
        if ($this->source->getToken()->isRequested()) {
            throw new Exception\ActionCanceled((string) $this->id, $this->reason);
        }
    }
}
