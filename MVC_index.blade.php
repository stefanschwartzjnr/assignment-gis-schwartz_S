<!DOCTYPE html>
<html>

  <head>
    <meta charset="utf-8" />
    <title></title>
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no" />
    <link href='https://api.mapbox.com/mapbox.js/v3.0.1/mapbox.css' rel='stylesheet' />
    <link href='/css/style.css' rel='stylesheet' />
    <script data-require="jquery@*" data-semver="3.0.0" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.js"></script>
    <script src='https://api.mapbox.com/mapbox.js/v3.0.1/mapbox.js'></script>


  </head>

  <body>
  <div class="filter-wrap">
    <form id="filterForm">
      <input type="text" name="title" placeholder="Zadajte názov">
      <label for="choice1"><input type="checkbox" value="stadium" name="choices[]" id="choice1" checked>Štadión</label>
      <label for="choice2"><input type="checkbox" value="pitch" name="choices[]" id="choice2" checked>Ihrisko</label>
      <label for="choice3"><input type="checkbox" value="sports_centre" name="choices[]" id="choice3" checked>Športové stredisko</label>
      <label for="choice5"><input type="checkbox" value="swimming_pool" name="choices[]" id="choice5" checked>Plaváreň</label>
      <label for="choice6"><input type="checkbox" value="fitness_station" name="choices[]" id="choice6" checked>Fitnes centrum</label>
<!--
      <select name="limit">
        <option value="100">100</option>
        <option value="200">200</option>
        <option value="500">500</option>
        <option value="1500">1500</option>
      </select>
-->
<br>
      <input type="text" name="river" placeholder="Zadaj názov rieky">

<br>
      <input type="text" name="latitude" placeholder="Zadaj zem. šírku">
      <input type="text" name="longitude" placeholder="Zadaj zem. dĺžku">
      <label for="park_check"><input type="checkbox" value="mall" name="park_check" id="park_check">Zobraz najbližšie parkovisko</label>

<br>
      <select name="radius">
        <option value="">radius</option>
        <option value="500">500</option>
        <option value="1000">1000</option>
        <option value="1500">1500</option>
      </select>
<br>
      <input class="btn btn-primary btn-sm" type="submit" value="Hľadať">
    </form>
  </div>

  <div class="counter"></div>

  <!--
      doplnit
      radius
      rivers
   -->

  <div id="map"></div>


  <script src="/js/script.js"></script>
  </body>

</html>
