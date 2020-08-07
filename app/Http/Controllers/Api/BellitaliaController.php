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
    $rules = [
      'number' => 'numeric',
      'date' => 'required',
      'images.*' => 'required|max:40000000|image',
    ];

    // Messages d'erreur custom
    $messages = [
      'number.numeric' => "Veuillez saisir un numéro de publication valide",
      'date.required' => "Vous devez saisir une date de publication",
      'images.*.required' => "Vous devez associer une couverture à cette publication",
      'images.*.image' => "L'image chargée n'a pas le bon format (jpg, jpeg ou png)",
      'images.*.max' => "L'image chargée dépasse le poids autorisé (4Mo)",
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
    if(isset($data['images']) && !empty($data['images']) && $data['images'] != 'undefined')
    {
      // Je récupère l'image envoyée
      $imageArray = $data['images'];
      // Même s'il n'y a qu'une image envoyée, elle est stockée dans un tableau, donc foreach nécessaire
      foreach ($imageArray as $key => $oneImage) {

        // Je récupère la taille de l'image
        $oneImageSize = filesize($oneImage);

        // Pour la réduction de taille, j'applique un pourcentage différent selon la taille d'origine
        if($oneImageSize <= 4000000 && $oneImageSize >= 2000000) {
          $quality = 70;
        } elseif ($oneImageSize < 2000000 && $oneImageSize >= 1000000) {
          $quality = 60;
        } elseif ($oneImageSize < 1000000 && $oneImageSize >= 500000) {
          $quality = 50;
        } elseif ($oneImageSize < 500000) {
          $quality = 30;
        }

        // On renomme ensuite l'image en évitant toute possibilité de doublons :
        $imageName = 'publication'.trim($data['number']).'-'.date("Ymd-His", strtotime('+2 hours'));

        // Quand tout ça est ok, on peut stocker l'image
        $source = imagecreatefromjpeg($oneImage);
        $imageSave = imagejpeg($source,'./assets/publications/'. $imageName,$quality);
        imagedestroy($source);

        // On stocke l'URL vers l'image associée au point d'intérêt
        $imagePath = url('/assets/publications/'.$imageName);
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
