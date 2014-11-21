var map;
var defaultIcon = 'img/mapicons/restaurant.png';

function initialize() {
    var myLatlng = new google.maps.LatLng(restaurantPosition.lat, restaurantPosition.lng);
    var mapOptions = {
        zoom: 17,
        center: myLatlng,
        streetViewControl: false
    }

    map = new google.maps.Map(document.getElementById('map_restaurant'), mapOptions);

    var marker = new google.maps.Marker({
        position: myLatlng,
        map: map,
        title: 'Hello World!',
        animation: google.maps.Animation.DROP,
        icon: defaultIcon
    });
}

google.maps.event.addDomListener(window, 'load', initialize);