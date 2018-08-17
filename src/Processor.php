<?php
/**
 * This file is part of PHPinnacle/Ensign.
 *
 * (c) PHPinnacle Team <dev@phpinnacle.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace PHPinnacle\Ensign;

use Amp\LazyPromise;
use PHPinnacle\Identity\UUID;

abstract class Processor implements Contract\Processor
{
    /**
     * @var callable[]
     */
    protected $interruptions = [];

    /**
     * {@inheritdoc}
     */
    public function interrupt(string $interrupt, callable $handler): void
    {
        $this->interruptions[$interrupt] = $handler;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(callable $handler, array $arguments): Action
    {
        $id = UUID::random();

        $token   = new Token($id);
        $promise = new LazyPromise($this->process($handler, $arguments, $token));

        return new Action($id, $promise, $token);
    }

    /**
     * @param callable $handler
     * @param array    $arguments
     * @param Token    $token
     *
     * @return callable
     */
    abstract protected function process(callable $handler, array $arguments, Token $token): callable;
}
