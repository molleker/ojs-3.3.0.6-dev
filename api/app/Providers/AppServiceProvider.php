<?php

namespace App\Providers;

use App\AntiplagiatReport;
use App\Article;
use App\ArticleFile;
use App\Observers\AntiplagiatReportObserver;
use App\Observers\ArticleFileObserver;
use App\Observers\ArticleObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        ArticleFile::observe(ArticleFileObserver::class);
        Article::observe(ArticleObserver::class);
        AntiplagiatReport::observe(AntiplagiatReportObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
