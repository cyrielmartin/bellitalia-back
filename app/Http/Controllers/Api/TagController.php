<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
    // code 200 : succès de la requête
    return response()->json(Tag::get(), 200);
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

}
