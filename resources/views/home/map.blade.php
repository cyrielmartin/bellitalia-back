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
        @foreach ($places as $place)

        var place = [{{$place->latitude}}, {{$place->longitude}}];
        var popup = '<h3>{{$place->city}}, {{$place->region}}</h3><h4>{{$place->monument}}</h4><p>{{$place->description}}</p><p>Bell\'Italia n°{{$place->issue}}, {{ \Carbon\Carbon::parse($place->published)->format('F, Y') }}</p><a target="_blank" rel="noopener noreferrer" href="{{$place->link}}">Lien</a>';


        // ... ajout de marqueurs...
        var marker = L.marker(place).addTo(mymap);

        // ... et affichage pop up
        marker.bindPopup(popup);
        @endforeach

    </script>

@endsection
