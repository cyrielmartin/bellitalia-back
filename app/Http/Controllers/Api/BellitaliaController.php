<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Bellitalia;

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
    // Récupération requête
    $data = $request->all();

    // Enregistrement du Bellitalia nouvellement créé
    if (isset($data['number'])) {
      if(isset($data['date'])) {
        // Formattage de la date pour BDD -> je ne prends que le mois et l'année
        $formattedDate  = date('Y-m', strtotime($data['date']));
        // Je force le jour à 01 comme je ne peux pas utiliser de MonthPicker en front
        $forcedFirstDayDate = $formattedDate.'-01';
        //firstOrCreate pour éviter tout doublon accidentel
        //(même si normalement doublons rendus impossibles par Vue Multiselect)
        $bellitalia = BellItalia::firstOrCreate(array("number" => $data['number'], "publication" => $forcedFirstDayDate));
      }
    }
    return response()->json($bellitalia, 201);

  }
}
