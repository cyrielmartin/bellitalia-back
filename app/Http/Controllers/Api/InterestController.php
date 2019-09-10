<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\Interest;
use App\City;
use App\Region;
use App\Bellitalia;

class InterestController extends Controller
{
  /**
  * Affiche la liste des ressources (GET)
  *
  * @return \Illuminate\Http\Response
  */
  public function index()
  {
    // code 200 : succès de la requête
    return response()->json(Interest::get(), 200);
  }

  /**
  * Enregistrement d'une nouvelle ressource (POST)
  *
  * @param  \Illuminate\Http\Request  $request
  * @return \Illuminate\Http\Response
  */
  public function store(Request $request)
  {
    // // Je mets ici mes règles de validation
    // $rules = [
    //   'name' => 'required',
    //   'latitude' => 'required',
    //   'longitude' => 'required',
    //   'city.name' => 'required_without:city_id',
    //   'city.region.name' => 'required_without:city.region_id',
    //   'bellitalia.number' => 'required_without:bellitalia_id'
    //
    // ];
    //
    // // J'applique le Validator à toutes les requêtes envoyées.
    // $validator = Validator::make($request->all(), $rules);
    // // Si moindre souci : 404.
    // if($validator->fails()){
    //   //code 400 : syntaxe requête erronée
    //   return response()->json($validator->errors(), 400);
    // }

    // Bonne pratique : on ne modifie pas directement la requête récupérée.
    $data = $request->all();
    if(isset($data['city_id'])) {
      if(isset($data['region_id'])){
        if(isset($data['bellitalia_id'])) {
          if(isset($data['publication'])) {

            $region = Region::firstOrCreate(array("name" => $data['region_id']));
            $data['region_id'] = $region->id;

            $city = City::firstOrCreate(array("name" => $data['city_id'], "region_id" => $region->id));
            $data['city_id'] = $city->id;

            $bellitalia = BellItalia::firstOrCreate(array("number" => $data['bellitalia_id'], "publication" => $data['publication']));
            $data['bellitalia_id'] = $bellitalia->id;

            $interest = Interest::create($data);
          }
        }
      }
    }
  }
  //
  // // Si la ville est déjà en base...
  // if(isset($request['city_id'])){
  //   // ... et qu'elle est associée à une région déjà en base...
  //   if(isset($request['city']['region_id'])){
  //     // ... et que le Bellitalia associé existe déjà en base
  //     if(isset($request['bellitalia_id'])) {
  //       //... on enregistre
  //       $interest = Interest::create($data);
  //       // Si le BellItalia n'existe pas encore en base, je le crée, je l'associe...
  //     } else {
  //       $bellitalia = BellItalia::create(array("number" => $data['bellitalia']['number']));
  //       $data['bellitalia_id'] = $bellitalia->id;
  //       //... et on enregistre
  //       $interest = Interest::create($data);
  //     }
  //     // ... et qu'on veut l'associer à une nouvelle région...
  //   } else {
  //     $region = Region::create(array("name" => $data['city']['region']['name']));
  //     $data['region_id'] = $region->id;
  //     // ... et que le Bellitalia associé existe déjà en base
  //     if(isset($request['bellitalia_id'])) {
  //       //... on enregistre
  //       $interest = Interest::create($data);
  //       // Si le BellItalia n'existe pas encore en base, je le crée, je l'associe...
  //     } else {
  //       $bellitalia = BellItalia::create(array("number" => $data['bellitalia']['number']));
  //       $data['bellitalia_id'] = $bellitalia->id;
  //       //... et on enregistre
  //       $interest = Interest::create($data);
  //     }
  //   }
  // }
  // // Si la ville n'est pas en base...
  // else {
  //   // ... et qu'on veut l'associer à une région est déjà en base :
  //   if(isset($request['city']['region_id'])){
  //     $city = City::create(array("name" => $data['city']['name'], "region_id" => $data['city']['region_id']));
  //     $data['city_id'] = $city->id;
  //     // ... et que le Bellitalia associé existe déjà en base
  //     if(isset($request['bellitalia_id'])) {
  //       //... on enregistre
  //       $interest = Interest::create($data);
  //       // Si le BellItalia n'existe pas encore en base, je le crée, je l'associe...
  //     } else {
  //       $bellitalia = BellItalia::create(array("number" => $data['bellitalia']['number']));
  //       $data['bellitalia_id'] = $bellitalia->id;
  //       //... et on enregistre
  //       $interest = Interest::create($data);
  //     }
  //     //... et qu'on veut l'associer à une région n'est pas encore en base :
  //   } else {
  //     $region = Region::create(array("name" => $data['city']['region']['name']));
  //     $data['region_id'] = $region->id;
  //     $city = City::create(array("name" => $data['city']['name'], "region_id" => $region->id));
  //     $data['city_id'] = $city->id;
  //     // ... et que le Bellitalia associé existe déjà en base
  //     if(isset($request['bellitalia_id'])) {
  //       //... on enregistre
  //       $interest = Interest::create($data);
  //       // Si le BellItalia n'existe pas encore en base, je le crée, je l'associe...
  //     } else {
  //       $bellitalia = BellItalia::create(array("number" => $data['bellitalia']['number']));
  //       $data['bellitalia_id'] = $bellitalia->id;
  //       //... et on enregistre
  //       $interest = Interest::create($data);
  //     }
  //   }
  // }
  // // Code 201 : succès requête et création ressource
  // return response()->json($interest, 201);

  // }

  /**
  * Affichage d'une ressource (GET)
  *
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function show($id)
  {
    $interest = Interest::find($id);
    if(is_null($interest)){
      return response()->json(['message' => 'Not found'], 404);
    }
    // Code 200 : succès requête
    return response()->json($interest, 200);
  }

  /**
  * Mise à jour d'une ressource (PUT)
  *
  * @param  \Illuminate\Http\Request  $request
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function update(Request $request, $id)
  {
    $interest = Interest::find($id);
    if(is_null($interest)){
      return response()->json(['message' => 'Not found'], 404);
    }

    // Bonne pratique : on ne modifie pas directement la requête récupérée.
    $data = $request->all();
    if(isset($data['city_id'])) {
      if(isset($data['region_id'])){
        if(isset($data['bellitalia_id'])) {
          if(isset($data['publication'])) {

            $region = Region::updateOrCreate(array("name" => $data['region_id']));
            $data['region_id'] = $region->id;

            $city = City::updateOrCreate(array("name" => $data['city_id'], "region_id" => $region->id));
            $data['city_id'] = $city->id;

            $bellitalia = BellItalia::updateOrCreate(array("number" => $data['bellitalia_id'], "publication" => $data['publication']));
            $data['bellitalia_id'] = $bellitalia->id;

            $interest = Interest::updateOrCreate($data);
          }
        }
      }
    }

    // return response()->json($interest, 200);

    //
    // // Je mets ici mes règles de validation
    // $rules = [
    //   'name' => 'required',
    //   'latitude' => 'required',
    //   'longitude' => 'required',
    //   'city.name' => 'required_without:city_id',
    //   'city.region.name' => 'required_without:city.region_id',
    //   'bellitalia.number' => 'required_without:bellitalia_id'
    //
    // ];
    //
    // // J'applique le Validator à toutes les requêtes envoyées.
    // $validator = Validator::make($request->all(), $rules);
    // // Si moindre souci : 404.
    // if($validator->fails()){
    //   //code 400 : syntaxe requête erronée
    //   return response()->json($validator->errors(), 400);
    // }
    //
    // // Bonne pratique : on ne modifie pas directement la requête récupérée.
    // $data = $request->all();
    //
    //
    // // Si la ville est déjà en base...
    // if(isset($request['city_id'])){
    //   // ... et qu'elle est associée à une région déjà en base...
    //   if(isset($request['city']['region_id'])){
    //     // ... et que le Bellitalia associé existe déjà en base
    //     if(isset($request['bellitalia_id'])) {
    //       //... on enregistre
    //       $interest = Interest::update($data);
    //       // Si le BellItalia n'existe pas encore en base, je le crée, je l'associe...
    //     } else {
    //       $bellitalia = BellItalia::create(array("number" => $data['bellitalia']['number']));
    //       $data['bellitalia_id'] = $bellitalia->id;
    //       //... et on enregistre
    //       $interest = Interest::update($data);
    //     }
    //     // ... et qu'on veut l'associer à une nouvelle région...
    //   } else {
    //     $region = Region::create(array("name" => $data['city']['region']['name']));
    //     $data['region_id'] = $region->id;
    //     // ... et que le Bellitalia associé existe déjà en base
    //     if(isset($request['bellitalia_id'])) {
    //       //... on enregistre
    //       $interest = Interest::update($data);
    //       // Si le BellItalia n'existe pas encore en base, je le crée, je l'associe...
    //     } else {
    //       $bellitalia = BellItalia::create(array("number" => $data['bellitalia']['number']));
    //       $data['bellitalia_id'] = $bellitalia->id;
    //       //... et on enregistre
    //       $interest = Interest::update($data);
    //     }
    //   }
    // }
    // // Si la ville n'est pas en base...
    // else {
    //   // ... et qu'on veut l'associer à une région est déjà en base :
    //   if(isset($request['city']['region_id'])){
    //     $city = City::create(array("name" => $data['city']['name'], "region_id" => $data['city']['region_id']));
    //     $data['city_id'] = $city->id;
    //     // ... et que le Bellitalia associé existe déjà en base
    //     if(isset($request['bellitalia_id'])) {
    //       //... on enregistre
    //       $interest = Interest::update($data);
    //       // Si le BellItalia n'existe pas encore en base, je le crée, je l'associe...
    //     } else {
    //       $bellitalia = BellItalia::create(array("number" => $data['bellitalia']['number']));
    //       $data['bellitalia_id'] = $bellitalia->id;
    //       //... et on enregistre
    //       $interest = Interest::update($data);
    //     }
    //     //... et qu'on veut l'associer à une région n'est pas encore en base :
    //   } else {
    //     $region = Region::create(array("name" => $data['city']['region']['name']));
    //     $data['region_id'] = $region->id;
    //     $city = City::create(array("name" => $data['city']['name'], "region_id" => $region->id));
    //     $data['city_id'] = $city->id;
    //     // ... et que le Bellitalia associé existe déjà en base
    //     if(isset($request['bellitalia_id'])) {
    //       //... on enregistre
    //       $interest = Interest::update($data);
    //       // Si le BellItalia n'existe pas encore en base, je le crée, je l'associe...
    //     } else {
    //       $bellitalia = BellItalia::create(array("number" => $data['bellitalia']['number']));
    //       $data['bellitalia_id'] = $bellitalia->id;
    //       //... et on enregistre
    //       $interest = Interest::update($data);
    //     }
    //   }
    // }
    // // Code 200 : succès requête
    // return response()->json($interest, 200);
  }

  /**
  * Suppression d'une ressource (DELETE)
  *
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function destroy($id)
  {
    $interest = Interest::find($id);
    if(is_null($interest)){
      return response()->json(['message' => 'Not found'], 404);
    }
    $interest->delete();
    // Code 204 : succès requête mais aucune information à envoyer
    return response()->json(null, 204);
  }
}
