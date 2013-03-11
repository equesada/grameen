var initialLocation;
var siberia = new google.maps.LatLng(60, 105);
var newyork = new google.maps.LatLng(40.69847032728747, -73.9514422416687);
var browserSupportFlag =  new Boolean();
var map;
var infowindow = new google.maps.InfoWindow();
var marker;
function initialize() {
  var myOptions = {
	scrollwheel: false,
    zoom: 14,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
  
  // Try W3C Geolocation method (Preferred)
  if(navigator.geolocation) {
    browserSupportFlag = true;
    navigator.geolocation.getCurrentPosition(function(position) {
      initialLocation = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
      contentString = "Location found using W3C standard";
      map.setCenter(initialLocation);
 	   //marker = createTheMarker(initialLocation);
		//getTheMarker(marker);
		google.maps.event.addListener(map, 'click', function(event) {
			//var location = new google.maps.LatLng(event.latitude,event.longitude);
			if (marker) deleteTheMarker(marker);
			marker = createTheMarker(event.latLng);
			getTheMarker(marker);
			
			google.maps.event.addListener(marker, 'drag', function() {
				getTheMarker(marker);
			});
			
		});
		/*
		google.maps.event.addListener(marker, 'drag', function() {
			getTheMarker(marker);
		});
		*/
     //infowindow.setContent(contentString);
      //infowindow.setPosition(initialLocation);
      //infowindow.open(map);
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
	  /*
      var marker = new google.maps.Marker({
		  position: initialLocation, 
		  draggable:true,
		  map: map, 
		});
		*/
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
	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	initialLocation = new google.maps.LatLng(lat,lng);
	map.setCenter(initialLocation);
	marker = createTheMarker(initialLocation);
		//getTheMarker(marker);
		
		google.maps.event.addListener(map, 'click', function(event) {
			alert('henry');
			deleteTheMarker(marker);
			//var location = new google.maps.LatLng(event.latitude,event.longitude);
			marker = createTheMarker(event.latLng);
			getTheMarker(marker);
			google.maps.event.addListener(marker, 'drag', function() {
				getTheMarker(marker);
			});
		});
		google.maps.event.addListener(marker, 'drag', function() {
			getTheMarker(marker);
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
function getTheMarker(marker)
{
	var theLatLon = marker.getPosition();
	$('#lat').val(theLatLon.lat());
	$('#lng').val(theLatLon.lng());
}
function createTheMarker(location)
{
	var m =   new google.maps.Marker({
		  position: location, 
		  draggable:true,
		  map: map, 
		});
		return m;
}
function deleteTheMarker(marker)
{
	marker.setMap(null);
	marker = "";
}
