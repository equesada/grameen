var initialLocation;
lat = "#lat";
lng = "#lng";
lat2 = "#lat2";
lng2 = "#lng2";

var siberia = new google.maps.LatLng(60, 105);
var newyork = new google.maps.LatLng(40.69847032728747, -73.9514422416687);
var browserSupportFlag =  new Boolean();
var map;
var infowindow = new google.maps.InfoWindow();
var marker = new google.maps.Marker();
function initialize() {
	var myOptions = {
		zoom: 14,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	map2 = new google.maps.Map(document.getElementById("map_canvas2"), myOptions);
  
	// Try W3C Geolocation method (Preferred)
	if(navigator.geolocation) {
		browserSupportFlag = true;
		navigator.geolocation.getCurrentPosition(function(position) {
			initialLocation = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
			contentString = "Location found using W3C standard";
			map.setCenter(initialLocation);
			map2.setCenter(initialLocation);
			//infowindow.setContent(contentString);
			//infowindow.setPosition(initialLocation);
			//infowindow.open(map);
			marker = createTheMarker(initialLocation, map);
			marker2 = createTheMarker(initialLocation, map2);
			getTheMarker(marker, lat, lng);
			getTheMarker(marker2, lat2, lng2);
			google.maps.event.addListener(map, 'click', function(event) {
				deleteTheMarker(marker, map);
				//var location = new google.maps.LatLng(event.latitude,event.longitude);
				marker = createTheMarker(event.latLng, map);
				getTheMarker(marker, lat, lng);
				google.maps.event.addListener(marker, 'drag', function() {
					getTheMarker(marker, lat, lng);
				});
			});
			google.maps.event.addListener(map2, 'click', function(event) {
				deleteTheMarker(marker2, map2);
				//var location = new google.maps.LatLng(event.latitude,event.longitude);
				marker2 = createTheMarker(event.latLng, map2);
				getTheMarker(marker2, lat2, lng2);
				google.maps.event.addListener(marker2, 'drag', function() {
					getTheMarker(marker2, lat2, lng2);
				});
			});
			google.maps.event.addListener(marker, 'drag', function() {
				getTheMarker(marker, lat, lng);
				getTheMarker(marker2, lat2, lng2);
			});
		}, function() {
			handleNoGeolocation(browserSupportFlag);
		});
	} else if (google.gears) {
    // Try Google Gears Geolocation
	browserSupportFlag = true;
	var geo = google.gears.factory.create('beta.geolocation');
	geo.getCurrentPosition(function(position) {
		initialLocation = new google.maps.LatLng(position.latitude,position.longitude);
		contentString = "Location found using Google Gears";
		map.setCenter(initialLocation);
		var marker = new google.maps.Marker({
			position: initialLocation, 
			draggable:true,
			map: map, 
		});
	}, function() {
		handleNoGeolocation(browserSupportFlag);
    });
	} else {
		// Browser doesn't support Geolocation
		browserSupportFlag = false;
		handleNoGeolocation(browserSupportFlag);
	}
}

function setLatLng(lat, lng)
{
	var myOptions = {
		zoom: 14,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	map2 = new google.maps.Map(document.getElementById("map_canvas2"), myOptions);
	initialLocation = new google.maps.LatLng(lat,lng);
	map2.setCenter(initialLocation);
	marker2 = createTheMarker(initialLocation, map2);
		getTheMarker(marker2, lat2, lng2);
		google.maps.event.addListener(map2, 'click', function(event) {
			deleteTheMarker(marker2);
			//var location = new google.maps.LatLng(event.latitude,event.longitude);
			marker2 = createTheMarker(event.latLng, map2);
			getTheMarker(marker2, lat2, lng2);
			google.maps.event.addListener(marker2, 'drag', function() {
				getTheMarker(marker2, lat2, lng2);
			});
		});
		google.maps.event.addListener(marker2, 'drag', function() {
			getTheMarker(marker2);
		});
}
function handleNoGeolocation(errorFlag) {
  if (errorFlag == true) {
    initialLocation = newyork;
    contentString = "Error: The Geolocation service failed.";
  } else {
    initialLocation = siberia;
    contentString = "Error: Your browser doesn't support geolocation. Are you in Siberia?";
  }
  map.setCenter(initialLocation);
  infowindow.setContent(contentString);
  infowindow.setPosition(initialLocation);
  infowindow.open(map);
}
function getTheMarker(marker, lat, lng)
{
	var theLatLon = marker.getPosition();
	$(lat).val(theLatLon.lat());
	$(lng).val(theLatLon.lng());
}
function createTheMarker(location, which_map)
{
	var m =   new google.maps.Marker({
		  position: location, 
		  draggable:true,
		  map: which_map, 
		});
		return m;
}
function deleteTheMarker(marker)
{
	marker.setMap(null);
	marker = "";
}
