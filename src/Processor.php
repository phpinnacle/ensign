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

use Amp;
use Amp\Promise;
use Amp\Success;

final class Processor
{
    /**
     * @var Executor
     */
    private $executor;

    /**
     * @var callable
     */
    private $resolver;

    /**
     * @var callable[]
     */
    private $interruptions = [];

    /**
     * @param Executor $executor
     */
    public function __construct(Executor $executor = null)
    {
        $this->executor = $executor ?: new Executor\SimpleExecutor;
        $this->resolver = function (string $interrupt, array $arguments) {
            if (!isset($this->interruptions[$interrupt])) {
                throw new Exception\UnknownInterrupt($interrupt);
            }

            return $this->execute($this->interruptions[$interrupt], $arguments);
        };
    }

    /**
     * @param string   $interrupt
     * @param callable $handler
     *
     * @return void
     */
    public function interrupt(string $interrupt, callable $handler): void
    {
        $this->interruptions[$interrupt] = $handler;
    }

    /**
     * @param callable $handler
     * @param array    $arguments
     *
     * @return Promise
     */
    public function execute(callable $handler, array $arguments): Promise
    {
        return Amp\call(function () use ($handler, $arguments) {
            $result = $this->executor->execute($handler, $arguments);

            if ($result instanceof \Generator) {
                return new Subroutine($result, $this->resolver);
            }

            return $result;
        });
    }
}
