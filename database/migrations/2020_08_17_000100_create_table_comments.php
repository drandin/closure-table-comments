<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateTableComments
 */
class CreateTableComments extends Migration
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
        $database = config('closure-table-comments.database') ?? DB::getDatabaseName();

        $this->tbl = implode('.', [
            $database,
            config('closure-table-comments.tables.comments')
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
            $table->integerIncrements('id')->unsigned();
            $table->integer('user_id')->nullable();
            $table->text('content');
            $table->timestamps();
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
