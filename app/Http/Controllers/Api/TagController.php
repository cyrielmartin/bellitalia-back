<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\Tag;


//Controller exclusivement dédié à l'Api

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

    // Enregistrement des catégories/tags
    if (isset($data['category_id'])) {
      // Il peut y en avoir plusieurs -> tableau
      $tags = array();
      // Pour chacune des catégories sélectionnées
      foreach ($data['category_id'] as $tag) {
        // On récupère le nom
        $t = array("name" => $tag);
        // Et on vérifie s'il existe déjà en base.
        //Si oui, on récupère son id.
        //Si non, on le stocke (en lui créant un ID, donc).
        foreach ($t as $tt) {
          Tag::create($tt)->id;
        }
      }
    }

    return response()->json($tag, 201);

  }

}
