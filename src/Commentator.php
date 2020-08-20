<?php

namespace Drandin\ClosureTableComments;

use Throwable;

/**
 * Class Commentator
 * @package Drandin\ClosureTableComments
 */
final class Commentator
{
    /**
     * @var ClosureTableService
     */
    private $closureTableService;

    /**
     * @var null|int
     */
    private $subjectId;

    /**
     * Commentator constructor.
     *
     * @param ClosureTableService $closureTableService
     */
    public function __construct(ClosureTableService $closureTableService)
    {
        $this->closureTableService = $closureTableService;
    }

    /**
     * Устанавливает значение $subjectId
     *
     * @param int|null $subjectId
     * @return Commentator
     */
    public function setSubjectId(?int $subjectId = null): Commentator
    {
        $this->subjectId = $subjectId;
        return $this;
    }

    /**
     * Добавляем новый комментарий в корень дерева
     *
     * @param string $comment
     * @param int|null $userId
     * @return int
     * @throws Throwable
     */
    public function addCommentToRoot(string $comment, int $userId = null): int
    {
        if ($comment === '') {
            return false;
        }

        $node = new Node;

        $node
            ->setContent($comment)
            ->setSubjectId($this->subjectId)
            ->setUserId($userId);

        return $this->closureTableService->add($node);
    }

    /**
     * Ответ на комментарий $id
     *
     * @param int $id
     * @param string $comment
     * @param int|null $userId
     * @return int
     * @throws Throwable
     */
    public function replyToComment(int $id, string $comment, int $userId = null): int
    {
        if ($comment === '' || $id <= 0) {
            return false;
        }

        $node = new Node;

        $node
            ->setContent($comment)
            ->setSubjectId($this->subjectId)
            ->setUserId($userId);

        if (!$this->closureTableService->has($id)) {
            return false;
        }

        return $this->closureTableService->add($node, $id);
    }

    /**
     * Удаляет ветку комментариев начиная с элемента $id
     *
     * @param int $id
     * @return bool
     * @throws Throwable
     */
    public function delete(int $id): bool
    {
        return $this->closureTableService->delete($id);
    }

    /**
     * Редактирование комментария
     *
     * @param int $id
     * @param string $comment
     * @return bool
     */
    public function editComment(int $id, string $comment): bool
    {
        return $this->closureTableService->editComment($id, $comment);
    }

    /**
     * Возвращает отсортированную ветку дерева
     *
     * @param int $id
     * @return NodeCollection|null
     */
    public function getTreeBranch(int $id = 0): ?NodeCollection
    {
        return $this->closureTableService->getTree($id);
    }

    /**
     * Возвращает иерархию комментариев
     * в виде отсортированного древовидного массива
     *
     * @param int $id
     * @return array
     */
    public function getTreeBranchArray(int $id = 0): array
    {
        return $this->closureTableService->getHierarchyTree($id);
    }

    /**
     * Возвращает один элемент
     *
     * @param int $id
     * @param int|null $subject_id
     * @return Node|null
     */
    public function getNode(int $id = 0, int $subject_id = null): ?Node
    {
        return $this->closureTableService->getNode($id, $subject_id);
    }

    /**
     * Проверяет существование элемента в структуре
     *
     * @param int $id
     * @return bool
     */
    public function has(int $id): bool
    {
        return $this->closureTableService->has($id);
    }

    /**
     * @param int $id
     * @return array
     */
    public function getBranchIds(int $id): array
    {
        return $this->closureTableService->getBranchIds($id);
    }

    /**
     * @param $id
     * @return int|null
     */
    public function getLevel($id): ?int
    {
        return $this->closureTableService->getLevel($id);
    }
}
