<?php

use Amp\Delayed;
use PHPinnacle\Ensign\Dispatcher;
use PHPinnacle\Ensign\DispatcherBuilder;

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
    $builder = new DispatcherBuilder;
    $builder
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

    $dispatcher = $builder->build();

    $data = yield $dispatcher->dispatch(new SimpleCommand(10));

    echo \sprintf('Action resolved with value: %d at %s' . \PHP_EOL, $data, \microtime(true));
});
