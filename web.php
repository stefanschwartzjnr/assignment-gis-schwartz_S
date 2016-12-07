<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests;

Route::get('/api', function (Request $request) {

    $where = 'where name is not null';

    //name select condition
    $title = $request->input("title");
    if (!empty($title)) {
        $where .= " AND lower(name) like lower('%".$title."%')";
    }

    //search limit condition
    $limit = (!empty($request->input("limit"))) ? $request->input("limit") : 1500;


    //where conditions in select (stadium, sports_centre, etc.)
    $choices =  $request->input("choices");
    if (!empty($choices)) {
        $where .= ' AND(';
        $j = 0;
        foreach ($choices as $choice) {
            $or = ' OR ';

            if($j==0){
             $or = '';
             //$where .= " leisure = '".$choice."'";
            }
            if ($choice == 'pitch'){
                $where .= $or." leisure = 'pitch' OR leisure = 'playground'";
            } else {
                $where .= $or." leisure = '".$choice."'";
            }
             $j++;
        }
            $where .= ")";
    };


    $river = $request->input("river");

    //default radius na 500m
    $radius = (!empty($request->input("radius"))) ? $request->input("radius") : '500';


    $lat = $request->input("latitude");
    $long = $request->input("longitude");

    $park_check = $request->input("park_check");

    //select merging data from polygon and point table
    $data = DB::select("SELECT st_asgeojson(st_centroid(way)) AS latlong, name FROM planet_osm_polygon ".$where."
                        union
                        SELECT st_asgeojson(way) AS latlong, name FROM planet_osm_point ".$where."
                        limit ".$limit.";");

//select st_asgeojson(st_centroid(way)) as latlong, name from planet_osm_polygon ".$where."
        //                union

    if(!empty($river)){
    //sportoviska v okoli rieky
    $data = DB::select("  SELECT a.way AS latlong, a.name AS name
                                      FROM (
                                            SELECT 'leisure' AS type, ST_AsGeoJSON(way) AS way, name
                                            FROM planet_osm_point ".$where."
                                             AND ST_Within(
                                                    (way)::geometry,
                                                    (
                                                        SELECT ST_Buffer(ST_Union(way)::geography, ".$radius.")::geometry
                                                        FROM planet_osm_line
                                                        WHERE 1=1
                                                         AND waterway LIKE 'river'
                                                         AND name LIKE '%".$river."%')
                                             )
                                        ) as a ;
                                    ");
    }
    //najblizsie parkovisko v okoli zvoleneho bodu = sportoviska

    if(!empty($park_check) && !empty($lat) && !empty($long)){
    $data = DB::select("SELECT a.latlong AS latlong, a.name AS name
                                        FROM (
                                            SELECT ST_AsGeoJson(ST_Centroid(way)) AS latlong,
                                             name,
                                             ST_Distance(
                                                ST_MakePoint(".$lat.", ".$long."),
                                                ST_Transform(way, 4326)::geography) AS distance
                                            FROM planet_osm_polygon
                                             WHERE 1=1
                                             AND amenity = 'parking'
                                             AND name IS NOT NULL

                                            UNION

                                            SELECT ST_AsGeoJson(way) AS latlong,
                                             name,
                                             ST_Distance(
                                                ST_MakePoint(".$lat.", ".$long."),
                                                ST_Transform(way,4326)::geography) AS distance
                                             FROM planet_osm_point
                                            WHERE 1=1
                                            --AND (shop = 'mall')
                                            AND amenity = 'parking'
                                            AND name IS NOT NULL
                                        ) AS a
                                        ORDER BY distance
                                        LIMIT 1;

                                    ");
    }

    if( empty($park_check) && !empty($lat) && !empty($long) ){
    //vsetky sportoviska v mojom okoli v ramci zadaneho radiusu
    $data = DB::select("   SELECT ST_AsGeoJSON(way) as latlong, name
                                            FROM planet_osm_point
                                            ".$where."
                                            AND ST_Distance(
                                                ST_MakePoint(".$lat.", ".$long."),
                                                ST_Transform(way, 4326)::geography) < ".$radius."
                                    ");

    }
    $new_data = [];
    $i = 0;
    foreach ($data as $item) {
        //print_r($item->latlong);
        $json = json_decode($item->latlong);
        $new_data[$i]['type'] ="Feature";
        $new_data[$i]['geometry']['type'] = "Point";
        $new_data[$i]['geometry']['coordinates'] = $json->coordinates;
        $new_data[$i]['properties']['title'] = $item->name;
        $new_data[$i]['properties']['description'] = "lat: ".$json->coordinates[0]."<br>long: ".$json->coordinates[1];
        $new_data[$i]['properties']['marker-color'] = "#BADA55";
        $i++;
    }

    if(!empty($park_check)&& !empty($lat)&&!empty($long)){
        $new_data[$i]['type'] ="Feature";
        $new_data[$i]['geometry']['type'] = "Point";
        $new_data[$i]['geometry']['coordinates'] = [$lat, $long];
        $new_data[$i]['properties']['title'] = 'Moja poloha';
        $new_data[$i]['properties']['description'] = "lat:".$lat."<br>long:".$long;
        $new_data[$i]['properties']['marker-color'] = "#FF7F50";
    }

/*
    $i = 0;

    foreach ($leisure_near_river as $item) {
        //print_r($item->latlong);
        $json = json_decode($item->latlong);
        $new_data[$i]['type'] ="Feature";
        $new_data[$i]['geometry']['type'] = "Point";
        $new_data[$i]['geometry']['coordinates'] = $json->coordinates;
        $new_data[$i]['properties']['title'] = $item->name;
        $new_data[$i]['properties']['marker-color'] = "#B0E0E6";
        $i++;
    }

    $i = 0;
    foreach ($leisure_near_river as $item) {
        //print_r($item->latlong);
        $json = json_decode($item->latlong);
        $new_data[$i]['type'] ="Feature";
        $new_data[$i]['geometry']['type'] = "Point";
        $new_data[$i]['geometry']['coordinates'] = $json->coordinates;
        $new_data[$i]['properties']['title'] = $item->name;
        $new_data[$i]['properties']['marker-color'] = "#FF7F50";
        $i++;
    }
//    dd($new_data);
*/
    return $new_data;
});



Route::get('/', function () {
    return view('MVC_index');
});


//doplniť funkcionalitu do budúcej stredy
//blizko mojej polohy (defaultna vzdialenost) - done
//v nakupnych centrach
//blizko vody - done
