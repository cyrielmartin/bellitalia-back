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
// use \Gumlet\ImageResize;

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

    return InterestResource::collection(Interest::with(['supplement'])->get());
    // return TagResource::collection(Tag::with(['interests'])->get());

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
      'images.*' => 'max:80000000|image',
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
      'images.*.image' => "Au moins une image n'a pas le bon format (jpg, jpeg ou png)",
      'images.*.max' => "Au moins une image dépasse le poids autorisé (8Mo)",
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
    if(isset($data['bellitalia_id']) && !empty($data['bellitalia_id']) && $data['bellitalia_id'] != 'undefined') {
      $bellitalia = BellItalia::firstOrCreate(array("number" => $data['bellitalia_id']));
      $data['bellitalia_id'] = $bellitalia->id;
    } else {
      $data['bellitalia_id'] = null;
    }

    // Association avec le supplément, s'il est défini
    if(isset($data['supplement_id']) && !empty($data['supplement_id']) && $data['supplement_id'] != 'undefined') {
      // Côté front, je suis obligé d'associer le numéro (et non l'id) de la publication à bellitalia_id.
      // Pour enregistrer correctement l'interest, je dois donc récupérer l'id correspondant à ce numéro.
      $bellitalia = BellItalia::firstOrCreate(array("number" => $data['supplement_id']['bellitalia_id']));
      // Seulement ensuite, je peux enregistrer le supplément.
      $supplement = Supplement::firstOrCreate(array("name" => $data['supplement_id']['name'], "bellitalia_id" => $bellitalia->id));
      $data['supplement_id'] = $supplement->id;
      // S'il n'y a pas de supplément, j'envoie du null sinon pb Array to String Conversion.
    } else {
      $data['supplement_id'] = null;
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
    }

    // Une fois que tout ça est fait, on peut enregistrer l'Interest en base.
    $interest = new Interest($data);
    $interest->save();

    // Si au moins une image lui a été associée :
    // if($request->get('images'))
    if(isset($data['images']) && !empty($data['images']) && $data['images'] != 'undefined')
    {
      // Je récupère les images envoyées
      $imageArray = $data['images'];
      dd($imageArray);
      // Pour chacune d'entre elles :
      // dd($imageArray);
      foreach ($imageArray as $key => $oneImage) {
        // Réduction taille images
        // WIP : à affiner en fonction de la taille d'origine
        $imageDataEncoded = base64_encode(file_get_contents($oneImage));
        $imageData = base64_decode($imageDataEncoded);
        $source = imagecreatefromstring($imageData);
        // $angle = 90;
        // $rotate = imagerotate($source, $angle, 0); // if want to rotate the image
        $imageName = trim(mb_strtolower(preg_replace("/[^A-Za-z0-9]/", '', $interest->name))).$key.'-'.date("Ymd-His", strtotime('+2 hours')).'.' . explode('/', explode(':', substr($oneImage, 0, strpos($oneImage, ';')))[1])[1];
        // dd($imageName);
        // $target_file = './assets/interests/'. $imageName;
        $quality = 50;
        $imageSave = imagejpeg($source,'./assets/interests/'. $imageName,$quality);
        // \Image::make(imagejpeg($rotate,$imageName,100))->save('./assets/interests/'. $imageName);

        imagedestroy($source);

        // On la renomme en évitant toute possibilité de doublons :
        // Nom du point d'intérêt + index + date + heure
        // On fait bien attention de "nettoyer" le nom du point d'intérêt pour éviter tout pb dans la base :
        // Pas d'espace, en minuscule, pas d'accent ou de caractères spéciaux (s'il y en a, la lettre est supprimée)
        // $name = trim(mb_strtolower(preg_replace("/[^A-Za-z0-9]/", '', $interest->name))).$key.'-'.date("Ymd-His", strtotime('+2 hours')).'.' . explode('/', explode(':', substr($oneImage, 0, strpos($oneImage, ';')))[1])[1];
        // \Image::make($imageSave)->save('./assets/interests/'. $imageName);

        // On stocke l'URL vers l'image associée au point d'intérêt
        $imagePath = url('/assets/interests/'.$imageName);
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
      'image' => 'max:5000000|image64:jpg,jpeg,png',
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
      'image.max' => "L'image dépasse le poids autorisé (5Mo)",
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
    if(!empty($data['bellitalia_id'])) {
      $bellitalia = BellItalia::firstOrCreate(array("number" => $data['bellitalia_id']['number']));
      $data['bellitalia_id'] = $bellitalia->id;
    } else {
      $data['bellitalia_id'] = null;
    }

    // Association avec le supplément, s'il est défini
    if(!empty($data['supplement_id'])) {
      // Côté front, je suis obligé d'associer le numéro (et non l'id) de la publication à bellitalia_id.
      // Pour enregistrer correctement l'interest, je dois donc récupérer l'id correspondant à ce numéro.
      $bellitalia = BellItalia::firstOrCreate(array("number" => $data['supplement_id']['bellitalia_id']));
      // Seulement ensuite, je peux enregistrer le supplément.
      $supplement = Supplement::firstOrCreate(array("name" => $data['supplement_id']['name'], "bellitalia_id" => $bellitalia->id));
      $data['supplement_id'] = $supplement->id;
    } else {
      $data['supplement_id'] = null;
    }

    // Association des tags (catégories)
    if (isset($data['tag_id'])) {
      // Pour chacun des tags récupérés ici
      foreach ($data['tag_id'] as $tag) {
        // On formatte le tag comme la BDD l'attend : name : xxx
        // On le stocke dans un tableau
        $formattedTag = ["name" => $tag['name']];

        // Stockage en BDD via un mass assignement :
        // envoi direct d'un tableau en BDD
        // attention, bien rendre fillable "name" dans model
        // firstOrCreate important pour éviter doublons
        $tags[] = Tag::firstOrCreate($formattedTag)->id;
      }
    }
    // Une fois que tout ça est fait, on peut mettre à jour l'Interest en base.
    // Pour une raison que j'ignore, je suis obligé de préciser chaque propriété qui doit être mise à jour, sinon 'error to string conversion'
    // Du coup, j'en profite pour ne pas remettre à jour les champs adresse, latitude et longitude -> sécurité supplémentaire
    $interest->update([
      'name' => $data['name'],
      'description' => $data['description'],
      'link' => $data['link'],
      'bellitalia_id' => $data['bellitalia_id'],
      'supplement_id' => $data['supplement_id'],
    ]);

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
