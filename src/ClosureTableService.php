<?php

namespace Drandin\ClosureTableComments;

use Carbon\Carbon;
use Drandin\ClosureTableComments\Interfaces\IClosureTable;
use Drandin\ClosureTableComments\Models\StructureTree;
use DB;
use Drandin\ClosureTableComments\Models\Comment;
use Throwable;

/**
 * Class ClosureTableService
 *
 * @package Drandin\ClosureTableComments
 */
final class ClosureTableService implements IClosureTable
{
    /**
     * @var bool
     */
    private $addResult = false;

    /**
     * @var bool
     */
    private $deleteResult = false;

    /**
     * @var NodeCollection
     */
    private $tree;

    /**
     * @param int $id
     * @return bool
     */
    public function has(int $id): bool
    {
        if ($id <= 0) {
            return false;
        }

        return StructureTree::where('descendant_id', $id)->count() > 0;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteBranch($id): bool
    {
        $this->deleteResult = false;

        $branchIds = $this->getBranchIds($id);

        $branchIds = array_values(array_unique($branchIds));

        if (empty($branchIds)) {
            return false;
        }

        DB::transaction(function () use ($branchIds) {
            StructureTree::whereIn('descendant_id', $branchIds)->delete();
            Comment::whereIn('id', $branchIds)->delete();
            $this->deleteResult = true;
        });

        return $this->deleteResult;
    }

    /**
     * Добавляем новый элемент к существующему элементу с $id > 0
     *
     * @param Node $node
     * @param int $id
     * @return bool
     * @throws Throwable
     */
    public function add(Node $node, int $id = 0): bool
    {
        $this->addResult = false;

        DB::transaction(function () use ($node, $id) {

            $comment = new Comment;
            $comment->user_id = $node->getUserId();
            $comment->content = $node->getContent();
            $comment->save();

            $tblTree = StructureTree::getModel()->getTable();

            $now = Carbon::now()->format('Y-m-d H:i:s');

            $level = $this->getLevel($id);

            $nextLevel = $level !== null
                ? $level + 1
                : 1;

            $subjectId = $node->getSubjectId();

            $newId = (int) $comment->id;

            $fields = '`ancestor_id`,';
            $fields.= '`descendant_id`,';
            $fields.= '`nearest_ancestor_id`,';
            $fields.= '`subject_id`,';
            $fields.= '`level`,';
            $fields.= '`created_at`,';
            $fields.= '`updated_at`';

            $sql = "INSERT INTO {$tblTree} ({$fields})
            SELECT `ancestor_id`, {$newId}, {$id}, ?, {$nextLevel}, '{$now}', '{$now}'
            FROM {$tblTree}
            WHERE `descendant_id` = {$id}
            UNION ALL SELECT {$newId}, {$newId}, {$id}, ?, {$nextLevel}, '{$now}', '{$now}'";

            $this->addResult = DB::insert($sql, [$subjectId, $subjectId]);

        });

        return $this->addResult;
    }


    /**
     * @param int $id
     * @return NodeCollection|null
     */
    public function getTree($id = 0): ?NodeCollection
    {
        $this->tree = null;
        $this->buildTreeFlat($this->getBranch($id));
        return $this->tree;
    }

    /**
     * @param int $id
     * @return array
     */
    public function getBranchIds($id): array
    {
        return StructureTree::select('descendant_id')
            ->where('ancestor_id', $id)
            ->pluck('descendant_id')
            ->toArray();
    }

    /**
     * @param int $id
     * @return array
     */
    public function getHierarchyTree($id = 0): array
    {
        return $this->buildHierarchyArrayTree($this->getBranch($id));
    }

    /**
     * @param int $id
     * @return int|null
     */
    public function getLevel(int $id): ?int
    {
        if ($id <= 0) {
            return null;
        }

        $treeItem = StructureTree::where('descendant_id', $id)
            ->whereRaw('`ancestor_id` = `descendant_id`')
            ->first();

        if ($treeItem === null) {
            return null;
        }

        /**
         * @var $treeItem StructureTree
         */
        return (int) $treeItem->level;
    }

    /**
     * @param int $subjectId
     * @return int
     */
    public function countNodesBySubject(int $subjectId): int
    {
        $res = StructureTree::selectRaw('COUNT(DISTINCT `ancestor_id`) AS `countNodes`')
            ->where('subject_id', $subjectId)
            ->first();

        return $res->countNodes ?? 0;
    }


    /**
     * @param int $id
     * @param int|null $subject_id
     * @return array
     */
    public function getBranch(int $id = 0, int $subject_id = null): array
    {
        $tblComments = Comment::getModel()
            ->getTable();

        $tblTree = StructureTree::getModel()
            ->getTable();

        $builder = DB::table($tblTree)
            ->join(
                $tblComments,
                $tblTree.'.descendant_id',
                '=',
                $tblComments.'.id'
            )->select([
                $tblComments.'.id',
                $tblComments.'.user_id',
                $tblComments.'.content',
                $tblTree.'.ancestor_id',
                $tblTree.'.descendant_id',
                $tblTree.'.nearest_ancestor_id',
                $tblTree.'.level',
                $tblTree.'.subject_id',
                $tblComments.'.created_at',
                $tblComments.'.updated_at'
            ]);

        if ($id > 0) {
            $builder->where($tblTree.'.ancestor_id', '=', $id);
        }

        if ($subject_id !== null) {
            $builder->where($tblTree.'.subject_id', '=', $subject_id);
        }

        return $builder
             ->orderBy($tblComments.'.id', 'ASC')
             ->get()
             ->keyBy('id')
             ->toArray();
    }

    /**
     * @param int $id
     * @param int|null $subject_id
     * @return array
     */
    public function getOne(int $id = 0, int $subject_id = null): array
    {
        $tblComments = Comment::getModel()
            ->getTable();

        $tblTree = StructureTree::getModel()
            ->getTable();

        $builder = DB::table($tblTree)
            ->join(
                $tblComments,
                $tblTree.'.descendant_id',
                '=',
                $tblComments.'.id'
            )->select([
                $tblComments.'.id',
                $tblComments.'.user_id',
                $tblComments.'.content',
                $tblTree.'.ancestor_id',
                $tblTree.'.descendant_id',
                $tblTree.'.nearest_ancestor_id',
                $tblTree.'.level',
                $tblTree.'.subject_id',
                $tblComments.'.created_at',
                $tblComments.'.updated_at'
            ])
            ->whereRaw($tblTree.'.descendant_id = '.$tblTree.'.ancestor_id')
            ->where($tblTree.'.ancestor_id', '=', $id);

        if ($subject_id !== null) {
            $builder->where($tblTree.'.subject_id', '=', $subject_id);
        }

        $one = $builder
            ->orderBy($tblComments.'.id', 'ASC')
            ->first();

        return $one !== null
            ? (array) $one
            : [];
    }

    /**
     * @param int $id
     * @param int|null $subject_id
     * @return Node|null
     */
    public function getNode(int $id = 0, int $subject_id = null): ?Node
    {
        $data = $this->getOne($id, $subject_id);

        if (empty($data)) {
            return null;
        }

        return $this->createNode($data);
    }


    /**
     * @param array $treeData
     * @param int $ancestorId
     */
    private function buildTreeFlat(array $treeData, int $ancestorId = 0): void
    {
        if ($ancestorId >= 0) {

            if ($this->tree === null) {
                $this->tree = new NodeCollection;
            }

            foreach ($treeData as $item) {

                if ((int) $item->nearest_ancestor_id === $ancestorId || $this->tree->count() === 0) {

                    $data = (array) $treeData[(int) $item->id];

                    $obj = $this->createNode($data);

                    if ($obj instanceof Node) {
                        $this->tree->addNode($obj);
                    }

                    $this->buildTreeFlat($treeData, (int) $item->id);
                }
            }
        }
    }

    /**
     * @param array $treeData
     * @param int $ancestorId
     * @return array
     */
    private function buildHierarchyArrayTree(array $treeData, int $ancestorId = 0): array
    {
        $tree = [];

        if ($ancestorId >= 0) {

            foreach ($treeData as $item) {

                $item = (array) $item;

                if ((int) $item['nearest_ancestor_id'] === $ancestorId) {

                    $tree[] = [
                        'id' => (int) $item['id'],
                        'data' => (array) $treeData[(int) $item['id']],
                        'descendant' =>  $this->buildHierarchyArrayTree($treeData, (int) $item['id'])
                    ];
                }
            }
        }

        return empty($tree) ? [] : $tree;
    }

    /**
     * @param array $data
     * @return Node
     */
    private function createNode(array $data): Node
    {
        $node = new Node;

        $data['created_at'] = Carbon::parse($data['created_at']);
        $data['updated_at'] = Carbon::parse($data['updated_at']);

        $node->setId((int) $data['id']);
        $node->setAncestorId($data['ancestor_id'] ?? 0);
        $node->setDescendantId($data['descendant_id'] ?? 0);
        $node->setSubjectId($data['subject_id'] ?? 0);
        $node->setUserId($data['user_id'] ?? null);
        $node->setContent($data['content'] ?? '');
        $node->setLevel($data['level'] ?? 0);
        $node->setCreatedAt($data['created_at']);
        $node->setUpdatedAt($data['updated_at']);

        return $node;
    }

    /**
     * @param string $comment
     * @param int $id
     * @return bool
     */
    public function editComment(string $comment, int $id): bool
    {
        if ($comment === '' || $id <= 0) {
            return false;
        }

        $commentExist = Comment::find($id);

        if ($commentExist !== null) {
            $commentExist->content = $comment;
            return $commentExist->save();
        }

        return false;
    }

}
