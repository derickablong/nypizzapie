<?php
/**
 * Module: POS
 * Desription: POS front-end
 * 
 * @since  1.2
 */
function pizza_pos_shortcode( $atts ) {

	global $nypizza;
	
	show_admin_bar( false );

	require_once( NYPIZZA_SERVER . "/cart.php" );
	require_once( NYPIZZA_MODULES . "/{$nypizza->pos->folder}/inc/pos.php" );

}