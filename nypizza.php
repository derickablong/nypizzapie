<?php
/*
Plugin Name: NY Pizza Woocommerce Addons
Plugin URI: http://derickablong.com/
description: Woocommerce Addons
Version: 1.2
Author: Derick Ablong
*/
define( 'NYPIZZA' , plugin_dir_path( __FILE__ ) );
define( 'NYPIZZA_URI', plugins_url() . '/nypizza' );
define( 'NYPIZZA_SERVER', NYPIZZA . 'server' );
define( 'NYPIZZA_MODULES', NYPIZZA . 'modules' );


if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {    
	

	class NYPIZZA
	{

		
		public $options;
		public $pin;
		public $pos;
		public $orders;
		public $kitchen;
		public $shipping;
		public $employee;
		public $register;
		public $message = array();



		/**
		 * Default function to load
		 * after instantiation
		 *
		 * @since  1.2
		 */
		function __construct()
		{			

			add_action('wp_head', array($this, 'controlUI'));

			$this->server();
			$this->modules();
			$this->build();

		}



		/**
		 * Load Server
		 * require dependencies
		 *
		 * @since  1.2
		 */
		public function server()
		{			
			require_once( NYPIZZA_SERVER . '/store.php' );			
			require_once( NYPIZZA_SERVER . '/register.php' );			
			require_once( NYPIZZA_SERVER . '/orders.php' );
			require_once( NYPIZZA_SERVER . '/kitchen.php' );
		}



		/**
		 * Load Modules
		 * require dependencies
		 *
		 * @since  1.2
		 */
		public function modules()
		{
			require_once( NYPIZZA_MODULES . '/options/setup.php' );
			require_once( NYPIZZA_MODULES . '/pin/setup.php' );
			require_once( NYPIZZA_MODULES . '/pos/setup.php' );
			require_once( NYPIZZA_MODULES . '/register/setup.php' );
			require_once( NYPIZZA_MODULES . '/employee/setup.php' );
			require_once( NYPIZZA_MODULES . '/orders/setup.php' );
			require_once( NYPIZZA_MODULES . '/kitchen/setup.php' );
			require_once( NYPIZZA_MODULES . '/shipping/setup.php' );
		}



		/**
		 * Build Modules
		 * instantiate modules
		 *
		 * @since  1.2
		 */
		public function build()
		{
			
			$this->options = new NYPIZZA_OPTIONS();
			$this->pin = new NYPIZZA_PIN();
			$this->pos = new NYPIZZA_POS();
			$this->register = new NYPIZZA_REGISTER();
			$this->employee = new NYPIZZA_EMPLOYEE();
			$this->orders = new NYPIZZA_ORDERS();
			$this->kitchen = new NYPIZZA_KITCHEN();
			//$this->shipping = new NYPIZZA_SHIPPING();

		}



		/**
		 * Message
		 * show message
		 *
		 * @since  1.2
		 */
		public function message( $return = '' )
		{
			if( is_array($this->message ) ) {
				foreach($this->message as $status => $messages) {
					$return .= "<div class='notice notice-{$status} is-dismissible'>";
					foreach( $messages as $message )
						$return .= "<p>{$message}</p>";        
			    	$return .= '</div>';
				}
			}
			echo $return;
		}





		/**
		 * Control UI
		 */
		public function controlUI() {
			if (is_page(229)) {
			?>
			<style type="text/css">
				header, footer { display: none!important; }
				body { background: #fff!important; }
			</style>
			<?php
			}
			?>
			<style type="text/css">
				body.woocommerce-order-received header, body.woocommerce-order-received footer { display: none!important; }
				body.woocommerce-order-received { background: #fff!important; }
			</style>
			<?php
		}
		
	}


	/**
	 * Start Plugin
	 */
	$nypizza = new NYPIZZA();

}