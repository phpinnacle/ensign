<?php

use PHPinnacle\Ensign\HandlerMap;
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

Amp\Loop::run(function () {
    $handlers = new HandlerMap();
    $handlers->register('emit', function (int $num) {
        yield new SimpleEvent($num - 1);
        yield new Amp\Delayed(1000); // Just do some heavy calculations

        yield new SimpleEvent($num * $num);
        yield new Amp\Delayed(1000); // Do more work

        $event = new SimpleEvent($num + $num);

        yield $event;

        return $num;
    });

    $dispatcher = new SignalDispatcher($handlers);

    $task = $dispatcher->dispatch('emit', 10);
    $task->onResolve(function (\Throwable $error = null, $data) {
        echo \sprintf('Task resolved with value: %d at %s' . \PHP_EOL, $data, \microtime(true));
    });

    $eventOne = $task->wait(SimpleEvent::class);
    $eventOne->onResolve(function (\Throwable $error = null, SimpleEvent $event = null) {
        echo \sprintf('Event one resolved with value: %d at %s' . \PHP_EOL, $event->num, \microtime(true));
    });

    $eventTwo = $task->wait(SimpleEvent::class);
    $eventTwo->onResolve(function (\Throwable $error = null, SimpleEvent $event = null) {
        echo \sprintf('Event two resolved with value: %d at %s' . \PHP_EOL, $event->num, \microtime(true));
    });

    $eventThree = $task->wait(SimpleEvent::class);
    $eventThree->onResolve(function (\Throwable $error = null, SimpleEvent $event = null) {
        echo \sprintf('Event three resolved with value: %d at %s' . \PHP_EOL, $event->num, \microtime(true));
    });
});
