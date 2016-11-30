//set mapbox accessToken
L.mapbox.accessToken = 'pk.eyJ1IjoiZGplbWJlcGxheWVyIiwiYSI6ImNpdndlMnV0dDAwMGgydWw5YjJmbTB4ZHkifQ.F8lJy9BkPjsjZkLKKvhqZw';

//init map
var map = L.mapbox.map('map', 'mapbox.streets')
    .setView([48.2316156,17.5632506], 9);
//L.control.locate({flyTo: true}).addTo(map);
map.addControl(L.control.locate({
       locateOptions: {
               maxZoom: 14
}}));

//init empty featurelayer
var featureLayer = L.mapbox.featureLayer().addTo(map);


$(document).ready(function(){

    //init default markets
    initMarkers();

    //send filter form
    $('#filterForm').submit(function() {
        initMarkers();
        return false;
    });

});

function initMarkers() {
  var formData = $('#filterForm').serialize();
  var data;
  $.get('/api?'+formData, function( data ) {
    setMarkers(data);
    setCounter(data.length);
  });
}

function setMarkers(data) {
    if (featureLayer !== 'undefined') {
      map.removeLayer(featureLayer);
    }
    featureLayer = L.mapbox.featureLayer(data).addTo(map);
}

function setCounter(count){
  $('.counter').text("Počet záznamov: " + count);

}
