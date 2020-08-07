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
      'address' => 'required',
      'latitude' => 'numeric|required',
      'longitude' => 'numeric|required',
      'bellitalia_id' => 'required_without:supplement_id',
      'supplement_id' => 'required_without:bellitalia_id',
      'tag_id' => 'required',
      'images.*' => 'max:40000000|image',
    ];

    // Messages d'erreur custom
    $messages = [
      'name.required' => "Veuillez saisir un nom",
      'address.required' => "Veuillez saisir une adresse",
      'latitude.numeric' => "Veuillez saisir une latitude valide",
      'latitude.required' => "Veuillez saisir une latitude",
      'longitude.numeric' => "Veuillez saisir une longitude valide",
      'longitude.required' => "Veuillez saisir une longitude",
      'bellitalia_id.required_without' => "Veuillez définir un numéro de Bell'Italia ou un supplément",
      'supplement_id.required_without' => "Veuillez définir un numéro de Bell'Italia ou un supplément",
      'tag_id.required' => "Veuillez sélectionner au moins une catégorie",
      'images.*.image' => "Au moins une image n'a pas le bon format (jpg, jpeg ou png)",
      'images.*.max' => "Au moins une image dépasse le poids autorisé (4Mo)",
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

      // En front, j'ai envoyé le numéro du BellItalia correspondant juste pour pouvoir le récupérer ici (pour enregistrement supplément)
      $supplement = Supplement::firstOrCreate(array("name" => $data['supplement_id'], "bellitalia_id" => $data['bellitalia_id']));
      $data['supplement_id'] = $supplement->id;
      // Mais à peine je m'en suis servi que je remets le numéro du BI à null sinon double association publication/supplement. 
      $data['bellitalia_id'] = null;
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
    if(isset($data['images']) && !empty($data['images']) && $data['images'] != 'undefined')
    {
      // Je récupère les images envoyées
      $imageArray = $data['images'];

      // Pour chacune d'entre elles :
      foreach ($imageArray as $key => $oneImage) {
        // Je récupère leur taille
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

        // On renomme les images en évitant toute possibilité de doublons :
        // Nom du point d'intérêt + index + date + heure
        // On fait bien attention de "nettoyer" le nom du point d'intérêt pour éviter tout pb dans la base :
        // Pas d'espace, en minuscule, pas d'accent ou de caractères spéciaux (s'il y en a, la lettre est supprimée)
        $imageName = trim(mb_strtolower(preg_replace("/[^A-Za-z0-9]/", '', $interest->name))).$key.'-'.date("Ymd-His", strtotime('+2 hours'));

        // Quand tout ça est ok, on peut stocker les images
        $source = imagecreatefromjpeg($oneImage);
        $imageSave = imagejpeg($source,'./assets/interests/'. $imageName,$quality);
        imagedestroy($source);

        // Et enfin on stocke l'URL vers la ou les images associées au point d'intérêt
        $imagePath = url('/assets/interests/'.$imageName);
        $interest->images()->create([
          'url' => $imagePath,
        ]);
      }
    }
    // Et on n'oublie pas d'associer les catégories à l'intérêt qui vient d'être créé
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
  //NB: WIP, update à mettre d'équerre avec Store.
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
      'address' => 'required',
      'latitude' => 'numeric|required',
      'longitude' => 'numeric|required',
      'bellitalia_id' => 'required_without:supplement_id',
      'supplement_id' => 'required_without:bellitalia_id',
      'tag_id' => 'required',
      'images.*' => 'max:40000000|image',
    ];

    // Messages d'erreur custom
    $messages = [
      'name.required' => "Veuillez saisir un nom",
      'address.required' => "Veuillez saisir une adresse",
      'latitude.numeric' => "Veuillez saisir une latitude valide",
      'latitude.required' => "Veuillez saisir une latitude",
      'longitude.numeric' => "Veuillez saisir une longitude valide",
      'longitude.required' => "Veuillez saisir une longitude",
      'bellitalia_id.required_without' => "Veuillez définir un numéro de Bell'Italia ou un supplément",
      'supplement_id.required_without' => "Veuillez définir un numéro de Bell'Italia ou un supplément",
      'tag_id.required' => "Veuillez sélectionner au moins une catégorie",
      'images.*.image' => "Au moins une image n'a pas le bon format (jpg, jpeg ou png)",
      'images.*.max' => "Au moins une image dépasse le poids autorisé (4Mo)",
    ];
    $data = $request->all();

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
    if(isset($data['images']) && !empty($data['images']) && $data['images'] != 'undefined')
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
          // Je récupère leur taille
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

          // On renomme les images en évitant toute possibilité de doublons :
          // Nom du point d'intérêt + index + date + heure
          // On fait bien attention de "nettoyer" le nom du point d'intérêt pour éviter tout pb dans la base :
          // Pas d'espace, en minuscule, pas d'accent ou de caractères spéciaux (s'il y en a, la lettre est supprimée)
          $imageName = trim(mb_strtolower(preg_replace("/[^A-Za-z0-9]/", '', $interest->name))).$key.'-'.date("Ymd-His", strtotime('+2 hours'));

          // Quand tout ça est ok, on peut stocker les images
          $source = imagecreatefromjpeg($oneImage);
          $imageSave = imagejpeg($source,'./assets/interests/'. $imageName,$quality);
          imagedestroy($source);

          // Et enfin on stocke l'URL vers la ou les images associées au point d'intérêt
          $imagePath = url('/assets/interests/'.$imageName);
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
    if(isset($tags)) {
      $interest->tags()->sync($tags);
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
