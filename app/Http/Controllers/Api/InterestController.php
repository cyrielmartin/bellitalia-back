<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\Http\Resources\Interest as InterestResource;
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

    return InterestResource::collection(Interest::get());
    // return response()->json(InterestResource::all(), 200);
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
    ];

    // Messages d'erreur custom
    $messages = [
      'name.required' => "Veuillez saisir un nom",
      'latitude.numeric' => "Veuillez saisir une latitude",
      'longitude.numeric' => "Veuillez saisir une longitude",
      'city_id.required' => "Veuillez saisir un nom de ville",
      'region_id.required' => "Veuillez sélectionner une région",
      'bellitalia_id.required' => "Veuillez saisir un numéro de Bell'Italia",
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

    // Enregistrement et association des régions et des villes nouvelles
    if(isset($data['city_id'])) {
      if(isset($data['region_id'])){

        $region = Region::firstOrCreate(array("name" => $data['region_id']));
        $data['region_id'] = $region->id;

        $city = City::firstOrCreate(array("name" => $data['city_id'], "region_id" => $region->id));
        $data['city_id'] = $city->id;
      }
    }

    // Association du numéro de Bell'Italia
    if(isset($data['bellitalia_id'])) {
      $bellitalia = BellItalia::firstOrCreate(array("number" => $data['bellitalia_id']));
      $data['bellitalia_id'] = $bellitalia->id;
    }

    // Association des tags (catégories)
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

    return new InterestResource(Interest::FindOrFail($id));

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

    $interest = Interest::FindOrFail($id);

    if(is_null($interest)){
      return response()->json(['message' => 'Interest Not found'], 404);
    }

    // Je mets ici mes règles de validation du formulaire :
    $rules = [
      'name' => 'required',
      'latitude' => 'numeric',
      'longitude' => 'numeric',
      'city_id' => 'required',
      'region_id' => 'required',
      'bellitalia_id' => 'required',
    ];

    // Messages d'erreur custom
    $messages = [
      'name.required' => "Veuillez saisir un nom",
      'latitude.numeric' => "Veuillez saisir une latitude",
      'longitude.numeric' => "Veuillez saisir une longitude",
      'city_id.required' => "Veuillez saisir un nom de ville",
      'region_id.required' => "Veuillez sélectionner une région",
      'bellitalia_id.required' => "Veuillez saisir un numéro de Bell'Italia",
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
    // Enregistrement et association des régions et des villes nouvelles
    if(isset($data['city_id'])) {
      if(isset($data['region_id'])){

        $region = Region::firstOrCreate(array("name" => $data['region_id']));
        $data['region_id'] = $region->id;

        $city = City::firstOrCreate(array("name" => $data['city_id'], "region_id" => $region->id));
        $data['city_id'] = $city->id;
      }
    }

    // Association du numéro de Bell'Italia
    if(isset($data['bellitalia_id'])) {
      $bellitalia = BellItalia::firstOrCreate(array("number" => $data['bellitalia_id']));
      $data['bellitalia_id'] = $bellitalia->id;
    }

    // Association des tags (catégories)
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
      $interest->update($data);
      // Et on n'oublie pas d'associer les catégories à l'intérêt qui vient d'être créé
      // Si aucun tag n'est envoyé, on envoie un tableau vide
      if(isset($tags)) {
        $interest->tags()->sync($tags);
      } else {
        $interest->tags()->sync([]);
      }
    }

    // Code 201 : succès requête et création ressource
    return response()->json($interest, 201);
  }

  /**
  * Suppression d'une ressource (DELETE)
  *
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function destroy($id)
  {
    $interest = Interest::findOrFail($id);
    if(is_null($interest)){
      return response()->json(['message' => 'Not found'], 404);
    }
    $interest->delete();
    // Code 204 : succès requête mais aucune information à envoyer
    return response()->json(null, 204);
  }
}
