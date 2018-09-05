<?php

use Amp\Delayed;
use PHPinnacle\Ensign\Dispatcher;
use PHPinnacle\Ensign\Processor;

require __DIR__ . '/../vendor/autoload.php';

class Publish
{
    public $signal;
    public $arguments;

    public function __construct(string $signal, ...$arguments)
    {
        $this->signal    = $signal;
        $this->arguments = $arguments;
    }
}

class Publisher
{
    private $processor;
    private $listeners = [];

    public function __construct(Processor $processor)
    {
        $this->processor = $processor;
    }

    public function listen(string $signal, callable $listener): self
    {
        $this->listeners[$signal][] = $listener;

        return $this;
    }

    public function __invoke(Publish $message)
    {
        $actions = \array_map(function ($listener) use ($message) {
            return $this->processor->execute($listener, $message->arguments);
        }, $this->listeners[$message->signal] ?? []);

        yield $actions;
    }
}

Amp\Loop::run(function () {
    $processor = new Processor();
    $publisher = new Publisher($processor);
    $publisher
        ->listen('print', function ($num) {
            for ($i = 0; $i < $num; $i++) {
                echo '-';

                yield new Delayed(100);
            }
        })
        ->listen('print', function ($num) {
            for ($i = 0; $i < $num; $i++) {
                echo '+';

                yield new Delayed(100);
            }
        })
        ->listen('print', function ($num) {
            for ($i = 0; $i < $num; $i++) {
                echo '*';

                yield new Delayed(50);
            }
        })
    ;

    $dispatcher = new Dispatcher($processor);
    $dispatcher
        ->register(Publish::class, $publisher)
    ;

    yield $dispatcher->dispatch(new Publish('print', 10));
});
