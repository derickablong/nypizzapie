var sh = jQuery.noConflict();
var map;

NYPIZZA_SHIPPING = {

	build: function() {

		map = new google.maps.Map(document.getElementById('shipping-map'), {
          center: {lat: -34.397, lng: 150.644},
          zoom: 8
        });
		
	}

}

function initMap() {

	NYPIZZA_SHIPPING.build();

}