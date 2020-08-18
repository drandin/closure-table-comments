<?php

namespace Drandin\ClosureTableComments;

use Iterator;

/**
 * Class NodeCollection
 * @package Drandin\ClosureTableComments
 */
final class NodeCollection implements Iterator
{
    /**
     * @var array
     */
    private $users = [];

    /**
     * NodeCollection constructor.
     *
     * @param array|null $nodes
     */
    public function __construct(array $nodes = null)
    {
        if (!empty($nodes)) {
            foreach ($nodes as $node) {
                if ($node instanceof Node) {
                    $this->addNode($node);
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
        $this->nodes[] = $node;

        if ($node->getUserId() !== null) {
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
        return key($this->nodes) !== null;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->nodes);
    }
}
