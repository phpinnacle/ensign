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

use PHPinnacle\Ensign\Dispatcher;
use PHPinnacle\Ensign\Action;

function __ensign_dispatcher(): Dispatcher
{
    static $dispatcher;

    return $dispatcher ?: $dispatcher = new Dispatcher();
}

/**
 * @param string   $signal
 * @param callable $handler
 */
function ensign_signal(string $signal, callable $handler): void
{
    __ensign_dispatcher()->register($signal, $handler);
}

/**
 * @param string    $signal
 * @param mixed  ...$arguments
 *
 * @return Action
 */
function ensign_dispatch(string $signal, ...$arguments): Action
{
    return __ensign_dispatcher()->dispatch($signal, ...$arguments);
}

/**
 * @param array      $signals
 * @param Dispatcher $dispatcher
 *
 * @throws \Amp\Loop\UnsupportedFeatureException
 */
function ensign_pcntl_signals(array $signals, Dispatcher $dispatcher = null): void
{
    $dispatcher = $dispatcher ?: __ensign_dispatcher();

    foreach ($signals as $signal) {
        // OS signal support check
        if (!$sigNo = \constant($signal)) {
            continue;
        }

        $handler = static function (string $watcherId, int $sigNo, $sigInfo = null) use ($dispatcher, $signal) {
            yield $dispatcher->dispatch($signal, $sigNo, $sigInfo, $watcherId);
        };

        \Amp\Loop::onSignal($sigNo, $handler);
    }
}

/**
 * @param array $exclude
 *
 * @return array
 */
function pcntl_signal_list(array $exclude = []): array
{
    return \array_diff([
        'SIGHUP',
        'SIGINT',
        'SIGQUIT',
        'SIGILL',
        'SIGTRAP',
        'SIGABRT',
        'SIGIOT',
        'SIGBUS',
        'SIGFPE',
        // 'SIGKILL',
        'SIGUSR1',
        'SIGSEGV',
        'SIGUSR2',
        'SIGPIPE',
        'SIGALRM',
        'SIGTERM',
        'SIGSTKFLT',
        'SIGCLD',
        'SIGCHLD',
        'SIGCONT',
        // 'SIGSTOP',
        'SIGTSTP',
        'SIGTTIN',
        'SIGTTOU',
        'SIGURG',
        'SIGXCPU',
        'SIGXFSZ',
        'SIGVTALRM',
        'SIGPROF',
        'SIGWINCH',
        'SIGPOLL',
        'SIGIO',
        'SIGPWR',
        'SIGSYS',
    ], $exclude);
}
