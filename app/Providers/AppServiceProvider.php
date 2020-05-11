<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
      // Règles custom permettant de valider le type des images chargées en base 64
      Validator::extend('image64', function ($attribute, $value, $parameters, $validator) {
          $type = explode('/', explode(':', substr($value, 0, strpos($value, ';')))[1])[1];
          if (in_array($type, $parameters)) {
              return true;
          }
          return false;
      });

      Validator::replacer('image64', function($message, $attribute, $rule, $parameters) {
          return str_replace(':values',join(",",$parameters),$message);
      });
    }
}
