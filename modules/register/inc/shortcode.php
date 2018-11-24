<?php
/**
 * Module: POS
 * Desription: POS front-end
 * 
 * @since  1.2
 */
function pizza_register_shortcode( $atts ) {

	global $nypizza;
	
	show_admin_bar( false );
	
	require_once( NYPIZZA_MODULES . "/{$nypizza->register->folder}/inc/register.php" );

}