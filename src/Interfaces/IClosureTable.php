<?php

namespace Drandin\ClosureTableComments\Interfaces;

use Drandin\ClosureTableComments\NodeCollection;
use Drandin\ClosureTableComments\Node;

/**
 * Interface IClosureTable
 * @package Drandin\ClosureTableComments\Interfaces
 */
interface IClosureTable {

    /**
     * Check having element in tree
     *
     * @param int $id
     * @return bool
     */
    public function has(int $id): bool;

    /**
     * Delete branch of tree
     *
     * @param int $id
     * @return bool
     */
    public function deleteBranch(int $id): bool;

    /**
     * Add one new element into tree
     *
     * @param Node $node
     * @param int $id
     * @return bool
     */
    public function add(Node $node, int $id = 0): bool;

    /**
     * Return part of tree or entire hierarchy from root
     *
     * @param int $id
     * @return NodeCollection|null
     */
    public function getTree(int $id = 0): ?NodeCollection;


    /**
     * Return IDs all elements of branch of tree
     *
     * @param int $id
     * @return array
     */
    public function getBranchIds(int $id): array;

    /**
     * Return part tree or entire tree as multidimensional array
     *
     * @param int $id
     * @return array
     */
    public function getHierarchyTree(int $id = 0): array;

    /**
     * @param int $id
     * @return int|null
     */
    public function getLevel(int $id): ?int;

    /**
     * Return count of elements in tree which belongs to $subjectId
     *
     * @param int $subjectId
     * @return int
     */
    public function countNodesBySubject(int $subjectId): int;

    /**
     * Edit exist comment
     *
     * @param string $comment
     * @param int $id
     * @return bool
     */
    public function editComment(string $comment, int $id): bool;

}
