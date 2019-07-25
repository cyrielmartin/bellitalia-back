<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interest;
use App\City;
use App\Region;
use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\InterestForm;

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
    $form = $formBuiler->create(InterestForm::class);
    if(!$form->isValid()) {
      return redirect()->back()->withErrors($form->getErrors())->withInput();
    }
    $form->redirectIfNotValid();
    $request = $form->getRequest();
    $data = $request->all();

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
    $interest = new Interest($data);
    $interest->save();


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
  public function edit($id)
  {

  }

  /**
  * Update the specified resource in storage.
  *
  * @param  int  $id
  * @return Response
  */
  public function update($id)
  {

  }

  /**
  * Remove the specified resource from storage.
  *
  * @param  int  $id
  * @return Response
  */
  public function destroy($id)
  {

  }

}

?>
