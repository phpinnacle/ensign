<?php

use PHPinnacle\Ensign\SignalDispatcher;

require __DIR__ . '/../vendor/autoload.php';

class Ping
{
    public $times;

    public function __construct(int $times)
    {
        $this->times = $times;
    }
}

class Pong extends Ping
{
}

class Stop
{
}

Amp\Loop::run(function () {
    $dispatcher = SignalDispatcher::amp();
    $dispatcher
        ->register(Ping::class, function (Ping $cmd) {
            if ($cmd->times > 0) {
                $cmd->times--;

                echo 'Ping!' . \PHP_EOL;

                yield new Pong($cmd->times);
            } else {
                yield new Stop();
            }
        })
        ->register(Pong::class, function (Pong $cmd) {
            echo 'Pong!' . \PHP_EOL;

            yield new Ping($cmd->times);
        })
        ->register(Stop::class, function () {
            echo 'Stop!' . \PHP_EOL;
        })
    ;

    yield $dispatcher->dispatch(new Ping(\rand(2, 10)));
});
