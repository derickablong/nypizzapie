<?php
/**
 * Module: Shipping
 * Desription: Ability to add custom shippings
 * in product creation
 * 
 * @since  1.2
 */
class NYPIZZA_SHIPPING
{		
		
		public $slug = 'nypizza_shipping';
		public $folder = 'shipping';	
		public $table = 'pizza_shipping';	 



		/**
		 * Default function to load
		 * after instantiation
		 *
		 * @since  1.2
		 */
		function __construct()
		{

			$this->media();
			$this->dependencies();
			$this->hooks();
			
		}



		/**
		 * Hooks
		 * wp add hooks
		 *
		 * @since  1.2
		 */
		public function hooks()
		{			
			
			add_action( 'admin_menu', array( $this, 'menu' ) );

		}



		/**
		 * Shipping Admin Menu
		 * add menu in dashboard
		 *
		 * @since  1.2
		 */
		public function menu()
		{
			add_menu_page(
		        'Pizza Shipping',
		        'Pizza Shipping',
		        'manage_options',
		        $this->slug,
		        'pizza_shipping',
		        NYPIZZA_URI . '/media/icons/icon.png',
		        30
		    );
		}



		/**
		 * Shipping Dependencies
		 * load dependencies
		 *
		 * @since  1.2
		 */
		public function dependencies()
		{
			
			require_once( NYPIZZA_MODULES . "/{$this->folder}/install.php" );						
			require_once( NYPIZZA_MODULES . "/{$this->folder}/admin/shipping.php" );						
			
		}



		/**
		 * Shipping Media
		 * styles and scripts
		 *
		 * @since  1.2
		 */
		public function media()
		{			

			wp_register_style(
				'pizza-shipping-css',
				NYPIZZA_URI . '/media/css/shipping.css'
			);

			wp_register_script(
				'pizza-shipping-map',
				'//maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap'
			);

			wp_register_script(
				'pizza-shipping-script',
				NYPIZZA_URI . '/media/script/shipping.js'
			);

		}


}