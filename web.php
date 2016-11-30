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
    $limit = (!empty($request->input("limit"))) ? $request->input("limit") : 100;


    //where conditions in select (stadium, sports_centre, etc.)
    $choices =  $request->input("choices");
    if (!empty($choices)) {
        $where .= ' AND(';
        $j = 0;
        foreach ($choices as $choice) {
            if($j==0){
             $where .= " leisure = '".$choice."'";
            }
             $where .= " OR leisure = '".$choice."'";
             $j++;
        }
        $where .= ')';
    }
;
    //select merging data from polygon and point table
    $data = DB::select("select st_asgeojson(st_centroid(way)) as latlong, name from planet_osm_polygon ".$where."
                        union
                        select st_asgeojson(way) as latlong, name from planet_osm_point ".$where."
                        limit ".$limit.";");


    $new_data = [];
    $i = 0;
    foreach ($data as $item) {
        //print_r($item->latlong);
        $json = json_decode($item->latlong);
        $new_data[$i]['type'] ="Feature";
        $new_data[$i]['geometry']['type'] = "Point";
        $new_data[$i]['geometry']['coordinates'] = $json->coordinates;
        $new_data[$i]['properties']['title'] = $item->name;
        $new_data[$i]['properties']['marker-color'] = "#bada55";
        $i++;
    }
//    dd($new_data);

    return $new_data;
});



Route::get('/', function () {
    return view('MVC_index');
});

