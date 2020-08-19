<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateTableStructureTree
 */
class CreateTableStructureTree extends Migration
{
    /**
     * @var string
     */
    private $tbl;

    /**
     * CreateTableClosureTableTree constructor.
     */
    public function __construct()
    {
        $this->tbl = implode('.', [
            config('closure-table-comments.database'),
            config('closure-table-comments.tables.structure')
        ]);
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create($this->tbl, static function (Blueprint $table) {
            $table->integer('ancestor_id')->unsigned()->default(0);
            $table->integer('descendant_id')->unsigned()->default(0);
            $table->integer('nearest_ancestor_id')->unsigned()->default(0);
            $table->smallInteger('level')->unsigned()->default(1);
            $table->integer('subject_id')->nullable();
            $table->timestamps();

            $table->primary(['ancestor_id', 'descendant_id'], 'primary_key_base');

            $table->foreign('ancestor_id')
                ->references('id')
                ->on('comments')
                ->cascadeOnDelete();

            $table->foreign('descendant_id')
                ->references('id')
                ->on('comments')
                ->cascadeOnDelete();

            $table->index([
                'ancestor_id',
                'descendant_id',
                'nearest_ancestor_id',
                'level',
                'subject_id'
            ], 'base');

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists($this->tbl);
    }
}
