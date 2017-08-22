
console.log("yipikai");


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

// var coord;
//
// function adressToCoord (address){
//
//   $.ajax({
//     type: "GET",
//     url:  "http://nominatim.openstreetmap.org/search",
//     dataType : "json",
//     data: {
//       format : json,
//       q : address
//     }
//
//     success : function (data){
//       $("coord").append(data);
//       consol.log(coord);
//     }
//   });
// };

var listadressjson =

// nominatim Open Street  Maps Geocoder
$geocoder = "http://nominatim.openstreetmap.org/search",;

$arrAddresses = Address::LoadAll(); // Notre collection d'objets Address

foreach ($arrAddresses as $address) {

        if (strlen($address->Lat) == 0 && strlen($address->Lng) == 0) {

            $adresse = $address->Rue;
            $adresse .= ', '.$address->CodePostal;
            $adresse .= ', '.$address->Ville;

            // Requête envoyée à l'API Geocoding
            $query = sprintf($geocoder, urlencode(utf8_encode($adresse)));

            $result = json_decode(file_get_contents($query));
            $json = $result->results[0];

            $adress->Lat = (string) $json->geometry->location->lat;
            $adress->Lng = (string) $json->geometry->location->lng;
            $adress->Save();

         }

document.addEventListener('DOMContentLoaded',
InitialiserCarte());
