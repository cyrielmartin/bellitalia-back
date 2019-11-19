<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\Interest;
use App\City;
use App\Region;
use App\Bellitalia;
use App\Tag;

//Controller exclusivement dédié à l'Api

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
    // Je mets ici mes règles de validation du formulaire :
    $rules = [
      'name' => 'required',
      'latitude' => 'numeric',
      'longitude' => 'numeric',
      'city_id' => 'required',
      'region_id' => 'required',
      'bellitalia_id' => 'required',
      'publication' => 'required',
    ];

    // Messages d'erreur custom
    $messages = [
      'name.required' => "Veuillez saisir un nom",
      'latitude.numeric' => "Veuillez saisir une latitude",
      'longitude.numeric' => "Veuillez saisir une longitude",
      'city_id.required' => "Veuillez saisir un nom de ville",
      'region_id.required' => "Veuillez sélectionner une région",
      'bellitalia_id.required' => "Veuillez saisir un numéro de Bell'Italia",
      'publication.required' => "Veuillez saisir un numéro de publication",
    ];

    // J'applique le Validator à toutes les requêtes envoyées.
    $validator = Validator::make($request->all(), $rules, $messages);
    // Si 1 des règles de validation n'est pas respectée
    if($validator->fails()){
      //code 400 : syntaxe requête erronée
      return response()->json($validator->errors(), 400);
    }

    // Bonne pratique : on ne modifie pas directement la requête.
    $data = $request->all();

    // Enregistrement des régions et des villes nouvelles
    if(isset($data['city_id'])) {
      if(isset($data['region_id'])){

        $region = Region::firstOrCreate(array("name" => $data['region_id']));
        $data['region_id'] = $region->id;

        $city = City::firstOrCreate(array("name" => $data['city_id'], "region_id" => $region->id));
        $data['city_id'] = $city->id;
      }
    }

    // Enregistrement des BellItalia nouveaux (numéros + publication)
    if(isset($data['bellitalia_id'])) {
      if(isset($data['publication'])) {
        // Pour la date de publication, transformation nécessaire du format pour BDD
        // Et ajout d'un jour car date renvoyée par vue monthly picker est à J-1
        $formattedDate  = date('Y-m-d', strtotime($data['publication']. ' +1 day'));
        $bellitalia = BellItalia::firstOrCreate(array("number" => $data['bellitalia_id'], "publication" => $formattedDate));
        $data['bellitalia_id'] = $bellitalia->id;
      }
    }

    // Enregistrement des tags (catégories) nouvellement créés
    if (isset($data['tag_id'])) {
      // Pour chacun des tags récupérés ici
      foreach ($data['tag_id'] as $tag) {
        // On formatte le tag comme la BDD l'attend : name : xxx
        // On le stocke dans un tableau
        $formattedTag = ["name" => $tag];
        // Stockage en BDD via un mass assignement :
        // envoi direct d'un tableau en BDD
        // attention, bien rendre fillable "name" dans model
        // firstOrCreate important pour éviter doublons
        $tags[] = Tag::firstOrCreate($formattedTag)->id;
      }

      // Une fois que tout ça est fait, on peut enregistrer l'Interest en base.
      $interest = new Interest($data);
      $interest->save();
      // Et on n'oublie pas d'associer les catégories à l'intérêt qui vient d'être créé
      // (uniquement si au moins 1 tag a été ajouté)
      if(isset($tags)) {
        $interest->tags()->sync($tags);
      }
    }

    // Code 201 : succès requête et création ressource
    return response()->json($interest, 201);
  }

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
    //TODO

    // $interest = Interest::find($id);
    // if(is_null($interest)){
    //   return response()->json(['message' => 'Not found'], 404);
    // }

    // // Je mets ici mes règles de validation du formulaire :
    //     $rules = [
    //         'name' => 'required|string',
    //         'latitude' => 'required',
    //         'longitude' => 'required',
    //         'city_id' => 'required',
    //         'region_id' => 'required',
    //         'bellitalia_id' => 'required|integer',
    //         'publication' => 'required',
    //       ];

    //       // J'applique le Validator à toutes les requêtes envoyées.
    //       $validator = Validator::make($request->all(), $rules);
    //       // Si moindre souci : 404.
    //       if($validator->fails()){
    //         //code 400 : syntaxe requête erronée
    //         return response()->json($validator->errors(), 400);
    //       }

    //       // Bonne pratique : on ne modifie pas directement la requête.
    //       $data = $request->all();

    //       // Enregistrement des catégories nouvelles
    //       // TODO Association avec Interest ?
    //       if(isset($data['category_id'])) {
    //         $category = Tag::updateOrCreate(array("name" => $data['category_id']));
    //         $data['category_id'] = $category->id;
    //       }

    //       // Enregistrement des régions et des villes nouvelles
    //       if(isset($data['city_id'])) {
    //         if(isset($data['region_id'])){

    //           $region = Region::updateOrCreate(array("name" => $data['region_id']));
    //           $data['region_id'] = $region->id;

    //           $city = City::updateOrCreate(array("name" => $data['city_id'], "region_id" => $region->id));
    //           $data['city_id'] = $city->id;
    //         }
    //       }

    //       // Enregistrement des BellItalia nouveaux (numéros + publication)
    //       // TODO formattage date month only ?
    //       if(isset($data['bellitalia_id'])) {
    //         if(isset($data['publication'])) {

    //           $bellitalia = BellItalia::updateOrCreate(array("number" => $data['bellitalia_id'], "publication" => $data['publication']));
    //           $data['bellitalia_id'] = $bellitalia->id;
    //         }
    //       }
    //       // Enregistrement de l'interest
    //       $interest = Interest::update($data);

    //       // Code 201 : succès requête et modification ressource
    //       return response()->json($interest, 204);

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
