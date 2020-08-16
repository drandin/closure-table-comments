<?php

namespace Drandin\ClosureTableComments;

use Carbon\Carbon;

/**
 * Class Node
 * @package Drandin\ClosureTableComments
 */
class Node
{
    /**
     * Номер записи
     * @var int
     */
    protected $id = 0;

    /**
     * Предок
     * @var int
     */
    protected $ancestorId = 0;

    /**
     * Потомок
     * @var int
     */
    protected $descendantId = 0;

    /**
     * Ближайший предок
     * @var int
     */
    protected $nearestAncestorId = 0;

    /**
     * Уровень вложености
     *
     * @var int
     */
    protected $level = 0;

    /**
     * Код субъекта
     *
     * @var int
     */
    protected $subjectId = 0;

    /**
     * Код пользователя
     *
     * @var int|null
     */
    protected $userId;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var Carbon
     */
    protected $created_at;

    /**
     * @var Carbon
     */
    protected $updated_at;

    /**
     * @return int
     */
    public function getId(): int
    {
        return (int) $this->id;
    }

    /**
     * @param int $id
     * @return Node
     */
    public function setId(int $id): Node
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getAncestorId(): int
    {
        return $this->ancestorId;
    }

    /**
     * @param int $ancestorId
     * @return Node
     */
    public function setAncestorId(int $ancestorId): Node
    {
        $this->ancestorId = $ancestorId;
        return $this;
    }

    /**
     * @return int
     */
    public function getDescendantId(): int
    {
        return $this->descendantId;
    }

    /**
     * @param int $descendantId
     * @return Node
     */
    public function setDescendantId(int $descendantId): Node
    {
        $this->descendantId = $descendantId;
        return $this;
    }

    /**
     * @return int
     */
    public function getNearestAncestorId(): int
    {
        return $this->nearestAncestorId;
    }

    /**
     * @param int $nearestAncestorId
     * @return Node
     */
    public function setNearestAncestorId(int $nearestAncestorId): Node
    {
        $this->nearestAncestorId = $nearestAncestorId;
        return $this;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @param int $level
     * @return Node
     */
    public function setLevel(int $level): Node
    {
        $this->level = $level;
        return $this;
    }

    /**
     * @return int
     */
    public function getSubjectId(): int
    {
        return (int) $this->subjectId;
    }

    /**
     * @param int $subjectId
     * @return Node
     */
    public function setSubjectId(int $subjectId): Node
    {
        $this->subjectId = $subjectId;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param $userId
     * @return Node
     */
    public function setUserId($userId): Node
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return Node
     */
    public function setContent(string $content): Node
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return Carbon
     */
    public function getCreatedAt(): Carbon
    {
        return $this->created_at;
    }

    /**
     * @param Carbon $created_at
     * @return Node
     */
    public function setCreatedAt(Carbon $created_at): Node
    {
        $this->created_at = $created_at;
        return $this;
    }

    /**
     * @return Carbon
     */
    public function getUpdatedAt(): Carbon
    {
        return $this->updated_at;
    }

    /**
     * @param Carbon $updated_at
     * @return Node
     */
    public function setUpdatedAt(Carbon $updated_at): Node
    {
        $this->updated_at = $updated_at;
        return $this;
    }



}
