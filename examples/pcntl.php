<?php

require __DIR__ . '/../vendor/autoload.php';

Amp\Loop::run(function () {
    \ensign_signal('wow', function ($sigNo) {
        echo \sprintf('System signal "%d" received.' . \PHP_EOL, $sigNo);
    });

    $signals = \pcntl_signal_list(['SIGINT']);

    foreach ($signals as $signal) {
        \ensign_signal($signal, function ($sigNo) {
            yield 'wow' => $sigNo;
        });
    }

    \ensign_pcntl_signals($signals);

    echo 'Waiting for signals ...' . \PHP_EOL;
});
