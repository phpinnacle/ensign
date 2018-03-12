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

final class Task implements Promise, \IteratorAggregate
{
    /**
     * @var Promise
     */
    private $promise;

    /**
     * @var Channel
     */
    private $channel;

    /**
     * @param Promise $promise
     * @param Channel $channel
     */
    public function __construct(Promise $promise, Channel $channel)
    {
        $this->promise = $promise;
        $this->channel = $channel;
    }

    /**
     * @param string $signal
     *
     * @return Promise
     */
    public function wait(string $signal): Promise
    {
        return $this->channel->wait($signal);
    }

    /**
     * @return void
     */
    public function cancel(): void
    {
        $this->channel->close();
    }

    /**
     * {@inheritdoc}
     */
    public function onResolve(callable $onResolved)
    {
        $this->promise->onResolve($onResolved);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return $this->channel;
    }
}
