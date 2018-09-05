<?php

use PHPinnacle\Ensign\Dispatcher;
use PHPinnacle\Ensign\Processor;
use PHPinnacle\Ensign\Executor;

require __DIR__ . '/../vendor/autoload.php';

if (!function_exists('\Amp\ParallelFunctions\parallel')) {
    echo 'Please install amphp/parallel-functions for this example.' . \PHP_EOL;
    exit(1);
}

Amp\Loop::run(function () {
    $executor   = new Executor\ParallelExecutor();
    $dispatcher = new Dispatcher(new Processor($executor));
    $dispatcher->register('load', function ($url) {
        echo "Start getting: {$url}" . \PHP_EOL;

        file_get_contents($url);

        echo "Done!" . \PHP_EOL;
    });

    $actionOne = $dispatcher->dispatch('load', 'http://php.net');
    $actionTwo = $dispatcher->dispatch('load', 'https://github.com');
    $actionThree = $dispatcher->dispatch('load', 'https://amphp.org');

    yield [$actionOne, $actionTwo, $actionThree];
    yield $executor->shutdown();
});
