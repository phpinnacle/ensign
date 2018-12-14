<?php

use Amp\Delayed;
use PHPinnacle\Ensign\DispatcherBuilder;

require __DIR__ . '/../vendor/autoload.php';

Amp\Loop::run(function () {
    $builder = new DispatcherBuilder;
    $builder
        ->register('print', function ($num) {
            for ($i = 0; $i < $num; $i++) {
                echo '-';

                yield new Delayed(100);
            }
        })
        ->register('print', function ($num) {
            for ($i = 0; $i < $num; $i++) {
                echo '+';

                yield new Delayed(100);
            }
        })
        ->register('print', function ($num) {
            for ($i = 0; $i < $num; $i++) {
                echo '*';

                yield new Delayed(50);
            }
        })
    ;

    $dispatcher = $builder->build();

    yield $dispatcher->dispatch('print', 10);
});
