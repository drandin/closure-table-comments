<?php

namespace Drandin\ClosureTableComments;

use Carbon\Carbon;
use Drandin\ClosureTableComments\Interfaces\IClosureTable;
use Drandin\ClosureTableComments\Models\ClosureTableTree;
use DB;
use Drandin\ClosureTableComments\Models\Comment;
use Throwable;

/**
 * Class TreeService
 * @package Drandin\ClosureTableComments
 */
class TreeService implements IClosureTable
{

    /**
     * @var bool
     */
    private $addResult = false;

    /**
     * @var ClosureTableCollection
     */
    private $tree;

    /**
     * @param int $id
     * @return bool
     */
    public function has(int $id): bool
    {
        return ClosureTableTree::where('descendant_id', $id)->count() > 0;
    }

    /**
     * @param int $id
     * @throws Throwable
     */
    public function deleteBranch($id): void
    {
        $branchIds = $this->getBranchIds($id);

        $branchIds = array_values(array_unique($branchIds));

        if (empty($branchIds)) {
            return;
        }

        DB::transaction(static function () use ($branchIds) {
            ClosureTableTree::whereIn('descendant_id', $branchIds)->delete();
            Comment::whereIn('id', $branchIds)->delete();
        });

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

            $tblTree = ClosureTableTree::getModel()->getTable();

            $now = Carbon::now()->format('Y-m-d H:i:s');

            $level = $this->getLevel($id);

            $nextLevel = $level !== null
                ? $level + 1
                : 1;

            $subjectId = $node->getSubjectId();

            $newId = (int) $comment->id;

            $sql = "INSERT INTO {$tblTree} (`ancestor_id`, `descendant_id`, `nearest_ancestor_id`, `subject_id`, `level`, `created_at`, `updated_at`)
            SELECT ancestor_id, {$newId}, {$id}, {$subjectId}, {$nextLevel}, '{$now}', '{$now}'
            FROM {$tblTree}
            WHERE descendant_id = {$id}
            UNION ALL SELECT {$newId}, {$newId}, {$id}, {$subjectId}, {$nextLevel}, '{$now}', '{$now}'";

            $this->addResult = DB::insert($sql);
        });

        return $this->addResult;
    }


    /**
     * @param int $id
     * @return ClosureTableCollection|null
     */
    public function getTree($id = 0): ?ClosureTableCollection
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
        return ClosureTableTree::where('ancestor_id', $id)
            ->get()
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

        $treeItem = ClosureTableTree::where('descendant_id', $id)
            ->whereRaw('`ancestor_id` = `descendant_id`')
            ->first();

        if ($treeItem === null) {
            return null;
        }

        /**
         * @var $treeItem ClosureTableTree
         */
        return (int) $treeItem->level;
    }

    public function countItemsBySubject($subjectId): int
    {
        return 0;
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

        $tblTree = ClosureTableTree::getModel()
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

        $tblTree = ClosureTableTree::getModel()
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
     * @param array $treeData
     * @param int $ancestorId
     */
    private function buildTreeFlat(array $treeData, int $ancestorId = 0): void
    {
        if ($ancestorId >= 0) {

            if ($this->tree === null) {
                $this->tree = new ClosureTableCollection;
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

}
