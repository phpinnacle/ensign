<?php

use Amp\Delayed;
use PHPinnacle\Ensign\Dispatcher;

require __DIR__ . '/../vendor/autoload.php';

Amp\Loop::run(function () {
    $dispatcher = Dispatcher::instance();
    $dispatcher
        ->register('endless', function (string $sign) {
            while (true) {
                yield new Delayed(100);

                echo $sign;
            }
        })
    ;

    try {
        $task = $dispatcher->dispatch('endless', '.');

        Amp\Loop::delay(500, function () use ($task) {
            $task->cancel();
        });

        yield $task;
    } catch (Exception $exception) {
        exit(\PHP_EOL . $exception->getMessage());
    }
});
