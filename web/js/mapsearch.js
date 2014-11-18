var map, places, iw;
var markers = [];
var searchTimeout;
var centerMarker;
var hostnameRegexp = new RegExp('^https?://.+?/');
var typeSearch = 'restaurant'; // others types : bar, cafe
var rankBy = 'prominence'; // other criteria : distance
var defaultIcon = 'img/mapicons/restaurant.png';
var activeIcon = 'img/mapicons-active/restaurant.png';
var countryRestrict = { 'country': 'fr' };


function initialize() {
    var myOptions = {
        zoom: 12,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        streetViewControl: false
    }
    map = new google.maps.Map(document.getElementById('map_canvas'), myOptions);

    /*
     * Autocomplete
     */
    var input = document.getElementById('map-search-input');
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

//    var southWest = new google.maps.LatLng( 25.341233, 68.289986 );
//    var northEast = new google.maps.LatLng( 25.450715, 68.428345 );
//    var hyderabadBounds = new google.maps.LatLngBounds( southWest, northEast );

    var autocomplete = new google.maps.places.Autocomplete(input,
        {
            componentRestrictions: countryRestrict
            //bounds: hyderabadBounds,
        });
    autocomplete.bindTo('bounds', map);

    var infowindow = new google.maps.InfoWindow();
    var marker = new google.maps.Marker({
        map: map,
        anchorPoint: new google.maps.Point(0, -29)
    });

    // clear on focus
    input.onfocus = function() {
        this.value = '';
    }

    google.maps.event.addListener(autocomplete, 'place_changed', function() {
        infowindow.close();
        marker.setVisible(false);
        var place = autocomplete.getPlace();
        if (!place.geometry) {
            return;
        }

        // If the place has a geometry, then present it on a map.
        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);  // Why 17? Because it looks good.
        }

        // show marker and infowindow only if it is a restaurant
        if ($.inArray(typeSearch, place.types) == -1) {
            return;
        }

        marker.setIcon(/** @type {google.maps.Icon} */({
            url: activeIcon,
            size: new google.maps.Size(71, 71),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(17, 34),
            scaledSize: new google.maps.Size(35, 35)
        }));
        marker.setPosition(place.geometry.location);
        marker.setVisible(true);

        //google.maps.event.addListener(marker, 'click', getDetails(place, i));
        infowindow.setContent(getIWContent(place));
        infowindow.open(map, marker);

//        var address = '';
//        if (place.address_components) {
//            address = [
//                (place.address_components[0] && place.address_components[0].short_name || ''),
//                (place.address_components[1] && place.address_components[1].short_name || ''),
//                (place.address_components[2] && place.address_components[2].short_name || '')
//            ].join(' ');
//        }
    });
    /*
     * End autocomplete
     */


    // places on the map
    places = new google.maps.places.PlacesService(map);
    google.maps.event.addListener(map, 'tilesloaded', tilesLoaded);

    // Try HTML5 geolocation
    if(navigator.geolocation) {
        showMyAlertModal('We try to geolocate you.', 'Please wait...');

        navigator.geolocation.getCurrentPosition(function(position) {
            var pos = new google.maps.LatLng(position.coords.latitude,
                position.coords.longitude);
            map.setCenter(pos);
            map.setZoom(17);  // Why 17? Because it looks good.

            // show info
            showMyAlertModal('You are now geolocated !', 'Yeah !');

            // hide modal
            window.setTimeout(hideMyAlertModal, 1000);

        }, function() {
            handleNoGeolocation(true);
        });
    } else {
        // Browser doesn't support Geolocation
        handleNoGeolocation(false);
    }



//    document.getElementById('keyword').onkeyup = function(e) {
//        if (!e) var e = window.event;
//        if (e.keyCode != 13) return;
//        document.getElementById('keyword').blur();
//        var keyword = document.getElementById('keyword').value;
//        search(keyword);
//    }
}

function tilesLoaded() {
    search();
    google.maps.event.clearListeners(map, 'tilesloaded');
    google.maps.event.addListener(map, 'zoom_changed', searchIfRankByProminence);
    google.maps.event.addListener(map, 'dragend', search);
}

function searchIfRankByProminence() {
    if (rankBy == 'prominence') {
        search();
    }
}

function search() {
    clearResults();
    clearMarkers();

    if (searchTimeout) {
        window.clearTimeout(searchTimeout);
    }
    searchTimeout = window.setTimeout(reallyDoSearch, 500);
}

function reallyDoSearch() {
    var search = {};
    search.keyword = '';

//    var keyword = document.getElementById('keyword').value;
//    if (keyword) {
//        search.keyword = keyword;
//    }

    if (typeSearch != 'establishment') {
        search.types = [typeSearch];
    }

    if (rankBy == 'distance' && (search.types || search.keyword)) {
        search.rankBy = google.maps.places.RankBy.DISTANCE;
        search.location = map.getCenter();
        centerMarker = new google.maps.Marker({
            position: search.location,
            animation: google.maps.Animation.DROP,
            map: map
        });
    } else {
        search.bounds = map.getBounds();
    }

    places.search(search, function(results, status) {
        if (status == google.maps.places.PlacesServiceStatus.OK) {
            for (var i = 0; i < results.length; i++) {
                markers.push(new google.maps.Marker({
                    position: results[i].geometry.location,
                    animation: google.maps.Animation.DROP,
                    icon: defaultIcon
                }));
                google.maps.event.addListener(markers[i], 'click', getDetails(results[i], i));
                window.setTimeout(dropMarker(i), i * 50);
                addResult(results[i], i);
            }
        } else {
            handlePlacesError(status)
        }
    });
}

function handlePlacesError(status) {
    var serviceStatus = google.maps.places.PlacesServiceStatus;
    if (status == serviceStatus.ZERO_RESULTS) {
        return;
        //var content = 'No result was found for this request.';
    } else if (status == serviceStatus.ERROR) {
        var content = 'There was a problem contacting the Google servers.';
    } else if (status == serviceStatus.INVALID_REQUEST) {
        var content = 'This request was invalid.';
    } else if (status == serviceStatus.OVER_QUERY_LIMIT) {
        var content = 'The webpage has gone over its request quota. Please try later';
    } else if (status == serviceStatus.REQUEST_DENIED) {
        var content = 'The webpage is not allowed to use the PlacesService.';
    } else { // UNKNOWN_ERROR
        var content = 'The PlacesService request could not be processed due to a server error. The request may succeed if you try again.';
    }

    showMyAlertModal(content);
}


function clearMarkers() {
    for (var i = 0; i < markers.length; i++) {
        markers[i].setMap(null);
    }
    markers = [];
    if (centerMarker) {
        centerMarker.setMap(null);
    }
}

function dropMarker(i) {
    return function() {
        if (markers[i]) {
            markers[i].setMap(map);
        }
    }
}

function addResult(result, i) {
    var results = document.getElementById('results');
    var tr = document.createElement('tr');
    tr.style.backgroundColor = (i% 2 == 0 ? '#F0F0F0' : '#FFFFFF');
    tr.onclick = function() {
        google.maps.event.trigger(markers[i], 'click');
    };

    var iconTd = document.createElement('td');
    var nameTd = document.createElement('td');
    var icon = document.createElement('img');
    icon.src = defaultIcon;
    icon.setAttribute('class', 'placeIcon');
    icon.setAttribute('className', 'placeIcon');
    var name = document.createTextNode(result.name);
    iconTd.appendChild(icon);
    nameTd.appendChild(name);
    tr.appendChild(iconTd);
    tr.appendChild(nameTd);
    results.appendChild(tr);
}

function clearResults() {
    var results = document.getElementById('results');
    while (results.childNodes[0]) {
        results.removeChild(results.childNodes[0]);
    }
}

function getDetails(result, i) {
    return function() {
        places.getDetails({
            reference: result.reference
        }, showInfoWindow(i));
    }
}

function showInfoWindow(i) {
    return function(place, status) {
        if (iw) {
            iw.close();
            iw = null;
        }

        if (status == google.maps.places.PlacesServiceStatus.OK) {
            iw = new google.maps.InfoWindow({
                content: getIWContent(place)
            });
            iw.open(map, markers[i]);
        }
    }
}

function getIWContent(place) {
    var content = '';
    content += '<b><a href="' + place.id + '">' + place.name + '</a></b><br>';
    content += place.vicinity;
//    content += '<table>';
//    content += '<tr class="iw_table_row">';
//    content += '<td style="text-align: right"><img class="hotelIcon" src="' + place.icon + '"/></td>';
//    content += '<td><b><a href="' + place.url + '">' + place.name + '</a></b></td></tr>';
//    content += '<tr class="iw_table_row"><td class="iw_attribute_name">Address:</td><td>' + place.vicinity + '</td></tr>';
//    if (place.formatted_phone_number) {
//        content += '<tr class="iw_table_row"><td class="iw_attribute_name">Telephone:</td><td>' + place.formatted_phone_number + '</td></tr>';
//    }
//    if (place.rating) {
//        var ratingHtml = '';
//        for (var i = 0; i < 5; i++) {
//            if (place.rating < (i + 0.5)) {
//                ratingHtml += '&#10025;';
//            } else {
//                ratingHtml += '&#10029;';
//            }
//        }
//        content += '<tr class="iw_table_row"><td class="iw_attribute_name">Rating:</td><td><span id="rating">' + ratingHtml + '</span></td></tr>';
//    }
//    if (place.website) {
//        var fullUrl = place.website;
//        var website = hostnameRegexp.exec(place.website);
//        if (website == null) {
//            website = 'http://' + place.website + '/';
//            fullUrl = website;
//        }
//        content += '<tr class="iw_table_row"><td class="iw_attribute_name">Website:</td><td><a href="' + fullUrl + '">' + website + '</a></td></tr>';
//    }
//    content += '</table>';
    return content;
}

function handleNoGeolocation(errorFlag) {
    if (errorFlag) {
        var content = 'The Geolocation service is not allowed by your browser.\nPlease search manually an address!';
    } else {
        var content = 'Sorry, your browser doesn\'t support geolocation.';
    }

    var options = {
        map: map,
        position: new google.maps.LatLng(48.858859, 2.3470599), // Paris
        content: content
    };

    map.setCenter(options.position);

    showMyAlertModal(content);
}

google.maps.event.addDomListener(window, 'load', initialize);