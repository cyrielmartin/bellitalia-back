<?php

namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class PlaceForm extends Form
{
    protected $clientValidationEnabled = false;
    public function buildForm()
    {
        if ($this->getModel() && $this->getModel()->id) { //edition d'un modele
            $url = route('place.update', $this->getModel()->id);
            $method = 'PUT';
            $label = "Sauvegarder";
        } else { //creation de modele
            $url = route('place.store');
            $method = 'POST';
            $label = "Enregistrer";
        }

        $this
            ->add('region', 'text', [
                'label' => 'Région *',
                'rules' => 'required',
                'error_messages' => [
                    'region.required' => 'Veuillez indiquer une région',
                ]
            ])
            ->add('city', 'text', [
                'label' => 'Ville *',
                'rules' => 'required',
                'error_messages' => [
                    'city.required' => 'Veuillez indiquer une ville',
                ]
            ])
            ->add('monument', 'text', [
                'label' => 'Monument',
            ])
            ->add('latitude', 'number', [
                'label' => 'Latitude *',
                'rules' => 'required',
                'error_messages' => [
                    'latitude.required' => 'Veuillez indiquer une latitude',
                ]
            ])
            ->add('longitude', 'number', [
                'label' => 'Longitude *',
                'rules' => 'required',
                'error_messages' => [
                    'longitude.required' => 'Veuillez indiquer une longitude',
                ]
            ])
            ->add('description', 'text', [
                'label' => 'Description',
            ])
            ->add('issue', 'number', [
                'label' => 'N° du Bell\'Italia *',
                'rules' => 'required',
                'error_messages' => [
                    'issue.required' => 'Veuillez indiquer le numéro du Bell\'Italia',
                ]
            ])
            ->add('published', 'date', [
                'label' => 'Date de publication *',
                'rules' => 'required',
                'error_messages' => [
                    'date.required' => 'Veuillez indiquer la date de publication du Bell\'Italia',
                ]
            ])
            ->add("url", "url", [
                "label" => 'Lien',
                "label_attr" => [
                    'class' => "bmd-label-floating"
                ],
                "rules" => "nullable|url",
                'help_block' => [
                    'text' => __("http(s)://www.exemple.fr"),
                    'tag' => 'span',
                    'attr' => ['class' => 'bmd-help']
                ],
                "error_messages" =>	[
                    "url.url" => "Le site doit être une adresse valide de la forme \"http(s)://www.exemple.fr\"."
                    ],
                ]);

        $this->add('submit', 'submit', ['label' => $label, 'wrapper' => ['class' => '']]);
        $this->formOptions = [
            'method' => $method,
            'url' => $url,
            'files' => true,
        ];
    }
}
