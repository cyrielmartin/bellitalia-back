<?php

namespace App\Forms;

use Kris\LaravelFormBuilder\Form;
use App\Bellitalia;
use Carbon\Carbon;

class BellitaliaForm extends Form
{
  protected $clientValidationEnabled = false;
  public function buildForm()
  {
    if ($this->getModel() && $this->getModel()->id) { //edition d"un modele
      $mode = "edition";
      $url = route("interest.update", $this->getModel()->id);
      $method = "PUT";
      $label = __("Save");
      $number = $this->getModel()->bellitalia()->get()->all();

      foreach ($number as $thisnumber) {
        $publication = date("Y-m-d", strtotime($thisnumber->publication));
      }

    } else { //creation de modele
      $mode = "creation";
      $url = route("interest.store");
      $method = "POST";
      $label = __("Save");
      $publication = '';
    }
    $this
    ->add("number", "number", [
      "label" => "Numéro du Bell'Italia",
      // "rules" => "required",
      // "error_messages" => [
      //   "number.required" => "Veuillez associer votre point d'intérêt à un Bell'Italia",
      // ]
    ])
    ->add("publication", "date", [
      "label" => "Date de publication du Bell'Italia",
      "value" => $publication,


      // "rules" => "required",
      // "error_messages" => [
      //   "publication.required" => "Veuillez associer une date de publication à ce numéro de Bell'Italia",
      // ]
      "value" => $publication
    ]);
    $this->formOptions = [
      "method" => $method,
      "url" => $url
    ];
  }

}
