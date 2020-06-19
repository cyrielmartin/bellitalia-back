<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\Http\Resources\Interest as InterestResource;
use App\Interest;
use App\Bellitalia;
use App\Supplement;
use App\Tag;
use App\Image;

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
      'bellitalia_id' => 'required_without:supplement_id',
      'supplement_id' => 'required_without:bellitalia_id',
      'tag_id' => 'required',
      'image' => 'max:30000000|image64:jpg,jpeg,png',
      'address' => 'required'
    ];

    // Messages d'erreur custom
    $messages = [
      'name.required' => "Veuillez saisir un nom",
      'latitude.numeric' => "Veuillez saisir une latitude valide",
      'longitude.numeric' => "Veuillez saisir une longitude valide",
      'bellitalia_id.required_without' => "Veuillez définir un numéro de Bell'Italia ou un supplément",
      'supplement_id.required_without' => "Veuillez définir un numéro de Bell'Italia ou un supplément",
      'tag_id.required' => "Veuillez sélectionner au moins une catégorie",
      'image.max' => "L'image dépasse le poids autorisé (30Mo)",
      'image.image64' => "L'image doit être au format jpg, jpeg ou png",
      'address.required' => "Veuillez saisir une adresse valide"
    ];

    // J'applique le Validator à toutes les requêtes envoyées.
    $validator = Validator::make($request->all(), $rules, $messages);
    // Si 1 des règles de validation n'est pas respectée
    if($validator->fails()){
      //code 400 : syntaxe requête erronée
      return response()->json($validator->errors(), 400);
    }
    $data = $request->all();

    // Association du numéro de Bell'Italia, s'il est défini
    if(isset($data['bellitalia_id'])) {
      $bellitalia = BellItalia::firstOrCreate(array("number" => $data['bellitalia_id']['number']));
      $data['bellitalia_id'] = $bellitalia->id;
    }

    // Association avec le supplément, s'il est défini
    if(isset($data['supplement_id'])) {
      // Côté front, je suis obligé d'associer le numéro (et non l'id) de la publication à bellitalia_id.
      // Pour enregistrer correctement l'interest, je dois donc récupérer l'id correspondant à ce numéro.
      $bellitalia = BellItalia::firstOrCreate(array("number" => $data['supplement_id']['bellitalia_id']));
      // Seulement ensuite, je peux enregistrer le supplément.
      $supplement = Supplement::firstOrCreate(array("name" => $data['supplement_id']['name'], "bellitalia_id" => $bellitalia->id));
      $data['supplement_id'] = $supplement->id;
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

      // Si au moins une image lui a été associée :
      if($request->get('image'))
      {
        // Je récupère les images envoyées
        $imageArray = $request->get('image');
        // Pour chacune d'entre elles :
        foreach ($imageArray as $key => $oneImage) {
          // On la renomme en évitant toute possibilité de doublons :
          // Nom du point d'intérêt + index + date + heure
          // On fait bien attention de "nettoyer" le nom du point d'intérêt pour éviter tout pb dans la base :
          // Pas d'espace, en minuscule, pas d'accent ou de caractères spéciaux (s'il y en a, la lettre est supprimée)
          $name = trim(mb_strtolower(preg_replace("/[^A-Za-z0-9]/", '', $interest->name))).$key.'-'.date("Ymd-His", strtotime('+2 hours')).'.' . explode('/', explode(':', substr($oneImage, 0, strpos($oneImage, ';')))[1])[1];
          \Image::make($oneImage)->save('./assets/interests/'. $name);

          // On stocke l'URL vers l'image associée au point d'intérêt
          $imagePath = url('/assets/interests/'.$name);
          $interest->images()->create([
            'url' => $imagePath,
          ]);
        }
      }
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
      'bellitalia_id' => 'required_without:supplement_id',
      'supplement_id' => 'required_without:bellitalia_id',
      'tag_id' => 'required',
      'image' => 'max:30000000|image64:jpg,jpeg,png',
      'address' => 'required'
    ];

    // Messages d'erreur custom
    $messages = [
      'name.required' => "Veuillez saisir un nom d'intérêt",
      'latitude.numeric' => "Veuillez saisir une latitude valide",
      'longitude.numeric' => "Veuillez saisir une longitude valide",
      'bellitalia_id.required_without' => "Veuillez définir un numéro de Bell'Italia ou un supplément",
      'supplement_id.required_without' => "Veuillez définir un numéro de Bell'Italia ou un supplément",
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

    // Association du numéro de Bell'Italia
    if(isset($data['bellitalia_id'])) {
      $bellitalia = BellItalia::firstOrCreate(array("number" => $data['bellitalia_id']['number']));
      $data['bellitalia_id'] = $bellitalia->id;
    }

    // Association avec le supplément, s'il est défini
    if(isset($data['supplement_id'])) {
      // Côté front, je suis obligé d'associer le numéro (et non l'id) de la publication à bellitalia_id.
      // Pour enregistrer correctement l'interest, je dois donc récupérer l'id correspondant à ce numéro.
      $bellitalia = BellItalia::firstOrCreate(array("number" => $data['supplement_id']['bellitalia_id']));
      // Seulement ensuite, je peux enregistrer le supplément.
      $supplement = Supplement::firstOrCreate(array("name" => $data['supplement_id']['name'], "bellitalia_id" => $bellitalia->id));
      $data['supplement_id'] = $supplement->id;
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

      // Si au moins une image lui a été associé :
      if($request->get('image'))
      {
        // Je récupère les images envoyées
        $imageArray = $request->get('image');

        // Je récupère les images (url) déjà stockées en BDD
        $storedUrl = $interest->images()->get('url')->all();

        // Je les stocke dans un tableau
        $storedUrlArray = array();
        foreach ($storedUrl as $oneUrl) {
          array_push($storedUrlArray, $oneUrl['url']);
        }

        // Si les images stockées ne correspondent pas à celles envoyées
        if($imageArray !== $storedUrlArray) {

          // Je supprime toutes les associations image-point d'intérêt
          $interest->images()->delete();

          // Et je stocke chacune des images envoyées comme dans le Store
          foreach ($imageArray as $key => $oneImage) {
            $name = trim(mb_strtolower(preg_replace("/[^A-Za-z0-9]/", '', $interest->name))).$key.'-'.date("Ymd-His", strtotime('+2 hours')).'.' . explode('/', explode(':', substr($oneImage, 0, strpos($oneImage, ';')))[1])[1];
            \Image::make($oneImage)->save('./assets/interests/'. $name);

            // On stocke l'URL vers l'image associée au point d'intérêt
            $imagePath = url('/assets/interests/'.$name);
            $interest->images()->create([
              'url' => $imagePath,
            ]);
          }
        }
        // Si aucune image n'est envoyée, je supprime toutes les associations images-points d'intérêt
      } else {
        $interest->images()->delete();
      }

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
