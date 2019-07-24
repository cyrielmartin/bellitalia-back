<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interest;
use App\City;
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
  public function store(Request $request)
  {

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
