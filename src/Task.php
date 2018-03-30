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

use Amp\Promise;

class Task implements Promise
{
    /**
     * @var Promise
     */
    private $promise;

    /**
     * @var TaskToken
     */
    private $token;

    /**
     * @param Promise   $promise
     * @param TaskToken $token
     */
    public function __construct(Promise $promise, TaskToken $token)
    {
        $this->promise = $promise;
        $this->token   = $token;
    }

    /**
     * @return void
     */
    public function cancel(): void
    {
        $this->token->cancel();
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
