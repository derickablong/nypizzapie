<?php
/**
 * Module: Shipping
 * Desription: Management
 * 
 * @since  1.2
 */
function pizza_shipping(){

	global $nypizza;

	wp_enqueue_style('pizza-shipping-css');	
	wp_enqueue_script('pizza-shipping-map');	
	wp_enqueue_script('pizza-shipping-script');	
	?>
	

	<div class="wrap shipping-wrap">
		<h1>Shipping Management</h1>
		
		<?php $nypizza->message(); ?>
	
		<div class="shipping-location">
			<label for="shipping-location">Enter Your Store Location:</label>
			<input type="text" name="shipping-location" id="shipping-location">
		</div>

		<div class="shipping-map" id="shipping-map"></div>


		<div class="shipping-maximum">
			<label for="">Maxium Miles</label>
		</div>


	</div>


	<?php

}