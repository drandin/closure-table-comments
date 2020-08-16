<?php

namespace Drandin\ClosureTableComments;

use Iterator;

/**
 * Class ClosureTableCollection
 * @package Drandin\ClosureTableComments
 */
class ClosureTableCollection implements Iterator
{
    /**
     * @var array
     */
    private $users = [];

    /**
     * @param null $items
     */
    public function __construct($items = null)
    {
        if (is_array($items)) {
            foreach ($items as $item) {
                if ($item instanceof Node) {
                    $this->addNode($item);
                }
            }

            $this->rewind();
        }
    }

    /**
     * @param Node $node
     * @return $this
     */
    public function addNode(Node $node): self
    {
        if ($node !== null) {
            $this->nodes[] = $node;
            $this->users[$node->getUserId()] = $node->getUserId();
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getUserIds(): array
    {
        return array_values($this->users);
    }

    /**
     * @var array
     */
    protected $nodes = [];


    /**
     * Перемотка в начало
     */
    public function rewind(): void
    {
        reset($this->nodes);
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return current($this->nodes);
    }

    /**
     * @return mixed
     */
    public function key()
    {
        return key($this->nodes);
    }

    /**
     * @return mixed|void
     */
    public function next()
    {
        return next($this->nodes);
    }

    /**
     * @return bool
     */
    public function valid(): bool
    {
        $key = key($this->nodes);

        return $key !== null && $key !== false;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->nodes);
    }
}
