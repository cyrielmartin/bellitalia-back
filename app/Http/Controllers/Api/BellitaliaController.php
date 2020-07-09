<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Bellitalia;
use App\Http\Resources\Bellitalia as BellItaliaResource;

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
    // return BellItaliaResource::collection(Bellitalia::with(['supplements'])->get());

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
    // La règle image64 est une règle custom définie dans App\Providers\AppServiceProvider
    $rules = [
      'number' => 'numeric',
      'date' => 'required',
      'image' => 'required|max:30000000|image64:jpg,jpeg,png',
    ];

    // Messages d'erreur custom
    $messages = [
      'number.numeric' => "Veuillez saisir un numéro de publication valide",
      'date.required' => "Vous devez saisir une date de publication",
      'image.required' => "Vous devez associer une couverture à cette publication",
      'image.max' => "L'image dépasse le poids autorisé (30Mo)",
      'image.image64' => "L'image doit être au format jpg, jpeg ou png",
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

    // Si une image est envoyée
    if($request->get('image'))
    {
      // On la renomme et on la stocke
      $imageArray = $request->get('image');
      // Même s'il n'y a qu'une image envoyée, elle est stockée dans un tableau, donc foreach nécessaire
      foreach ($imageArray as $key => $oneImage) {
        // On la renomme en évitant toute possibilité de doublons :
        // Nom du point d'intérêt + index + date + heure
        // On fait bien attention de "nettoyer" le nom du point d'intérêt pour éviter tout pb dans la base :
        // Pas d'espace, en minuscule, pas d'accent ou de caractères spéciaux (s'il y en a, la lettre est supprimée)
        $name = 'publication'.trim($data['number']).'-'.date("Ymd-His", strtotime('+2 hours')).'.' . explode('/', explode(':', substr($oneImage, 0, strpos($oneImage, ';')))[1])[1];
        \Image::make($oneImage)->save('./assets/publications/'. $name);

        // On stocke l'URL vers l'image associée au point d'intérêt
        $imagePath = url('/assets/publications/'.$name);
        $data['image'] = $imagePath;
      }
    }

    // Enregistrement du Bellitalia nouvellement créé
    if (isset($data['number'])) {
      if(isset($data['date'])) {
        // Formattage de la date pour BDD :
        // J'ajoute 1 jour (bizarrement, toutes les dates renvoyées par le front sont à J-1)
        $date = $data['date'];
        $formattedDate  = date('Y-m-d', strtotime($date . ' +1 day'));
        //firstOrCreate pour éviter tout doublon accidentel
        //(même si normalement doublons rendus impossibles par Vue Multiselect)
        $bellitalia = BellItalia::firstOrCreate(array("number" => $data['number'], "publication" => $formattedDate, "image" => $imagePath));
      }
    }
    return response()->json($bellitalia, 201);

  }

  /**
  * Affichage d'une ressource (GET)
  *
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function show($id)
  {

    return new BellItaliaResource(BellItalia::FindOrFail($id));

  }
}
