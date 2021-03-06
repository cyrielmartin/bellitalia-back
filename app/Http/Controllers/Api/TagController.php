<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Tag as TagResource;
use Validator;
use App\Tag;


// TagController exclusivement dédié à l'Api

class TagController extends Controller
{
  /**
  * Affiche la liste des ressources (GET)
  *
  * @return \Illuminate\Http\Response
  */
  public function index()
  {
    return TagResource::collection(Tag::with(['interests'])->get());
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

    // Enregistrement des tags (catégories) nouvellement créés
    if (isset($data['tag_id'])) {
      // Pour chacun des tags récupérés ici
      foreach ($data['tag_id'] as $tag) {
        // On formatte le tag comme la BDD l'attend : name : xxx
        // On le stocke dans un tableau
        $formattedTag = ["name" => $tag];
        // On stocke chacun de ces tags en BDD
        foreach ($formattedTag as $newTag) {
          //firstOrCreate pour éviter tout doublon accidentel
          //(même si normalement doublons rendus impossibles par Vue Multiselect)
          Tag::firstOrCreate($newTag)->id;
        }
      }
    }

    return response()->json($tag, 201);

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
    $tag = Tag::FindOrFail($id);
    if(is_null($tag)){
      return response()->json(['message' => 'Tag Not found'], 404);
    }

    // Règles de validation :
    $rules = [
      'name' => 'required',
    ];

    // Messages d'erreur custom
    $messages = [
      'name.required' => "Veuillez saisir un nom",
    ];

    // J'applique le Validator à toutes les requêtes envoyées.
    $validator = Validator::make($request->all(), $rules, $messages);
    // Si 1 des règles de validation n'est pas respectée
    if($validator->fails()){
      //code 400 : syntaxe requête erronée
      return response()->json($validator->errors(), 400);
    }


    // Récupération requête
    $data = $request->all();

    if (isset($data['name'])) {
      $tag->update($data);
    }
    return response()->json($tag, 201);

  }
  /**
  * Affichage d'une ressource (GET)
  *
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function show($id)
  {
    return new TagResource(Tag::FindOrFail($id));
  }

  /**
  * Suppression d'une ressource (DELETE)
  *
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function destroy($id)
  {
    $tag = Tag::findOrFail($id);
    if(is_null($tag)){
      return response()->json(['message' => 'Not found'], 404);
    }
    $tag->delete();
    // Code 204 : succès requête mais aucune information à envoyer
    return response()->json(null, 204);
  }

}
