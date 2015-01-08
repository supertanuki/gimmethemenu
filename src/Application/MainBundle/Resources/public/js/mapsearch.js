$(document).ready(function() {

    var map, places, iw;
    var markers = [];
    var searchTimeout;
    var centerMarker;
    var hostnameRegexp = new RegExp('^https?://.+?/');
    var typeSearch = ['restaurant', 'bar', 'cafe', 'bakery', 'meal_delivery', 'meal_takeaway'];
    var rankBy = 'prominence'; // other criteria : distance
    var defaultIcon = assets_dir + '/img/mapicons/restaurant.png';
    var activeIcon = assets_dir + '/img/mapicons-active/restaurant.png';
    //var countryRestrict = { 'country': 'fr' };
    var cookieGeolocalisationName = 'geolocalisation_cache';

    function initialize() {

        var map_canvas = document.getElementById('map_canvas');
        if (map_canvas == null) {
            return;
        }

        var myOptions = {
            zoom: 12,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            streetViewControl: false,
            mapTypeControl: false,
            zoomControl: false,
            center: new google.maps.LatLng(48.858859, 2.3470599) // Paris
        }
        map = new google.maps.Map(map_canvas, myOptions);

        /*
         * Autocomplete
         */
        var input = document.getElementById('map-search-input');

        // clear on focus
        $(input).on('focus', function() {
            $(this).val('');
            $(input).popover('destroy');
        });

        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);

        var infowindow = new google.maps.InfoWindow();
        var marker = new google.maps.Marker({
            map: map,
            anchorPoint: new google.maps.Point(0, -29)
        });



        // store the original event binding function
        var _addEventListener = (input.addEventListener) ? input.addEventListener : input.attachEvent;

        function addEventListenerWrapper(type, listener) {
            // Simulate a 'down arrow' keypress on hitting 'return' when no pac suggestion is selected,
            // and then trigger the original listener.
            if (type == "keydown") {
                var orig_listener = listener;
                listener = function(event) {
                    var suggestion_selected = $(".pac-item-selected").length > 0;
                    if (event.which == 13 && !suggestion_selected) {
                        var simulated_downarrow = $.Event("keydown", {
                            keyCode: 40,
                            which: 40
                        });
                        orig_listener.apply(input, [simulated_downarrow]);
                        input.blur();
                    }

                    orig_listener.apply(input, [event]);
                };
            }

            _addEventListener.apply(input, [type, listener]);
        }

        input.addEventListener = addEventListenerWrapper;
        input.attachEvent = addEventListenerWrapper;


        google.maps.event.addListener(autocomplete, 'place_changed', function() {

            infowindow.close();
            marker.setVisible(false);
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                return;
            }

            var isRestaurant = false;
            $.each(typeSearch, function( index, value ) { if ($.inArray(value, place.types) != -1) { isRestaurant = true; return; }});

            // If the place has a geometry, then present it on a map.
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);
            }

            marker.setIcon(/** @type {google.maps.Icon} */({
                url: isRestaurant ? activeIcon : '',
                size: new google.maps.Size(71, 71),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(17, 34),
                scaledSize: new google.maps.Size(35, 35)
            }));
            marker.setPosition(place.geometry.location);
            marker.setVisible(true);

            //google.maps.event.addListener(marker, 'click', getDetails(place, i));
            infowindow.setContent(getIWContent(place, isRestaurant));
            infowindow.open(map, marker);
        });
        /*
         * End autocomplete
         */



        // places on the map
        places = new google.maps.places.PlacesService(map);
        google.maps.event.addListener(map, 'tilesloaded', tilesLoaded);

        var geolocalisation_cache = getCookie(cookieGeolocalisationName);

        if(geolocalisation_cache) {
            var position = geolocalisation_cache.split(',');
            setMapCenter(position[0], position[1]);

            // show info
            showMyAlertModal('We show your last geolocalisation.', '');

            // stop this script
            return;
        }

        // Try HTML5 geolocation
        if(navigator.geolocation) {
            showMyAlertModal('We try to geolocate you.', 'Please wait...');

            navigator.geolocation.getCurrentPosition(function(position) {
                setMapCenter(position.coords.latitude, position.coords.longitude);

                // save geolocalisation for 5 minutes
                setCookie(cookieGeolocalisationName, position.coords.latitude + ',' + position.coords.longitude, 300);

                // show info
                showMyAlertModal('You are now geolocated !', 'Yeah !');

            }, function() {
                handleNoGeolocation(true);
            });
        } else {
            // Browser doesn't support Geolocation
            handleNoGeolocation(false);
        }
    }

    function setMapCenter(lat, lng) {
        var pos = new google.maps.LatLng(lat, lng);
        map.setCenter(pos);
        map.setZoom(17);
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
//        clearResults();
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

        search.types = typeSearch;

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
//                    addResult(results[i], i);
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



    function getIWContent(place, isRestaurant) {

        var components={};
        $.each(place.address_components, function(k,v1) {jQuery.each(v1.types, function(k2, v2){ components[v2]=v1.long_name });})

//        console.log(place);
//        console.log(components);


        var content = '';

        if (isRestaurant) {

            var params = 'place_id=' + encodeRFC5987ValueChars(place.place_id)
                + '&name=' + encodeRFC5987ValueChars(place.name)
                + '&address=' + encodeRFC5987ValueChars(place.vicinity)
                + '&full_address=' + encodeRFC5987ValueChars(place.formatted_address)
                + '&locality=' + encodeRFC5987ValueChars(typeof components.locality != 'undefined' ? components.locality : components.sublocality)
                + '&country=' + encodeRFC5987ValueChars(components.country)
                + '&international_phone_number=' + encodeRFC5987ValueChars(place.international_phone_number)
                + '&location_lat=' + encodeRFC5987ValueChars(place.geometry.location.lat())
                + '&location_lng=' + encodeRFC5987ValueChars(place.geometry.location.lng());

            // console.log(params);

            content += '<h5><a href="' + route_restaurant_get + '?' + params + '">';
            // content += '<img src="' + place.icon + '" width="16" />&nbsp;';
            content += place.name;
            content += '</a></h5>';
            content += '<p>';
            content += place.vicinity;
            // content += '<br>Type : ' + place.types.join(', ');
            content += '</p>';

        } else {

            content += '<h5>';
            content += '<img src="' + place.icon + '" width="16" />&nbsp;';
            content += place.name;
            content += '</h5>';
            content += '<p>';
            content += place.vicinity;
            content += '<br><i>This place is not marked as a restaurant</i>';
            //    content += '<br>Type : ' + place.types.join(', ');
            content += '</p>';

        }


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
            var content = 'The Geolocation service is not allowed by your browser.';
        } else {
            var content = 'Sorry, your browser doesn\'t support geolocation.';
        }

        showMyAlertModal(content);
    //    window.setTimeout(hideMyAlertModal, 3000);
    }

    if (typeof google != 'undefined') {
        google.maps.event.addDomListener(window, 'load', initialize);
    }

});