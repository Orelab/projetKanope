<!DOCTYPE html>
<?php
$listadresse=[];
try {
  $bdd = new PDO("mysql:host=localhost; dbname=test; charset=utf8", "root", "root");
} catch (Exception $e) {
  die("Erreur : ".$e -> getMessage());
}

$reponse = $bdd->query("SELECT * FROM `TABLE 1` WHERE `COL 8`!='' ");

while($donnees=$reponse->fetch()){
  $rue = $donnees['COL 8'];
  $code_postal = $donnees['COL 10'];
  $Ville = $donnees['COL 12'];
$listaddress=[Rue=>$rue, CodePostal=>$code_postal, Ville=>$Ville, Lat=>"",Lng=>""];
//$listaddressjson =json_encode($listaddress);



 // $listaddressutf8 =utf8_encode($listaddress);

$geocoder = "http://maps.googleapis.com/maps/api/geocode/json?address=%s&sensor=false";

//$arrAddresses = adress::LoadAll(); // Notre collection d'objets Address

foreach ($listaddress as $address) {


        if (strlen($listaddress[Lat]) == "" && strlen($listaddress[Lng]) == "") {

            $adresse = $listaddress[Rue].', '.$listaddress[CodePostal].', '.$listaddress[Ville];


            // Requête envoyée à l'API Geocoding
            $query = sprintf($geocoder, urlencode(utf8_encode($adresse)));

            $result = json_decode(file_get_contents($query));
            $json = $result->results[0];

            $adress->Lat = (string) $json->geometry->location->lat;
            $adress->Lng = (string) $json->geometry->location->lng;

            $lat = $adress->Lat;
            $lng = $adress->Lng;
            $listaddress["Lat"]=$lat;
            $listaddress["Lng"]=$lng;
            //$listaddress=[Lat=>$lat, Lng=>$lng];

         }
         var_dump($listadress);
}

}

?>
 <script type="text/javascript">

 </script>

<html>
  <head>
    <meta charset="utf-8">
    <title>test carte</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.1.0/dist/leaflet.css"
  integrity="sha512-wcw6ts8Anuw10Mzh9Ytw4pylW8+NAD4ch3lqm9lzAsTxg0GFeJgoAtxuCLREZSC5lUXdVyo/7yfsqFjQ4S+aKw=="
  crossorigin=""/>
  <link rel="stylesheet" href="style.css">


</head>
  <body>

    <h1>La carte</h1>
    <div id="map"></div>



    <script src="https://unpkg.com/leaflet@1.1.0/dist/leaflet.js"
    integrity="sha512-mNqn2Wg7tSToJhvHcqfzLMU6J4mkOImSPTxVZAdo+lcPlk+GhZmYgACEe0x35K7YzW1zJ7XyJV/TT1MrdXvMcA=="
    crossorigin=""></script>

    <script type="text/javascript">


    function InitialiserCarte() {

        var map = L.map('map').setView([43.6108333,0.5927778], 9);

        // create the tile layer with correct attribution
        var tuileUrl = 'http://{s}.tile.osm.org/{z}/{x}/{y}.png';

        var attrib='Map data © <a href="http://openstreetmap.org">OpenStreetMap</a> contributors';

        var osm = L.tileLayer(tuileUrl, {
            minZoom: 6,
            maxZoom: 17,
            attribution: attrib
        });
    //     var circle = L.circle([43.631725, 0.588503], 500, {
    // color: 'red',
    // fillColor: '#f03',
    // fillOpacity: 0.5
    // }).addTo(map);

        osm.addTo(map);

        var markers = <?= $listaddressjson ?>;

        var marker = L.marker([43.6661798,0.600956181973427]).addTo(map);
        var marker2 = L.marker([43.6689771,0.5977891]).addTo(map);

        var popup = L.popup();

        marker.on('mouseover', function (e) {
            popup.setLatLng(e.latlng)
            .setContent("KANOPE Inno parc")
            .openOn(map);
        });

        marker.on('mouseout', function (e) {
            popup.closePopup();
        });

    };

    var listadressjson =

    document.addEventListener('DOMContentLoaded',
    InitialiserCarte());
    </script>
  </body>


</html>
