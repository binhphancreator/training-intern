<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\Video;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('now',function(){return now()->format('Y:m:d H:i:s');});
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            'post'=>Post::class,
            'video'=>Video::class,
        ]);
    }
}
