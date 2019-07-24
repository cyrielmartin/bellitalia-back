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
      // $url = route("Interest.update", $this->getModel()->id);
      $method = "PUT";
      $label = __("Save");
      // $type = $this->getModel()->type;
      // $start_date = date("Y-m-d", strtotime($this->getModel()->start));
      // $end_date = date("Y-m-d", strtotime($this->getModel()->end));
      // $closed = $this->getModel()->closed;
      // Création
    } else {
      $mode = "creation";
      $url = route("interest.store");
      $method = "POST";
      $label = __("Save");
      // $type = "";
      // $start_date = "";
      // $end_date = "";
      // $closed = 0;
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
      "label" => "Latitude",
    ])
    ->add("longitude", "number", [
      "label" => "Longitude",
    ])
    // ->add("start", "date",  [
    //   "label" => __("Start date")." *",
    //   "label_attr" => [
    //     // 'class' => "bmd-label-floating"
    //   ],
    //   "attr" => [
    //     "class" => "form-control datepicker"
    //   ],
    //   "rules" => "required",
    //   "error_messages" => [
    //     "start.required" => __("Please specify a date to your event"),
    //   ],
    //   "value" => $start_date,
    // ])
    // ->add("end", "date", [
    //   "label" => __("End date"),
    //   "label_attr" => [
    //     // 'class' => "bmd-label-floating"
    //   ],
    //   "attr" => [
    //     "class" => "form-control datepicker"
    //   ],
    //   "rules" => "nullable|after_or_equal:start-date",
    //   "error_messages" => [
    //     "end.after_or_equal" => __("The end date has to be after the start date"),
    //   ],
    //   "value" => $end_date,
    // ])
    // ->add('closed', 'choice', [
    //   'choices' => [0 => 'ouvert', 1 => 'fermé'],
    //   'label' => __("Interest")." :",
    //   'selected' => $closed,
    //   'expanded' => true,
    //   'multiple' => false
    // ]);
    // if ($mode=="creation") {
    //   $this->add('question', 'form', [
    //     'class' => $this->formBuilder->create('App\Forms\QuestionForm'),
    //     'label' => false,
    //     'wrapper' => [
    //       'data-id' => 0,
    //       'class' => "question-divblock form-group"
    //     ]
    //   ]);
    // }
    // $this->add("button", "button", [
    //   "label" => "Ajouter une question",
    //   "wrapper" => [
    //     "class" => "d-flex justify-content-center add-question-divblock"
    //   ],
    //   "attr" => [
    //     "class" => "btn btn-fill btn-blue add-question"
    //   ],
    // ])
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
