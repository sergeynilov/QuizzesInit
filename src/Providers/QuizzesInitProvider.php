<?php

// packages/sergeynilov/QuizzesInit/src/Providers/QuizzesInitProvider.php

// packages/sergeynilov/QuizzesInit/src/Providers/QuizzesInitProvider.php
namespace sergeynilov\QuizzesInit\Providers;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;

// composer require sergeynilov\QuizzesInit
// php artisan vendor:publish --tag=lang --provider=sergeynilov\QuizzesInit\QuizzesInitProvider
class QuizzesInitProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
//        \Log::info(varDump(-1, ' -1 QuizzesInitProvider boot::'));
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->loadViewsFrom(__DIR__ . '/../views', 'QuizzesInit' );
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'QuizzesInit');
        $this->publishes([
            __DIR__.'/../lang' => $this->app->langPath('sergeynilov/QuizzesInit'),
        ], 'lang');
        // php artisan vendor:publish --tag=lang --provider=sergeynilov\QuizzesInit\QuizzesInitProvider
        // php artisan vendor:publish --tag=config  --provider=sergeynilov\QuizzesInit\QuizzesInitProvider

//        $this->publishes([
//            __DIR__.'/../lang' => $this->app->langPath('vendor/QuizzesInit'),
//        ], 'lang');

        $this->app->bind(
            //use sergeynilov\QuizzesInit\Library\Services\Interfaces\DbRepositoryInterface;
            'sergeynilov\QuizzesInit\Library\Services\Interfaces\DbRepositoryInterface',

            // packages/sergeynilov/QuizzesInit/src/Library/Services/DbRepository.php
            'sergeynilov\QuizzesInit\Library\Services\DbRepository'
        );

        AboutCommand::add('Quizzes Init', fn () => ['Version' => '1.2.3']);

        /* Target [sergeynilov\QuizzesInit\Library\Services\Interfaces\DbRepositoryInterface] is not instantiable.*/
//        \Log::info( ' -1 __DIR__ . views::');
//        \Log::info( __DIR__ . '/views');
//        $this->loadViewsFrom(__DIR__.'/../views', 'inspire');
//        $this->loadViewsFrom(__DIR__.'/../resources/views', 'courier');

    }

    public function register()
    {
//        \Log::info(varDump(-1, ' -1 QuizzesInitProvider register::'));

        // /_wwwroot/lar/quizzes/packages/sergeynilov/QuizzesInit/src/config/quizzes-init.php
        $this->mergeConfigFrom(
            __DIR__.'/../config/quizzes-init.php', 'quizzes-init'
        );
    }
}
