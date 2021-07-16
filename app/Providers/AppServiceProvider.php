<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\Finder\Finder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->isLocal()) {
//            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
//            $this->app->register(TelescopeServiceProvider::class);
        }
        $modules = config('modules.file');
        foreach ($modules as $module){
            $directory = __DIR__.'/../Domains/'.$module.'/Config';
            if (!is_dir($directory)) {
                continue;
            }
            foreach (Finder::create()->in($directory)->name('*.php') as $file) {
                $this->mergeConfigFrom( $file->getRealPath() , $module.'.'.basename($file->getRealPath(), '.php') );
            }
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
