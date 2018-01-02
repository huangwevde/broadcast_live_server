<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
       if (env('DB_Listen', false)) {
           DB::listen(function($sql) {
               foreach ($sql->bindings as $i => $binding) {
                   if ($binding instanceof \DateTime) {
                       $sql->bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
                   } else {
                       if (is_string($binding)) {
                           $sql->bindings[$i] = "'$binding'";
                       }
                   }
               }
               $query = str_replace(array('%', '?'), array('%%', '%s'), $sql->sql);
               $query = vsprintf($query, $sql->bindings);
               Log::info($query);
           });
       }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
