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

namespace PHPinnacle\Ensign\Amp;

use Amp\CancellationTokenSource;

final class AmpToken
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
     * TaskToken constructor.
     */
    public function __construct()
    {
        $this->source = new CancellationTokenSource();
        $this->token  = $this->source->getToken();
    }

    /**
     * @return void
     */
    public function cancel(): void
    {
        $this->source->cancel();
    }

    /**
     * @return void
     */
    public function guard(): void
    {
        $this->token->throwIfRequested();
    }
}
