<?php

use PHPinnacle\Ensign\HandlerMap;
use PHPinnacle\Ensign\SignalDispatcher;

require __DIR__ . '/../vendor/autoload.php';

Amp\Loop::run(function () {
    $handlers = new HandlerMap();
    $handlers->register('emit', function (string $string, int $num, int $delay) {
        for ($i = 0; $i < $num; $i++) {
            echo $string;

            yield new Amp\Delayed($delay);
        }

        return $num;
    });

    $dispatcher = new SignalDispatcher($handlers);

    $taskOne = $dispatcher->dispatch('emit', '+', 10, 100);
    $taskTwo = $dispatcher->dispatch('emit', '-', 5, 100);

    [$resultOne, $resultTwo] = yield [$taskOne, $taskTwo];

    echo \PHP_EOL;
    echo \sprintf('Task one done %d times.' . \PHP_EOL, $resultOne);
    echo \sprintf('Task two done %d times.' . \PHP_EOL, $resultTwo);
});
