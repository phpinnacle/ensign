<?php

use Amp\Delayed;
use PHPinnacle\Ensign\Dispatcher;
use PHPinnacle\Ensign\HandlerRegistry;

require __DIR__ . '/../vendor/autoload.php';

Amp\Loop::run(function () {
    $handlers = new HandlerRegistry;
    $handlers
        ->add('spawn', function (callable $process) {
            static $pid = 1;

            $gen = $process($pid);

            yield from $gen;

            return $pid++;
        })
        ->add('send', function (int $pid, string $message, ...$arguments) {
            $signal = sprintf('%s.%d', $message, $pid);

            return yield $signal => $arguments;
        })
        ->add('receive', function (int $pid, string $message, callable $handler) use ($handlers) {
            $signal = sprintf('%s.%d', $message, $pid);

            $handlers->add($signal, $handler);
        })
    ;

    $dispatcher = new Dispatcher($handlers);

    $receiver = yield $dispatcher->dispatch('spawn', function (int $pid) {
        yield 'receive' => [$pid, 'ping', function () {
            echo 'Ping!' . \PHP_EOL;
        }];

        yield 'receive' => [$pid, 'pong', function () {
            echo 'Pong!' . \PHP_EOL;
        }];
    });

    $senderOne = $dispatcher->dispatch('spawn', function () use ($receiver) {
        yield 'send' => [$receiver, 'ping'];
        yield new Delayed(500);
        yield 'send' => [$receiver, 'ping'];
    });

    $senderTwo = $dispatcher->dispatch('spawn', function () use ($receiver) {
        yield 'send' => [$receiver, 'pong'];
        yield new Delayed(100);
        yield 'send' => [$receiver, 'pong'];
    });

    yield [$senderOne, $senderTwo];
});
