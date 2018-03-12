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

final class Arguments implements \IteratorAggregate, \Countable
{
    /**
     * @var array
     */
    private $list;

    /**
     * @param array $list
     */
    private function __construct(array $list)
    {
        $this->list = $list;
    }

    /**
     * @return self
     */
    public static function empty(): self
    {
        return new self([]);
    }

    /**
     * @param array $arguments
     *
     * @return self
     */
    public static function list(array $arguments): self
    {
        return new self($arguments);
    }

    /**
     * @param Arguments $other
     *
     * @return self
     */
    public function merge(Arguments $other): self
    {
        return self::list(\array_replace($this->list, $other->list));
    }

    /**
     * @param Arguments $other
     *
     * @return self
     */
    public function inject(Arguments $other): self
    {
        $list = $this->list;

        foreach ($other->list as $position => $argument) {
            \array_splice($list, $position, 0, [$argument]);
        }

        return self::list($list);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return \count($this->list);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        yield from $this->list;
    }
}
