<?php

namespace Drandin\ClosureTableComments;

use Illuminate\Support\ServiceProvider;

/**
 * Class ClosureTableServiceProvider
 * @package Drandin\ClosureTableComments
 */
final class ClosureTableServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = false;

    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/closure-table-comments.php' => config_path('closure-table-comments.php'),
        ], 'config');

        $this->loadMigrationsFrom([
            __DIR__.'/../database/migrations/2020_08_17_000100_create_table_comments.php',
            __DIR__.'/../database/migrations/2020_08_17_000200_create_table_structure_tree.php'
        ]);
    }
}
