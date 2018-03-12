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

final class StaticDispatcher implements Dispatcher
{
    private static $instance;
    private $handlers;
    private $dispatcher;

    private function __construct()
    {
        $this->handlers   = new HandlerMap();
        $this->dispatcher = new SignalDispatcher($this->handlers);
    }

    /**
     * @return self
     */
    public static function instance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param string   $signal
     * @param callable $action
     *
     * @return void
     */
    public function register(string $signal, callable $action): void
    {
        $this->handlers->register($signal, $action);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(string $signal, ...$arguments): Task
    {
        return $this->dispatcher->dispatch($signal, ...$arguments);
    }
}
