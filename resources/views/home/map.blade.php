@extends('layouts.app')
@include('layouts.header')
@section('content')

    <div class="container-rem">
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card">
            <div id="app">


              {{-- Composant Vue V1 --}}
              <map-component></map-component>
            </div>
          </div>
        </div>
      </div>
    </div>
    @push('scripts')
      <script src="/assets/js/plugins/bootstrap_fileinput/bootstrap_fileinput.min.js" charset="utf-8"></script>
      <script src="/js/app.js"></script>
  {{-- <div id="mapid"></div>

  <script>

  // création de la carte
  var mymap = L.map('mapid').setView([40.853294, 14.305573], 5.5);

  // ajout du calque image
  L.tileLayer('https://maps.heigit.org/openmapsurfer/tiles/roads/webmercator/{z}/{x}/{y}.png', {
    maxZoom: 18,
  }).addTo(mymap);

  // pour chacun des lieux en BDD...
  @foreach ($interests as $interest)

  // création du point d'intérêt
  var interest = [{{$interest->latitude}}, {{$interest->longitude}}];

  // initialisation du pop up
  var popup = '';

  // ajout de tous les éléments nécessaires au pop up via concaténation.
  // D'abord les boucles (si belongsToMany, plusieurs éléments de même type dans le popup)
  @foreach ($interest->tags as $tag)
  popup = popup + '<small>{{$tag->name}}</small> '
  @endforeach

  // Autres boucles ici si nécessaire

  // Et ensuite les éléments uniques à chaque popup. A chaque fois, concaténation sur le popup
  popup += '<h5>{{$interest->city->name}}, {{$interest->city->region->name}}</h5><h4>{{$interest->name}}</h4><p>{{$interest->description}}</p><p><a target="_blank" rel="noopener noreferrer" href="{{$interest->link}}">Photos</a></p><h6>Bell\'Italia n°$interest->bellitalia->number, </h6><a href="{{route('interest.edit', $interest->id)}}">Modifier</a>';

  // Ajout des marqueurs
  var marker = L.marker(interest).addTo(mymap);

  // Et affichage du pop up
  marker.bindPopup(popup);
  @endforeach

  </script> --}}

@endsection
