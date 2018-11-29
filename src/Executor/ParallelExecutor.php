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

namespace PHPinnacle\Ensign\Executor;

use Amp\Parallel\Worker\DefaultPool;
use Amp\Parallel\Worker\Pool;
use Amp\ParallelFunctions;
use Amp\Promise;
use PHPinnacle\Ensign\Executor;

final class ParallelExecutor implements Executor
{
    /**
     * @var Pool
     */
    private $pool;

    /**
     * @param Pool $pool
     */
    public function __construct(Pool $pool = null)
    {
        $this->pool = $pool ?: new DefaultPool;
    }

    /**
     * @return Promise
     */
    public function shutdown(): Promise
    {
        return $this->pool->shutdown();
    }

    /**
     * {@inheritdoc}
     */
    public function execute(callable $handler, array $arguments)
    {
        $parallel = ParallelFunctions\parallel($handler, $this->pool);

        return $parallel(...$arguments);
    }
}
