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

final class Task implements Promise
{
    /**
     * @var int
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
     * @param int     $id
     * @param Promise $promise
     * @param Token   $token
     */
    public function __construct(int $id, Promise $promise, Token $token)
    {
        $this->id      = $id;
        $this->promise = $promise;
        $this->token   = $token;
    }

    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id;
    }

    /**
     * @param string $reason
     *
     * @return Task
     */
    public function cancel(string $reason = ''): self
    {
        $this->token->cancel($this->id, $reason);

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

        $watcher = Loop::delay($timeout, function () use ($deferred) {
            $deferred->fail(new Exception\TaskTimeout($this->id));
        });
        Loop::unreference($watcher);

        $promise = $this->promise;
        $promise->onResolve(function () use ($deferred, $watcher) {
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
