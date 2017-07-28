<!DOCTYPE html>
<?php
$listadresse=[];
try {
  $bdd = new PDO("mysql:host=localhost; dbname=locations; charset=utf8", "root", "j9hn2x2");
} catch (Exception $e) {
  die("Erreur : ".$e -> getMessage());
}

$reponse = $bdd->query("SELECT * FROM `TABLE 2` WHERE `rue`!='' ");

while($donnees=$reponse->fetch()){
    $id[] = $donnees["ID"];
    $rue[] = $donnees['rue'];
    $code_postal[] = $donnees['code postal'];
    $Ville[] = $donnees['ville'];
    $lat[] = $donnees['latitude'];
    $long[] = $donnees['longitude'];

}

for ($i=0; $i <count($rue) ; $i++) {
    
  $listaddress[]=['ID'=>$id[$i], 'Rue'=>$rue[$i], 'CodePostal'=>$code_postal[$i], 'Ville'=>$Ville[$i], 'Lat'=>$lat[$i], 'Lng'=>$long[$i]];
}



$geocoder = "http://maps.googleapis.com/maps/api/geocode/json?address=%s&sensor=false";
  for ($i=0; $i<count($listaddress);++$i) {
    if (strlen($listaddress[$i]['Lat']) == "" && strlen($listaddress[$i]['Lng']) == "") {
       $adresse = $listaddress[$i]['Rue'].', '.$listaddress[$i]['CodePostal'].', '.$listaddress[$i]['Ville'];
       // Requête envoyée à l'API Geocoding
       $query = sprintf($geocoder, urlencode(utf8_encode($adresse)));
       $result = json_decode(file_get_contents($query));
       $json = $result->results[0];
       $adress->Lat = (string) $json->geometry->location->lat;
       $adress->Lng = (string) $json->geometry->location->lng;

       $lat = $adress->Lat;
       $lng = $adress->Lng;
       $listaddress[$i]["Lat"]=$lat;
       $listaddress[$i]["Lng"]=$lng;
       //$listaddress=[Lat=>$lat, Lng=>$lng];
    }
  }
  $listaddressjson =json_encode($listaddress);

  for ($i=0; $i<count($listaddress); $i++){
    $lat = $listaddress[$i]["Lat"];
    $lng = $listaddress[$i]["Lng"];
    $id = $listaddress[$i]["ID"];
    $bdd->exec("UPDATE `TABLE 2` SET latitude = $lat, longitude = $lng WHERE `ID` = $id");
  }

  //var_dump($listaddressjson);

?>


<html>
  <head>
    <meta charset="utf-8">
    <title>test carte</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.1.0/dist/leaflet.css"
  integrity="sha512-wcw6ts8Anuw10Mzh9Ytw4pylW8+NAD4ch3lqm9lzAsTxg0GFeJgoAtxuCLREZSC5lUXdVyo/7yfsqFjQ4S+aKw=="
  crossorigin=""/>
  <link rel="stylesheet" href="../../wp-content/plugins/carte/cartestyle.css">


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
        osm.addTo(map);

    //     var circle = L.circle([43.631725, 0.588503], 500, {
    // color: 'red',
    // fillColor: '#f03',
    // fillOpacity: 0.5
    // }).addTo(map);


        var coord = <?= $listaddressjson ?>;
        console.log(coord);
        var markers = [];
        var rue = [];


        // for (var i = 0; i < coord.length; i++) {
        //
        //   markers.push(marker);
        //
        // }

        for (var i = 0; i < coord.length; i++) {
          // markers[i].addTo(map);


        var marker = L.marker([coord[i].Lat, coord[i].Lng]).bindPopup(coord[i].Rue).addTo(map);
        marker.on('mouseover', function (e) {
          this.openPopup();
        });
        marker.on('mouseout', function (e) {
          this.closePopup();
        });
          console.log(marker)
      }


     };



    document.addEventListener('DOMContentLoaded',
    InitialiserCarte());
    </script>
  </body>


</html>
