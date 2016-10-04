<?php

namespace Lupka\Printful;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class PrintfulServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('printful.php'),
        ]);
    }
    
}
