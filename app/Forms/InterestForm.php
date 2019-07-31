<?php

namespace App\Forms;

use Kris\LaravelFormBuilder\Form;
use App\Interest;

class InterestForm extends Form
{
  protected $clientValidationEnabled = false;
  public function buildForm()
  {
    // Edition
    if ($this->getModel() && $this->getModel()->id) {
      $mode = "edition";
      $url = route("interest.update", $this->getModel()->id);
      $method = "PUT";
      $label = "Sauvegarder";
      // Création
    } else {
      $mode = "creation";
      $url = route("interest.store");
      $method = "POST";
      $label = "Sauvegarder";

    }
    $this
    ->add("name", "text", [
      "label" => "Nom du point d'intérêt *",
      "rules" => "required",
      "error_messages" => [
        "name.required" => "Veuillez donner un nom à votre point d'intérêt",
      ]
    ])
    ->add("description", "textarea", [
      "label" => "Description",
    ])
    ->add("link", "url", [
      "label" => "Lien",
    ])
    ->add("latitude", "number", [
      "label" => "Latitude *",
      "rules" => "required|between:0,99.99",
      "error_messages" => [
        "latitude.required" => "Veuillez donner une latitude à votre point d'intérêt",
      ],
      "attr" => ["step" => 0.0000001]
    ])
    ->add("longitude", "number", [
      "label" => "Longitude *",
      "rules" => "required|between:0,99.99",
      "rules" => "required",
      "error_messages" => [
        "longitude.required" => "Veuillez donner une longitude à votre point d'intérêt",
      ],
      "attr" => ["step" => 0.00000001]
    ])
    ->add('city', 'form', [
      'class' => $this->formBuilder->create('App\Forms\CityForm'),
      'label' => false,
    ])
    ->add('region', 'form', [
      'class' => $this->formBuilder->create('App\Forms\RegionForm'),
      'label' => false,
    ])
    ->add('bellitalia', 'form', [
      'class' => $this->formBuilder->create('App\Forms\BellitaliaForm'),
      'label' => false,
    ])
    ->add('tag', 'form', [
      'class' => $this->formBuilder->create('App\Forms\TagForm'),
      'label' => false,
    ])
    ->add("submit", "submit", [
      "label" => $label,
      "wrapper" => [
        "class" => "d-flex justify-content-center"
      ],
      "attr" => [
        "class" => "btn btn-fill btn-blue"
      ]
    ]);
    $this->formOptions = [
      "method" => $method,
      "url" => $url
    ];
  }

}
