<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\City;


//Controller exclusivement dédié à l'Api

class CityController extends Controller
{
  /**
  * Affiche la liste des ressources (GET)
  *
  * @return \Illuminate\Http\Response
  */
  public function index()
  {
    // code 200 : succès de la requête
    return response()->json(City::get(), 200);
  }

  /**
  * Enregistrement d'une nouvelle ressource (POST)
  *
  * @param  \Illuminate\Http\Request  $request
  * @return \Illuminate\Http\Response
  */
  public function store(Request $request)
  {
    // Récupération requête
    $data = $request->all();
dd($data);


    // if(isset($data['city_id'])) {
    //   if(isset($data['region_id'])){
    //
    //     $region = Region::firstOrCreate(array("name" => $data['region_id']));
    //     $data['region_id'] = $region->id;
    //
    //     $city = City::firstOrCreate(array("name" => $data['city_id'], "region_id" => $region->id));
    //     $data['city_id'] = $city->id;
    //   }
    // }


    // // Enregistrement des villes nouvellement créées
    // if (isset($data['city_id'])) {
    //   // Pour chacune des villes récupérées ici
    //   foreach ($data['city_id'] as $city) {
    //     // On formatte la ville comme la BDD l'attend : name : xxx
    //     // On la stocke dans un tableau
    //     $formattedCity = ["name" => $city];
    //     // On stocke chacune de ces villes en BDD
    //     foreach ($formattedCity as $newCity) {
    //       //firstOrCreate pour éviter tout doublon accidentel
    //       //(même si normalement doublons rendus impossibles par Vue Multiselect)
    //       City::firstOrCreate($newCity)->id;
    //     }
    //   }
    // }

    return response()->json($city, 201);

  }

}
