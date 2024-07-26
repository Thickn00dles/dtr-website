// Global variables
let map, geofence, userMarker;
const officeLocation = { lat: 17.621654311088232, lng: 121.72186143583569 };
let geofenceRadius;

function initMap() {
    const mapElement = document.getElementById('map');
    if (!mapElement) {
        console.error('Map container not found');
        return;
    }

    try {
        // Detect device type and set geofence radius
        const userAgent = navigator.userAgent.toLowerCase();
        if (userAgent.includes('iphone') || userAgent.includes('ipad') || userAgent.includes('macintosh')) {
            geofenceRadius = 40; // 100 meters for Apple devices
            console.log('Apple device detected');
        } else if (userAgent.includes('android')) {
            geofenceRadius = 20; // 20 meters for Android devices
            console.log('Android device detected');
        } else {
            geofenceRadius = 10; // Default to 70 meters for other devices
            console.log('Other device detected');
        }
    
        $('#deviceInfo').text(`Device type: ${userAgent.includes('iphone') || userAgent.includes('ipad') || userAgent.includes('macintosh') ? 'Apple' : userAgent.includes('android') ? 'Android' : 'Other'}, Geofence radius: ${geofenceRadius} meters`);

        map = new google.maps.Map(mapElement, {
            center: officeLocation,
            zoom: 15
        });

        geofence = new google.maps.Circle({
            strokeColor: '#FF0000',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: '#FF0000',
            fillOpacity: 0.35,
            map: map,
            center: officeLocation,
            radius: geofenceRadius
        });

        new google.maps.Marker({
            position: officeLocation,
            map: map,
            title: 'Office Location'
        });

        // Start getting the user's location
        getCurrentLocation();
    } catch (error) {
        console.error('Error initializing map:', error);
        $('#message').text('Error initializing map. Please refresh the page and try again.');
    }
}

function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            handleLocationSuccess,
            handleLocationError,
            { 
                enableHighAccuracy: true, 
                timeout: 10000, 
                maximumAge: 0
            }
        );
    } else {
        handleLocationError({ code: 0, message: "Geolocation is not supported by this browser." });
    }
}

function handleLocationSuccess(position) {
    const userLocation = {
        lat: position.coords.latitude,
        lng: position.coords.longitude
    };

    updateMap(userLocation);
    updateUI(userLocation);
}

function updateMap(location) {
    if (!userMarker) {
        userMarker = new google.maps.Marker({
            position: location,
            map: map,
            title: 'Your Location',
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 10,
                fillColor: "#4285F4",
                fillOpacity: 1,
                strokeColor: "#FFFFFF",
                strokeWeight: 2
            }
        });
    } else {
        userMarker.setPosition(location);
    }

    map.setCenter(location);
    map.setZoom(15);
}

function updateUI(location) {
    const distance = google.maps.geometry.spherical.computeDistanceBetween(
        new google.maps.LatLng(location),
        new google.maps.LatLng(officeLocation)
    );

    const isInsideGeofence = distance <= geofenceRadius;
    
    console.log(`Distance to office: ${distance.toFixed(2)} meters`);
    console.log(`Geofence radius: ${geofenceRadius} meters`);
    console.log(`Is inside geofence: ${isInsideGeofence}`);

    $('#message').text(isInsideGeofence ? "You are in the DICT area." : "You are not in the DICT area.");
    $('#message').show(); // Make sure the message is visible
    
    const timeInBtn = $('#timeInBtn');
    const timeOutBtn = $('#timeOutBtn');
    timeInBtn.prop('disabled', !isInsideGeofence);
    timeOutBtn.prop('disabled', !isInsideGeofence);

    // Update the map to show both the user's location and the office location
    if (map && userMarker) {
        userMarker.setPosition(location);
        map.setCenter(location);
        map.fitBounds(new google.maps.LatLngBounds(
            new google.maps.LatLng(
                Math.min(location.lat, officeLocation.lat),
                Math.min(location.lng, officeLocation.lng)
            ),
            new google.maps.LatLng(
                Math.max(location.lat, officeLocation.lat),
                Math.max(location.lng, officeLocation.lng)
            )
        ));
    }

    // Update the geofence circle
    if (geofence) {
        geofence.setRadius(geofenceRadius);
    }
}

function handleLocationError(error) {
    console.error('Geolocation error:', error);
    $('#message').text(`Error: ${error.message}. Please check your device settings and ensure you've granted location permissions to this site.`);
}

function loadMapScript() {
  const script = document.createElement('script');
  script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyDigutaV5za1l2PzNmJV_gHHtpB_6Xx4NE&libraries=geometry&callback=initMap`;
  script.async = true;
  script.defer = true;
  document.head.appendChild(script);
}

function startLocationUpdates() {
  setInterval(getCurrentLocation, 30000); // Update every 30 seconds
}

$(document).ready(function() {
    loadMapScript();
    $('#timeInBtn').on('click', function() {
        console.log('Time In button clicked');
        handleTimeAction('timeIn');
    });

    $('#timeOutBtn').on('click', function() {
        console.log('Time Out button clicked');
        handleTimeAction('timeOut');
    });

    startLocationUpdates(); // Start periodic location updates
});