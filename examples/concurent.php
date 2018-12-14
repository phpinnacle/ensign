<?php

use Amp\Delayed;
use PHPinnacle\Ensign\DispatcherBuilder;

require __DIR__ . '/../vendor/autoload.php';

Amp\Loop::run(function () {
    $builder = new DispatcherBuilder;
    $builder
        ->register('emit', function (string $string, int $num, int $delay = 100) {
            for ($i = 0; $i < $num; $i++) {
                echo $string;

                yield new Delayed($delay);
            }

            return $num;
        })
    ;

    $dispatcher = $builder->build();

    $times   = \rand(5, 10);
    $actionOne = $dispatcher->dispatch('emit', '-', $times, 100);
    $actionTwo = $dispatcher->dispatch('emit', '+', $times + \rand(5, 10), 100);

    [$resultOne, $resultTwo] = yield [$actionOne, $actionTwo];

    echo \PHP_EOL;
    echo \sprintf('Action one done %d times.' . \PHP_EOL, $resultOne);
    echo \sprintf('Action two done %d times.' . \PHP_EOL, $resultTwo);
});
