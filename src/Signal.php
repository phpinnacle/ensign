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

final class Signal implements \Countable, \IteratorAggregate
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $arguments;

    /**
     * @param string    $name
     * @param mixed  ...$arguments
     */
    public function __construct(string $name, ...$arguments)
    {
        $this->name      = $name;
        $this->arguments = $arguments;
    }

    /**
     * @param mixed $signal
     * @param array $arguments
     *
     * @return Signal
     */
    public static function create($signal, array $arguments = []): self
    {
        $name      = $signal;
        $arguments = \array_values($arguments);

        if (\is_object($signal)) {
            $name = \get_class($signal);

            \array_unshift($arguments, $signal);
        }

        return new self($name, ...$arguments);
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function arguments(): array
    {
        return $this->arguments;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        yield from $this->arguments;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return \count($this->arguments);
    }
}
