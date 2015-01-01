$(document).ready(function() {
    $('#restaurant-address-link a').on('click', function() {
        $('#restaurantMapModal').modal('show');

        if (typeof restaurantPosition !== 'undefined') {
            var defaultIcon = assets_dir + '/img/mapicons/restaurant.png';
            var activeIcon = assets_dir + '/img/mapicons-active/restaurant.png';

            var myLatlng = new google.maps.LatLng(restaurantPosition.lat, restaurantPosition.lng);
            var mapOptions = {
                zoom: 17,
                center: myLatlng,
                streetViewControl: false
            }

            var map = new google.maps.Map(document.getElementById('map_restaurant'), mapOptions);

            var marker = new google.maps.Marker({
                position: myLatlng,
                map: map,
                animation: google.maps.Animation.DROP,
                icon: defaultIcon
            });
        }
    });
});

