<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Bellitalia;
use Validator;

class BellitaliaController extends Controller
{
  /**
  * Affiche la liste des ressources (GET)
  *
  * @return \Illuminate\Http\Response
  */
  public function index()
  {
    // code 200 : succès de la requête
    return response()->json(Bellitalia::get(), 200);
  }

  /**
  * Enregistrement d'une nouvelle ressource (POST)
  *
  * @param  \Illuminate\Http\Request  $request
  * @return \Illuminate\Http\Response
  */
  public function store(Request $request)
  {
    // Règles de validation :
    $rules = [
      'number' => 'numeric',
      'date' => 'required'
    ];

    // Messages d'erreur custom
    // (même si normalement, vérif en front rendent impossible l'arrivée de lettres ici)
    $messages = [
      'number.numeric' => "Veuillez saisir un numéro de publication valide",
      'date.required' => "Vous devez saisir une date de publication"
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

    // Enregistrement du Bellitalia nouvellement créé
    if (isset($data['number'])) {
      if(isset($data['date'])) {
        // Formattage de la date pour BDD :
        // J'ajoute 1 jour (bizarrement, toutes les dates renvoyées par le front sont à J-1)
        $date = $data['date'];
        $formattedDate  = date('Y-m-d', strtotime($date . ' +1 day'));
        //firstOrCreate pour éviter tout doublon accidentel
        //(même si normalement doublons rendus impossibles par Vue Multiselect)
        $bellitalia = BellItalia::firstOrCreate(array("number" => $data['number'], "publication" => $formattedDate, "image" => null));
      }
    }
    return response()->json($bellitalia, 201);

  }
}
