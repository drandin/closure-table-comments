<?php

namespace Drandin\ClosureTableComments;

use Illuminate\Support\ServiceProvider;

/**
 * Class ClosureTableServiceProvider
 * @package Drandin\ClosureTableComments
 */
class ClosureTableServiceProvider extends ServiceProvider
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
        $this->mergeConfigFrom(
            __DIR__ . '/../config/closure-table-comments.php',
            'closure-table-comments'
        );
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
    }
}
