<?php

namespace App\Http\Controllers;

use App\City;
use Illuminate\Http\Request;
use App\Interest;
use Validator;

class InterestController extends Controller
{
  /**
  * Affiche la liste des ressources (GET)
  *
  * @return \Illuminate\Http\Response
  */
  public function index()
  {
    return response()->json(Interest::get(), 200);
  }
  //
  // /**
  // * Affichage du formulaire de création d'une ressource
  // *
  // * @return \Illuminate\Http\Response
  // */
  // public function create()
  // {
  //   //
  // }

  /**
  * Enregistrement d'une nouvelle ressource (POST)
  *
  * @param  \Illuminate\Http\Request  $request
  * @return \Illuminate\Http\Response
  */
  public function store(Request $request)
  {
    $rules = [
      'name' => 'required',
      'latitude' => 'required',
      'longitude' => 'required',
      'city.name' => 'required_without:city_id',
    //   'region[name]' => 'required_unless:region_id',

    ];

    // dd($request->all());
    $validator = Validator::make($request->all(), $rules);
    if($validator->fails()){
      return response()->json($validator->errors(), 400);
    }

    $data = $request->all();
if(isset($request['city_id'])){
    $interest = Interest::create($data);
}
else {
    $city = City::create(array("name" => $data['city']['name'], "region_id" => $data['city']['region_id']));
    $data['city_id'] = $city->id;
    $interest = Interest::create($data);
}




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
    $interest = Interest::find($id);
    if(is_null($interest)){
      return response()->json(['message' => 'Not found'], 404);
    }
    return response()->json($interest, 200);
  }

  // /**
  // * Affichage du formulaire de modification d'une ressource
  // *
  // * @param  int  $id
  // * @return \Illuminate\Http\Response
  // */
  // public function edit($id)
  // {
  //   //
  // }

  /**
  * Mise à jour d'une ressource (PUT)
  *
  * @param  \Illuminate\Http\Request  $request
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function update(Request $request, $id)
  {
    $interest = Interest::find($id);
    if(is_null($interest)){
      return response()->json(['message' => 'Not found'], 404);
    }
    $interest->update($request->all());
    return response()->json($interest, 200);
  }

  /**
  * Suppression d'une ressource (DELETE)
  *
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function destroy($id)
  {
    $interest = Interest::find($id);
    if(is_null($interest)){
      return response()->json(['message' => 'Not found'], 404);
    }
    $interest->delete();
    return response()->json(null, 204);
  }
}
