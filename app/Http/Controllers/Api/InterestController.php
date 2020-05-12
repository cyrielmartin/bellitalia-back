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
    // Règles de validation du formulaire :
    $rules = [
      'name' => 'required',
      'latitude' => 'numeric',
      'longitude' => 'numeric',
      'city_id' => 'required',
      'region_id' => 'required',
      'bellitalia_id' => 'required',
      'tag_id' => 'required',
      'image' => 'max:30000000|image64:jpg,jpeg,png',
    ];

    // Messages d'erreur custom
    $messages = [
      'name.required' => "Veuillez saisir un nom",
      'latitude.numeric' => "Veuillez saisir une latitude valide",
      'longitude.numeric' => "Veuillez saisir une longitude valide",
      'city_id.required' => "Veuillez saisir un nom de ville",
      'region_id.required' => "Veuillez sélectionner une région",
      'bellitalia_id.required' => "Veuillez saisir un numéro de Bell'Italia",
      'tag_id.required' => "Veuillez sélectionner au moins une catégorie",
      'image.max' => "L'image dépasse le poids autorisé (30Mo)",
      'image.image64' => "L'image doit être au format jpg, jpeg ou png"
    ];

    // J'applique le Validator à toutes les requêtes envoyées.
    $validator = Validator::make($request->all(), $rules, $messages);
    // Si 1 des règles de validation n'est pas respectée
    if($validator->fails()){
      //code 400 : syntaxe requête erronée
      return response()->json($validator->errors(), 400);
    }

    $data = $request->all();
    // Si une image est envoyée
    if($request->get('image'))
    {
      // On la renomme et on la stocke
      $image = $request->get('image');
      $name = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
      \Image::make($request->get('image'))->save('./assets/interests/'. $name);

      // On stocke l'URL vers l'image
      $imagePath = url('/assets/interests/'.$name);
      $data['image'] = $imagePath;
    }
    // Enregistrement et association des régions et des villes nouvelles
    if(isset($data['city_id']['name'])) {
      if(isset($data['region_id']['name'])){

        $region = Region::firstOrCreate(array("name" => $data['region_id']['name']));
        $data['region_id'] = $region->id;

        $city = City::firstOrCreate(array("name" => $data['city_id']['name'], "region_id" => $region->id));
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
      'tag_id' => 'required',
      'image' => 'max:30000000',
    ];

    // Messages d'erreur custom
    $messages = [
      'name.required' => "Veuillez saisir un nom d'intérêt",
      'latitude.numeric' => "Veuillez saisir une latitude valide",
      'longitude.numeric' => "Veuillez saisir une longitude valide",
      'city_id.required' => "Veuillez saisir un nom de ville",
      'region_id.required' => "Veuillez sélectionner une région",
      'bellitalia_id.required' => "Veuillez saisir un numéro de Bell'Italia",
      'tag_id.required' => "Veuillez sélectionner au moins une catégorie",
      'image.file' => "L'image dépasse le poids autorisé (30Mo)",
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

    // Si une image est envoyée
    if($request->get('image'))

    // On vérifie que l'image envoyée n'est pas déjà celle en base.
    // On ne traite l'image envoyée que si ce n'est pas le cas
    // Au final, on ne rentre dans cette condition que si l'image envoyée est nouvelle
    if($interest->image != $data['image']) {
      {
        // On la renomme
        $image = $request->get('image');
        $name = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
        \Image::make($request->get('image'))->save('./assets/interests/'. $name);

        // On stocke l'URL vers l'image
        $imagePath = url('/assets/interests/'.$name);
        $data['image'] = $imagePath;
      }
    }

    // Enregistrement et association des régions et des villes nouvelles
    if(isset($data['city_id']['name'])) {
      if(isset($data['region_id']['name'])){

        $region = Region::firstOrCreate(array("name" => $data['region_id']['name']));
        $data['region_id'] = $region->id;

        $city = City::firstOrCreate(array("name" => $data['city_id']['name'], "region_id" => $region->id));
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
