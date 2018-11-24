<?php
/**
 * Module: POS
 * Desription: POS front-end
 * 
 * @since  1.2
 */
function pizza_employee_shortcode( $atts ) {

	global $nypizza;
	
	show_admin_bar( false );

	if (is_user_logged_in()) {
		require_once( NYPIZZA_SERVER . "/cart.php" );
		require_once( NYPIZZA_MODULES . "/{$nypizza->employee->folder}/inc/pos.php" );
	}
	else {
		require_once( NYPIZZA_MODULES . "/{$nypizza->employee->folder}/inc/login.php" );
	}

}