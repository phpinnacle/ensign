<?php

use Amp\Delayed;
use PHPinnacle\Ensign\Dispatcher;
use PHPinnacle\Ensign\Kernel;

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
    private $kernel;
    private $listeners = [];

    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    public function listen(string $signal, callable $listener): self
    {
        $this->listeners[$signal][] = $listener;

        return $this;
    }

    public function __invoke(Publish $message)
    {
        $actions = \array_map(function ($listener) use ($message) {
            return $this->kernel->execute($listener, $message->arguments);
        }, $this->listeners[$message->signal] ?? []);

        yield $actions;
    }
}

Amp\Loop::run(function () {
    $kernel = new Kernel();
    $publisher = new Publisher($kernel);
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

    $dispatcher = new Dispatcher($kernel);
    $dispatcher
        ->register(Publish::class, $publisher)
    ;

    yield $dispatcher->dispatch(new Publish('print', 10));
});
