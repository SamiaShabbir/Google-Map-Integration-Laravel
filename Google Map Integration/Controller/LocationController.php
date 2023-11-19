<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Routes;
use App\Models\Stops;
use App\Models\Location;

class LocationController extends Controller
{
     public function ShowRoutes(Request $request)
    {
         // Add the id of the route
         $bus_stops=Stops::get();
         $route=Routes::find($request->id);
         $startingPointStop = $bus_stops->find($route->starting_point);
         $endingPointStop = $bus_stops->find($route->ending_point);
         $explode_id=explode(',',$route->stops_list);
         foreach($explode_id as $each_id)
              {
                $stoplist[]=$bus_stops->where('status',1)->find($each_id);
              }

                $getRoutes[]=array
                (

                "id"=>$route->id,
                "title"=>$route->title,
                "starting_point"=>$startingPointStop,
                "ending_point"=>$endingPointStop,
                "stops_list"=>$stoplist,
                "status"=>$route->status,
                "assigned"=>$route->assigned,
                "created_at"=>$route->created_at,

               );

         return view('map.map',compact('getRoutes'));
    }

}
