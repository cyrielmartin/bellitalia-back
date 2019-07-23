@extends('layouts.app')

@section('map')

  <div id="mapid"></div>



  <script>

  // création de la carte
  var mymap = L.map('mapid').setView([40.853294, 14.305573], 5.5);

  // ajout du calque image
  L.tileLayer('https://maps.heigit.org/openmapsurfer/tiles/roads/webmercator/{z}/{x}/{y}.png', {
    maxZoom: 18,
  }).addTo(mymap);

  // pour chacun des lieux en BDD...

  @foreach ($interests as $interest)

  var interest = [{{$interest->latitude}}, {{$interest->longitude}}];
  var popup = '<h3>{{$interest->city_id}}</h3><h4>{{$interest->name}}</h4><p>{{$interest->description}}</p><a target="_blank" rel="noopener noreferrer" href="{{$interest->link}}">Lien</a>';



  // ... ajout de marqueurs...
  var marker = L.marker(interest).addTo(mymap);

  // ... et affichage pop up
  marker.bindPopup(popup);
  @endforeach

  </script>

@endsection
