<?php

namespace Drandin\ClosureTableComments\Models;

use Carbon\Carbon;
use Eloquent;

/**
 * Class StructureTree
 *
 * @property int $ancestor_id
 * @property int $descendant_id
 * @property int $nearest_ancestor_id
 * @property int $level
 * @property int|null $subject_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package Drandin\ClosureTableComments\Models
 */
class StructureTree extends Eloquent
{

    public function __construct(array $attributes = [])
    {
        $database = config('closure-table-comments.database');
        $table = config('closure-table-comments.tables.structure');

        $this->setTable($database. '.'. $table);
        parent::__construct($attributes);
    }

    /**
     * @var string[]
     */
    protected $primaryKey = [
        'ancestor_id',
        'descendant_id'
    ];

    /**
     * @var bool
     */
    public $incrementing = false;


    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var array
     */
    protected $casts = [
        'ancestor_id' => 'integer',
        'descendant_id' => 'integer',
        'nearest_ancestor_id' => 'integer',
        'level' => 'integer',
    ];

    /**
     * @var string[]
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];


}
