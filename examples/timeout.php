<?php

use Amp\Delayed;
use PHPinnacle\Ensign\Dispatcher;
use PHPinnacle\Ensign\Exception\TaskTimeout;

require __DIR__ . '/../vendor/autoload.php';

Amp\Loop::run(function () {
    $dispatcher = new Dispatcher();
    $dispatcher
        ->register('endless', function (string $sign) {
            while (true) {
                yield new Delayed(100);

                echo $sign;
            }
        })
    ;

    try {
        yield $dispatcher->dispatch('endless', '.')->timeout(500);
    } catch (TaskTimeout $exception) {
        exit(\PHP_EOL . $exception->getMessage());
    }
});
