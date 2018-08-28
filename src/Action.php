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

use Amp\Deferred;
use Amp\Loop;
use Amp\Promise;
use PHPinnacle\Identity\UUID;

final class Action implements Promise
{
    /**
     * @var UUID
     */
    private $id;

    /**
     * @var Promise
     */
    private $promise;

    /**
     * @var Token
     */
    private $token;

    /**
     * @param UUID    $id
     * @param Promise $promise
     * @param Token   $token
     */
    public function __construct(UUID $id, Promise $promise, Token $token)
    {
        $this->id      = $id;
        $this->promise = $promise;
        $this->token   = $token;
    }

    /**
     * @return UUID
     */
    public function id(): UUID
    {
        return $this->id;
    }

    /**
     * @param string $reason
     *
     * @return Action
     */
    public function cancel(string $reason = null): self
    {
        $this->token->cancel($reason);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function onResolve(callable $onResolved)
    {
        $this->token->guard();
        $this->promise->onResolve($onResolved);
    }
}
