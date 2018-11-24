<?php
/**
 * Module: Made Orders
 * Desription: Orders front-end
 * 
 * @since  1.2
 */
function pizza_orders_shortcode( $atts ) {

	global $nypizza;
	
	show_admin_bar( false );

	require_once( NYPIZZA_MODULES . "/{$nypizza->orders->folder}/inc/orders.php" );

}