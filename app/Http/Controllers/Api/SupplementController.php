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
    // La règle image64 est une règle custom définie dans App\Providers\AppServiceProvider
    $rules = [
      'name' => 'required',
      'publication' => 'required',
      'image' => 'required|max:30000000|image64:jpg,jpeg,png',
    ];

    // Messages d'erreur custom
    $messages = [
      'name.required' => "Vous devez donner un nom au supplément",
      'publication.required' => "Vous devez associer le supplément à un numéro de Bell'Italia",
      'image.required' => "Vous devez associer une couverture au supplément",
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
        $name = 'supplement'.trim($data['name']).'-'.date("Ymd-His", strtotime('+2 hours')).'.' . explode('/', explode(':', substr($oneImage, 0, strpos($oneImage, ';')))[1])[1];
        \Image::make($oneImage)->save('./assets/supplements/'. $name);

        // On stocke l'URL vers l'image associée au point d'intérêt
        $imagePath = url('/assets/supplements/'.$name);
        $data['image'] = $imagePath;
      }

      // Association du numéro de Bell'Italia
      if(isset($data['publication']['number'])) {
        $bellitalia = BellItalia::firstOrCreate(array("number" => $data['publication']['number']));
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
