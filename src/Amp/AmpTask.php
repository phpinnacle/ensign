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

use Amp\Promise;
use PHPinnacle\Ensign\Task;

final class AmpTask implements Promise, Task
{
    /**
     * @var Promise
     */
    private $promise;

    /**
     * @var AmpToken
     */
    private $token;

    /**
     * @param Promise   $promise
     * @param AmpToken $token
     */
    public function __construct(Promise $promise, AmpToken $token)
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

    /**
     * {@inheritdoc}
     */
    public function then(callable $then): void
    {
        $this->onResolve($then);
    }
}
