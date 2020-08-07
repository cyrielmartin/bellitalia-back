<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Supplement;
use App\Http\Resources\Supplement as SupplementResource;
use App\BellItalia;
use Validator;

class SupplementController extends Controller
{
  /**
  * Affiche la liste des ressources (GET)
  *
  * @return \Illuminate\Http\Response
  */
  public function index()
  {
    return SupplementResource::collection(Supplement::with(['bellitalia'])->get());
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
      'name' => 'required',
      'publication' => 'required',
      'images.*' => 'required|max:40000000|image'
    ];

    // Messages d'erreur custom
    $messages = [
      'name.required' => "Vous devez donner un nom au supplément",
      'publication.required' => "Vous devez associer le supplément à un numéro de Bell'Italia",
      'images.*.required' => "Vous devez associer une couverture à ce supplément",
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
        $imageName = 'publication'.trim($data['name']).'-'.date("Ymd-His", strtotime('+2 hours'));

        // Quand tout ça est ok, on peut stocker l'image
        $source = imagecreatefromjpeg($oneImage);
        $imageSave = imagejpeg($source,'./assets/supplements/'. $imageName,$quality);
        imagedestroy($source);

        // On stocke l'URL vers l'image associée au point d'intérêt
        $imagePath = url('/assets/supplements/'.$imageName);
        $data['image'] = $imagePath;
      }

      // Association du numéro de Bell'Italia
      if(isset($data['publication'])) {
        $bellitalia = BellItalia::firstOrCreate(array("number" => $data['publication']));
        $data['bellitalia_id'] = $bellitalia->id;
      }

      // Une fois que tout ça est fait, on peut enregistrer le supplément en base.
      $supplement = new Supplement($data);
      $supplement->save();

      return response()->json($supplement, 201);

    }
  }

  /**
  * Affichage d'une ressource (GET)
  *
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function show($id)
  {
    return new SupplementResource(Supplement::FindOrFail($id));
  }
}
