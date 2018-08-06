<?php

use Amp\Delayed;
use PHPinnacle\Ensign\Dispatcher;

require __DIR__ . '/../vendor/autoload.php';

Amp\Loop::run(function () {
    $dispatcher = new Dispatcher();
    $dispatcher
        ->register('emit', function (string $string, int $num, int $delay = 100) {
            for ($i = 0; $i < $num; $i++) {
                echo $string;

                yield new Delayed($delay);
            }

            return $num;
        })
    ;

    $times   = \rand(5, 10);
    $taskOne = $dispatcher->dispatch('emit', '-', $times, 100);
    $taskTwo = $dispatcher->dispatch('emit', '+', $times + \rand(5, 10), 100);

    [$resultOne, $resultTwo] = yield [$taskOne, $taskTwo];

    echo \PHP_EOL;
    echo \sprintf('Task one done %d times.' . \PHP_EOL, $resultOne);
    echo \sprintf('Task two done %d times.' . \PHP_EOL, $resultTwo);
});
