<?php

use PHPinnacle\Ensign\Dispatcher;
use PHPinnacle\Ensign\Processor;

require __DIR__ . '/../vendor/autoload.php';

Amp\Loop::run(function () {
    $processor = new Processor\ParallelProcessor();
    $dispatcher = new Dispatcher($processor);
    $dispatcher->register('load', function ($url) {
        echo "Start getting: {$url}" . \PHP_EOL;

        file_get_contents($url);

        echo "Done!" . \PHP_EOL;
    });

    $actionOne = $dispatcher->dispatch('load', 'http://php.net');
    $actionTwo = $dispatcher->dispatch('load', 'https://github.com');
    $actionThree = $dispatcher->dispatch('load', 'https://amphp.org');

    yield [$actionOne, $actionTwo, $actionThree];
    yield $processor->shutdown();
});
