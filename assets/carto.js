
function InitialiserCarte( coord ) {

    var map = L.map('map').setView([43.6108333,0.5927778], 8);

    var osm = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
        minZoom: 4,
        maxZoom: 19,
        attribution: '&copy Kanopé'
    });

    osm.addTo(map);

    var markers = [];
    var rue = [];

    for (var i = 0; i < coord.length; i++) {
      if( coord[i].lat && coord[i].lon){


        var icon = L.icon({
            iconUrl:      coord[i].profilepicture,
            iconSize:     [50],
            iconAnchor:   [25,25],
            popupAnchor:  [0,-25]
        });

        var marker = L.marker([coord[i].lat, coord[i].lon], {icon: icon})
          .bindPopup( popupHTML(coord[i]) )
          .openPopup()
          .addTo(map);

        marker.on('mouseover', function (e) {
          this.openPopup();
        });

        marker.on('mouseout', function (e) {
          var me = this;
          setTimeout(function(){
            me.closePopup();
          }, 4000 );
        });

        marker.on('click', function (e) {
          var url = jQuery(e.target._popup._contentNode).find('a').attr('href');
          window.location.href = url;
        });
      }
    }
}


function popupHTML(user){


  var name = user.first_name + ' ' + user.last_name;

  var picture = '<img src="' + user.profilepicture + '" alt="' + name + '" />';

  var jobs = JSON.parse(user.user_metiers).join(', ');

  var url = '/profil/' 
    + user.first_name.replace(' ','_') + '-' 
    + user.last_name.replace(' ','_') + '/';

  var addr = user.user_adresse01 + '<br/>' 
    + (user.user_adresse02 ? user.user_adresse02 + '<br/>' : '')
    + (user.user_codepostal ? user.user_codepostal + ' ' : '')
    + user.user_ville + '<br/>';

  return '<a href="' + url + '"><b>' + picture + name + '</b></a><br/>' 
    + '<span class="jobs">' + jobs + '</span><br/>'
    + addr;
}


