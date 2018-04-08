<?php

use Amp\Delayed;
use PHPinnacle\Ensign\HandlerRegistry;
use PHPinnacle\Ensign\SignalDispatcher;

require __DIR__ . '/../vendor/autoload.php';

class SimpleEvent
{
    public $num;

    public function __construct(int $num)
    {
        $this->num = $num;
    }
}

class SimpleCommand
{
    public $num;
    public $delay;

    public function __construct(int $num, int $delay = 100)
    {
        $this->num   = $num;
        $this->delay = $delay;
    }
}

Amp\Loop::run(function () {
    $dispatcher = new SignalDispatcher();
    $dispatcher
        ->register(SimpleCommand::class, function (SimpleCommand $cmd) {
            yield new Delayed($cmd->delay); // Just do some heavy calculations
            yield SimpleEvent::class => new SimpleEvent($cmd->num + $cmd->num);

            yield new Delayed($cmd->delay); // Do more work
            yield new SimpleEvent($cmd->num * $cmd->num);

            return $cmd->num;
        })
        ->register(SimpleEvent::class, function (SimpleEvent $event) {
            echo \sprintf('Signal dispatched with value: %d at %s' . \PHP_EOL, $event->num, \microtime(true));
        })
    ;

    $task = $dispatcher->dispatch(new SimpleCommand(10));
    $data = yield $task;

    echo \sprintf('Task resolved with value: %d at %s' . \PHP_EOL, $data, \microtime(true));
});
