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

/**
 * @param mixed $value
 *
 * @return bool
 */
function is_promise($value): bool
{
    if (\is_array($value) && \count($value) === \count(\array_filter($value, '\is_promise'))) {
        return true;
    }

    return $value instanceof \Amp\Promise;
}

/**
 * @param mixed $value
 *
 * @return \Amp\Promise
 */
function success($value = null): \Amp\Promise
{
    return new \Amp\Success($value);
}

/**
 * @param \Throwable $error
 *
 * @return \Amp\Promise
 */
function failure(\Throwable $error): \Amp\Promise
{
    return new \Amp\Failure($error);
}

/**
 * @param \Generator $generator
 *
 * @return \Amp\Coroutine
 */
function coroutine(\Generator $generator): \Amp\Coroutine
{
    return new \Amp\Coroutine($generator);
}

/**
 * @param mixed ...$arguments
 *
 * @return \Amp\Promise
 */
function promise(...$arguments): \Amp\Promise
{
    if (empty($arguments)) {
        return \success();
    }

    if (\is_promise($arguments)) {
        return \Amp\Promise\all($arguments);
    }

    if (\is_callable($arguments[0])) {
        $first = \array_shift($arguments);

        return \Amp\call($first, ...$arguments);
    }

    return isset($arguments[1]) ? \success($arguments) : \success($arguments[0]);
}

/**
 * @param mixed ...$arguments
 *
 * @return \Amp\Promise
 */
function future(...$arguments): \Amp\Promise
{
    return new \Amp\LazyPromise(function () use ($arguments) {
        return \promise(...$arguments);
    });
}

/**
 * @param \Amp\Promise $promise
 * @param int          $time
 *
 * @return \Amp\Promise
 */
function timeout(\Amp\Promise $promise, int $time): \Amp\Promise
{
    return \Amp\Promise\timeout($promise, $time);
}
