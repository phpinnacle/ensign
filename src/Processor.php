<?php
/**
 * This file is part of PHPinnacle/Ensign.
 *
 * (c) PHPinnacle Team <dev@phpinnacle.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPinnacle\Ensign;

use Amp\Coroutine;
use Amp\LazyPromise;
use PHPinnacle\Identity\UUID;

final class Processor
{
    /**
     * @var Executor
     */
    private $executor;

    /**
     * @var callable[]
     */
    private $interruptions = [];

    /**
     * @param Executor $executor
     */
    public function __construct(Executor $executor = null)
    {
        $this->executor = $executor ?: new Executor\SimpleExecutor();
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
     * @return Action
     */
    public function execute(callable $handler, array $arguments): Action
    {
        $id = UUID::random();

        $token   = new Token($id);
        $promise = new LazyPromise(function () use ($handler, $arguments, $token) {
            return $this->adapt($this->executor->execute($handler, $arguments), $token);
        });

        return new Action($id, $promise, $token);
    }

    /**
     * @param mixed $value
     * @param Token $token
     *
     * @return mixed
     */
    private function adapt($value, Token $token)
    {
        return $value instanceof \Generator ? new Coroutine($this->recoil($value, $token)) : $value;
    }

    /**
     * @param \Generator $generator
     * @param Token      $token
     *
     * @return mixed
     */
    private function recoil(\Generator $generator, Token $token)
    {
        $step = 1;

        while ($generator->valid()) {
            $token->guard();

            try {
                $value = $this->intercept($generator->key(), $generator->current());

                $generator->send(yield $this->adapt($value, $token));
            } catch (\Exception $error) {
                $this->throw($generator, $error, $step);
            } finally {
                $step++;
            }
        }

        return $this->adapt($generator->getReturn(), $token);
    }

    /**
     * @param \Generator $generator
     * @param \Exception $error
     * @param int        $step
     */
    private function throw(\Generator $generator, \Exception $error, int $step)
    {
        try {
            $generator->throw($error);
        } catch (\Throwable $error) {
            throw new Exception\BadActionCall($step, $error);
        }
    }

    /**
     * @param int|string $key
     * @param mixed      $value
     *
     * @return mixed
     */
    private function intercept($key, $value)
    {
        $interrupt = \is_string($key) ? $key : $value;

        if (\is_object($interrupt)) {
            $interrupt = \get_class($interrupt);
        }

        if (!\is_string($interrupt) || !isset($this->interruptions[$interrupt])) {
            return $value;
        }

        $value = \is_array($value) ? $value : [$value];

        return $this->executor->execute($this->interruptions[$interrupt], $value);
    }
}
