<?php

namespace App\Forms;

use Kris\LaravelFormBuilder\Form;
use App\Bellitalia;

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
    } else { //creation de modele
      $mode = "creation";
      $url = route("interest.store");
      $method = "POST";
      $label = __("Save");
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
      // "rules" => "required",
      // "error_messages" => [
      //   "publication.required" => "Veuillez associer une date de publication à ce numéro de Bell'Italia",
      // ]
    ]);
    $this->formOptions = [
      "method" => $method,
      "url" => $url
    ];
  }

}
