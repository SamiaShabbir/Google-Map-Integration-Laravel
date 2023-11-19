<!DOCTYPE html>
<html>
<body>
    <button id="sendMessageButton">Send Message</button>

<h1>Routes</h1>
 @if($getRoutes)
    @foreach($getRoutes as $route)
        <h4>Route ID: {{ $route['id'] }}</h4>
        <h4>Route Title: {{ $route['title'] }}</h4>

        <h4>Starting Point: {{ $route['starting_point']['title'] }}
        {{ $route['starting_point']['latitude'] }}</h4>

        {{ $route['starting_point']['title'] }}

        <h4>Ending Point: {{ $route['ending_point']['title'] }}</h4>

        <h4>Stops List:</h4>
        <ul>
            @foreach($route['stops_list'] as $stop)
                <li>{{ $stop['title'] }}</li>
            @endforeach
        </ul>

        <h4>Status: {{ $route['status'] }}</h4>
        <h4>Assigned: {{ $route['assigned'] }}</h4>
        <h4>Created At: {{ $route['created_at'] }}</h4>
    @endforeach
@endif
<div id="map" style="width:100%;height:700px;"></div>
<style>
#map {
  height: 100%;
}

/*
 * Optional: Makes the sample page fill the window.
 */
html,
body {
  height: 100%;
  margin: 0;
  padding: 0;
}
</style>
<script src="https://maps.googleapis.com/maps/api/js?key=YourApiKey&callback=initMap"></script>

<script>
  document.getElementById("sendMessageButton").addEventListener("click", function() {
            fetch('http://127.0.0.1:8000/proxy')
            .then(response => response.json())
            .then(data => {
                console.log("data");
            })
            .catch(error => {
                  console.error(error);
            });
        });

            var waypointCoordinates = [
               @foreach($route['stops_list'] as $stop)
                  { location: "{{ $stop['latitude'] }},{{ $stop['longitude'] }}" },
               @endforeach
            ];
let map;

async function initMap() {
  // Request needed libraries.
  const { Map } = await google.maps.importLibrary("maps");
  const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
  const map = new Map(document.getElementById("map"), {
    center: { lat: {{ $route['starting_point']['latitude'] }}, lng: 74.347709 },
    zoom: 14,
    mapId: "4504f8b37365c3d0",
  });
  const marker = new AdvancedMarkerElement({
    map,
    position: { lat: 31.562054, lng: 74.347709 },
  });
}
var stop = new google.maps.LatLng(31.562054,74.347709);

initMap();

function initMap() {
  const map = new google.maps.Map(document.getElementById("map"), {
    zoom: 4,
    center: { lat: {{ $route['starting_point']['latitude'] }}, lng: 74.347709 }, // Australia.
  });
  // Create LatLng objects for the starting and ending points
var startPoint = new google.maps.LatLng({{ $route['starting_point']['latitude'] }}, {{ $route['starting_point']['longitude'] }});
var endPoint = new google.maps.LatLng({{ $route['ending_point']['latitude'] }}, {{ $route['ending_point']['longitude'] }});



  const directionsService = new google.maps.DirectionsService();
  const directionsRenderer = new google.maps.DirectionsRenderer({
    draggable: false,
    map,
    panel: document.getElementById("panel"),
  });

  directionsRenderer.addListener("directions_changed", () => {
    const directions = directionsRenderer.getDirections();

    if (directions) {
      computeTotalDistance(directions);
    }
  });
  displayRoute(
    startPoint,
    endPoint,
    directionsService,
    directionsRenderer,
  );
}

function displayRoute(origin, destination, service, display,waypointCoordinates) {
  service
    .route({
      origin: origin,
      destination: destination,
      waypoints:[
                    @foreach($route['stops_list'] as $stop)
                        { location: "{{ $stop['latitude'] }},{{ $stop['longitude'] }}" },
                    @endforeach
                ],
      travelMode: google.maps.TravelMode.DRIVING,
      avoidTolls: false,
    })
    .then((result) => {
      display.setDirections(result);
    })
    .catch((e) => {
      alert("Could not display directions due to: " + e);
    });
}

function computeTotalDistance(result) {
  let total = 0;
  const myroute = result.routes[0];

  if (!myroute) {
    return;
  }

  for (let i = 0; i < myroute.legs.length; i++) {
    total += myroute.legs[i].distance.value;
  }

  total = total / 1000;
  document.getElementById("total").innerHTML = total + " km";
}

window.initMap = initMap;
</script>


</body>
</html>
