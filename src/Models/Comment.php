<?php

namespace Drandin\ClosureTableComments\Models;

use Carbon\Carbon;
use Eloquent;

/**
 * Class ClosureTableTree
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $content
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package Drandin\ClosureTableComments\Models
 */
class Comment extends Eloquent
{
    public function __construct(array $attributes = [])
    {
        $database = config('closure-table-comments.database');
        $table = config('closure-table-comments.tables.comments');

        $this->setTable($database. '.'. $table);
        parent::__construct($attributes);
    }

    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @var array
     */
    protected $casts = [
        'id' => 'integer'
    ];

    /**
     * @var string[]
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];


}
