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
      // Le validator n'entre en action que si une photo est envoyée
      if(!empty($value)) {
        foreach ($value as $oneValue) {
          // J'exclus du validator les fichiers déjà chargés (ce sont des url, plus des fichiers)
          if(substr( $oneValue, 0, 4 ) === "http") {
            return true;
          } else {
            $type = explode('/', explode(':', substr($oneValue, 0, strpos($oneValue, ';')))[1])[1];
            if (in_array($type, $parameters)) {
              return true;
            }
          }
          return false;
        }
        // Si aucune photo n'est envoyée, pas de souci, validator ok
      } else {
        return true;
      }
    });

    Validator::replacer('image64', function($message, $attribute, $rule, $parameters) {
      return;
    });
  }
}
