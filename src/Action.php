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
     * @param int $timeout
     *
     * @return self
     */
    public function timeout(int $timeout): self
    {
        $deferred = new Deferred;
        $resolved = false;

        $watcher = Loop::delay($timeout, function () use ($deferred, &$resolved) {
            if ($resolved) {
                return;
            }

            $resolved = true;

            $deferred->fail(new Exception\ActionTimeout((string) $this->id));
        });

        $promise = $this->promise;
        $promise->onResolve(function () use ($deferred, $watcher, &$resolved) {
            if ($resolved) {
                return;
            }

            $resolved = true;

            Loop::cancel($watcher);

            $deferred->resolve($this->promise);
        });

        $this->promise = $deferred->promise();

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
