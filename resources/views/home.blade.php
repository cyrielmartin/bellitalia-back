<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Bell'Italia</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.5.1/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin="" />
    <!-- Styles -->
    <style>
        html,
        body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        a {
            text-decoration: none;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: flex-start;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links>a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        #mapid {
            height: 100vh;
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="content">
        <div class="title m-b-md">
            <a href="/">Bell'Italia</a>
        </div>

        <div class="links">
            <a href="/admin">Admin</a>
            <a target="_blank" rel="noopener noreferrer" href="https://github.com/cyrielmartin/bellitalia">GitHub</a>
        </div>
    </div>
@foreach ($places as $place)
<div id="mapid"></div>

    <script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js" integrity="sha512-GffPMF3RvMeYyc1LWMHtK8EbPv0iNZ8/oTtHPx9/cc2ILxQ+u905qIwdpULaqDkyBKgOaB57QTMg7ztg8Jm2Og==" crossorigin=""></script>

    <script>
        var place = [{{$place->latitude}}, {{$place->longitude}}];
        var popup = '<h3>{{$place->city}}, {{$place->region}}</h3><h4>{{$place->monument}}</h4><p>{{$place->description}}</p><p>Bell\'Italia n°{{$place->issue}}, {{ \Carbon\Carbon::parse($place->published)->format('F, Y') }}</p><a target="_blank" rel="noopener noreferrer" href="{{$place->link}}">Lien</a>';

        // création de la carte
        var mymap = L.map('mapid').setView([40.853294, 14.305573], 5.5);

        // ajout du calque image
        L.tileLayer('https://maps.heigit.org/openmapsurfer/tiles/roads/webmercator/{z}/{x}/{y}.png', {
            maxZoom: 18,
        }).addTo(mymap);

        // ajout de marqueurs
        var marker = L.marker(place).addTo(mymap);

        // affichage pop up
        marker.bindPopup(popup);
    </script>

@endforeach
</body>

</html>
