<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Bell'Italia</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

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

        #map {
            height: 100%;
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
            <a href="https://github.com/cyrielmartin/bellitalia">GitHub</a>
        </div>
    </div>

    <div id="map"></div>
    <script>
        var map;

        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: {
                    lat: 41.890251,
                    lng: 12.492373
                },
                zoom: 6
            });
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBKFLOdmMpMZg-6_CHbN9Gw5zsT-_l4kmU&callback=initMap" async defer></script>




</body>

</html>
