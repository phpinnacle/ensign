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

use PHPinnacle\Ensign\SignalDispatcher;
use PHPinnacle\Ensign\Dispatcher;
use PHPinnacle\Ensign\Task;

final class StaticDispatcher implements Dispatcher
{
    private static $instance;
    private $dispatcher;

    private function __construct()
    {
        $this->dispatcher = SignalDispatcher::amp();
    }

    public static function instance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function register(string $signal, callable $action): void
    {
        $this->dispatcher->register($signal, $action);
    }

    public function dispatch($signal, ...$arguments): Task
    {
        return $this->dispatcher->dispatch($signal, ...$arguments);
    }
}

/**
 * @param string   $signal
 * @param callable $handler
 */
function ensign_signal(string $signal, callable $handler): void
{
    StaticDispatcher::instance()->register($signal, $handler);
}

/**
 * @param string    $signal
 * @param mixed  ...$arguments
 *
 * @return Task
 */
function ensign_dispatch(string $signal, ...$arguments): Task
{
    return StaticDispatcher::instance()->dispatch($signal, ...$arguments);
}

/**
 * @param array      $signals
 * @param Dispatcher $dispatcher
 *
 * @throws \Amp\Loop\UnsupportedFeatureException
 */
function ensign_pcntl_signals(array $signals, Dispatcher $dispatcher = null): void
{
    $dispatcher = $dispatcher ?: StaticDispatcher::instance();

    foreach ($signals as $signal) {
        // OS signal support check
        if (!$sigNo = \constant($signal)) {
            continue;
        }

        $handler = function (string $watcherId, int $sigNo, $sigInfo = null) use ($dispatcher, $signal) {
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
