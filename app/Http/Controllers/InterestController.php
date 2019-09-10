<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interest;
use App\City;
use App\Region;
use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\InterestForm;
use App\Bellitalia;
use App\Tag;

class InterestController extends Controller
{

  /**
  * Display a listing of the resource.
  *
  * @return Response
  */
  public function index()
  {
    $interests = Interest::all();
    return view('home.map', compact('interests'));
  }


  public function getlist()
  {
    $interests = Interest::all();
    return view('home.list', compact('interests'));
  }


  /**
  * Show the form for creating a new resource.
  *
  * @return Response
  */
  public function create(FormBuilder $formBuilder)
  {
    $form = $formBuilder->create(InterestForm::class);
    return view('interest.create', compact('form'));
  }

  /**
  * Store a newly created resource in storage.
  *
  * @return Response
  */
  public function store(FormBuilder $formBuiler)
  {
    // Récupération de toutes les données envoyées via le InterestForm
    $form = $formBuiler->create(InterestForm::class);
    if(!$form->isValid()) {
      return redirect()->back()->withErrors($form->getErrors())->withInput();
    }
    $form->redirectIfNotValid();
    $request = $form->getRequest();
    $data = $request->all();


    // Récupération et stockage des données du child form RegionForm
    $region = $data['region'];
    if(!isset($region['name'])) {
      $validator = $form->validate(['region'.$key.'.name' => 'required'], ['region'.$key.'.name.required' => __('The region name is required')]);
      $isValid = !$validator->fails();
      $form->alterValid($form, $form, $isValid);
      if (!$form->isValid()) {
        return redirect()->back()->withErrors($form->getErrors())->withInput();
      }
      $form->redirectIfNotValid();
    }
    $regionModel = new Region(array('name'=> $region['name']));
    $regionModel->save();

    // Récupération et stockage des données du child form CityForm (et association de la ville avec une région)
    $city = $data['city'];
    if(!isset($city['name'])) {
      $validator = $form->validate(['city'.$key.'.name' => 'required'], ['city'.$key.'.name.required' => __('The city name is required')]);
      $isValid = !$validator->fails();
      $form->alterValid($form, $form, $isValid);
      if (!$form->isValid()) {
        return redirect()->back()->withErrors($form->getErrors())->withInput();
      }
      $form->redirectIfNotValid();
    }
    $cityModel = new City(array('name'=> $city['name'], 'region_id' => $regionModel->id));
    $cityModel->save();

    // Ce petit bout de code sert à associer la ville dans la table Interest
    $data['city_id'] = $cityModel->id;

    // Récupération et stockage des données du child form BellitaliaForm
    $bellitalia = $data['bellitalia'];

    // Ce child form a deux champs. On les valide l'un après l'autre.
    if(!isset($bellitalia['number'])) {
      $validator = $form->validate(['bellitalia'.$key.'.number' => 'required'], ['bellitalia'.$key.'.number.required' => __('The bellitalia number is required')]);
      $isValid = !$validator->fails();
      $form->alterValid($form, $form, $isValid);
      if (!$form->isValid()) {
        return redirect()->back()->withErrors($form->getErrors())->withInput();
      }
      $form->redirectIfNotValid();
    }

    if(!isset($bellitalia['publication'])) {
      // Formattage de la date pour stockage
      $bellitalia['publication'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['publication'])));

      $validator = $form->validate(['bellitalia'.$key.'.publication' => 'required'], ['bellitalia'.$key.'.publication.required' => __('The bellitalia publication is required')]);
      $isValid = !$validator->fails();
      $form->alterValid($form, $form, $isValid);
      if (!$form->isValid()) {
        return redirect()->back()->withErrors($form->getErrors())->withInput();
      }
      $form->redirectIfNotValid();
    }

    $bellitaliaModel = new Bellitalia(array('number' => $bellitalia['number'], 'publication' => $bellitalia['publication']));
    $bellitaliaModel->save();

    // Même petit bout de code que plus haut servant à associer un Bellitalia dans la table Interest.
    $data['bellitalia_id'] = $bellitaliaModel->id;

    // Une fois que tout ça est fait, on peut enregistrer l'Interest en base.
    $interest = new Interest($data);
    $interest->save();

    // Et seulement après, on peut récupérer et stocker les données du child form TagForm,
    // relation ManyToMany avec Interest (donc qui a besoin que Interest existe)
    if (isset($data['tag'])) {
      $tags = array();

      foreach ($data['tag'] as $tag) {
        $t = array("name" => $tag);
        // Pour chaque tag sélectionné : soit il existe déjà, et on le récupère, soit on stocke son nom dynamiquement.
        $tags[] = Tag::firstOrCreate($t)->id;
      }

      $interest->tags()->sync($tags);
    }

    return redirect(route('interest.index'));

  }

  /**
  * Display the specified resource.
  *
  * @param  int  $id
  * @return Response
  */
  public function show($id)
  {

  }

  /**
  * Show the form for editing the specified resource.
  *
  * @param  int  $id
  * @return Response
  */
  public function edit(FormBuilder $formBuiler, $id)
  {
    $interest = Interest::findOrFail($id);
    // A l'édition, nécessaire de passer des paramètres supplémentaires au formulaire : les données de l'interest déjà en base. D'où le ['model' => $interest]
    $form = $formBuiler->create(InterestForm::class, ['model' =>$interest]);
    //Si je m'en tiens à ça, je ne récupèrerai que les infos de la table Interest + tables directement liées en belongsTo.
    // Si je veux les infos Catégories et Région, il faut les importer au chargement des formulaires concernés

    return view('interest.create', compact('form', 'interest'));
  }

  /**
  * Update the specified resource in storage.
  *
  * @param  int  $id
  * @return Response
  */
  public function update(FormBuilder $formBuiler, $id)
  {
    $interest = Interest::findOrFail($id);
    $form = $formBuiler->create(InterestForm::class, ['model' => $interest, 'data' => [$interest->tags]]);
    if(!$form->isValid()) {
      return redirect()->back()->withErrors($form->getErrors())->withInput();
    }
    $form->redirectIfNotValid();
    $request = $form->getRequest();
    $data = $request->all();

    // Comme pour le store, avant de faire l'update de la table interest, il faut aller récupérer chacune des données mises à jour sur les autres tables reliées. Le code est strictement le même que dans le store.

    // Region
    $region = $data['region'];
    if(!isset($region['name'])) {
      $validator = $form->validate(['region'.$key.'.name' => 'required'], ['region'.$key.'.name.required' => __('The region name is required')]);
      $isValid = !$validator->fails();
      $form->alterValid($form, $form, $isValid);
      if (!$form->isValid()) {
        return redirect()->back()->withErrors($form->getErrors())->withInput();
      }
      $form->redirectIfNotValid();
    }
    $regionModel = new Region(array('name'=> $region['name']));
    $regionModel->save();

    // City
    $city = $data['city'];
    if(!isset($city['name'])) {
      $validator = $form->validate(['city'.$key.'.name' => 'required'], ['city'.$key.'.name.required' => __('The city name is required')]);
      $isValid = !$validator->fails();
      $form->alterValid($form, $form, $isValid);
      if (!$form->isValid()) {
        return redirect()->back()->withErrors($form->getErrors())->withInput();
      }
      $form->redirectIfNotValid();
    }
    $cityModel = new City(array('name'=> $city['name'], 'region_id' => $regionModel->id));
    $cityModel->save();

    $data['city_id'] = $cityModel->id;

    // Bellitalia
    $bellitalia = $data['bellitalia'];

    // Numéro du BI
    if(!isset($bellitalia['number'])) {
      $validator = $form->validate(['bellitalia'.$key.'.number' => 'required'], ['bellitalia'.$key.'.number.required' => __('The bellitalia number is required')]);
      $isValid = !$validator->fails();
      $form->alterValid($form, $form, $isValid);
      if (!$form->isValid()) {
        return redirect()->back()->withErrors($form->getErrors())->withInput();
      }
      $form->redirectIfNotValid();
    }

    // Date de publication BI
    if(!isset($bellitalia['publication'])) {

      $bellitalia['publication'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['publication'])));

      $validator = $form->validate(['bellitalia'.$key.'.publication' => 'required'], ['bellitalia'.$key.'.publication.required' => __('The bellitalia publication is required')]);
      $isValid = !$validator->fails();
      $form->alterValid($form, $form, $isValid);
      if (!$form->isValid()) {
        return redirect()->back()->withErrors($form->getErrors())->withInput();
      }
      $form->redirectIfNotValid();
    }

    $bellitaliaModel = new Bellitalia(array('number' => $bellitalia['number'], 'publication' => $bellitalia['publication']));
    $bellitaliaModel->save();

    $data['bellitalia_id'] = $bellitaliaModel->id;

    // Seulement quand tout cela est fait, on peut mettre à jour la table Interest
    $interest->update($data);

    // Et son s'occupe du ManyToMany à la fin
    if (isset($data['tag'])) {
      $tags = array();

      foreach ($data['tag'] as $tag) {
        $t = array("name" => $tag);
        // Pour chaque tag sélectionné : soit il existe déjà, et on le récupère, soit on stocke son nom dynamiquement.
        $tags[] = Tag::firstOrCreate($t)->id;
      }

      $interest->tags()->sync($tags);
    }

    return redirect(route('interest.index'));
  }

  /**
  * Remove the specified resource from storage.
  *
  * @param  int  $id
  * @return Response
  */
  public function destroy($id)
  {
    $interest = Interest::findOrFail($id);
    $interest->delete();
    return redirect(route('interest.index'));
  }

}
