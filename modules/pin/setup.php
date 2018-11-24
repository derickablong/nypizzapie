<?php
/**
 * Module: Pin
 * Desription: User pin management
 * in product creation
 * 
 * @since  1.2
 */
class NYPIZZA_PIN
{
		
		public $slug = 'nypizza_pin';
		public $folder = 'pin';		 
		public $table = 'pizza_pin';



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
		 * Options Dependencies
		 * load dependencies
		 *
		 * @since  1.2
		 */
		public function dependencies()
		{
			require_once( NYPIZZA_MODULES . "/{$this->folder}/install.php" );
			require_once( NYPIZZA_MODULES . "/{$this->folder}/admin/pin.php" );
		}



		/**
		 * Options Admin Menu
		 * add menu in dashboard
		 *
		 * @since  1.2
		 */
		public function menu()
		{
			add_menu_page(
		        'Pin Management',
		        'Pin Management',
		        'manage_options',
		        $this->slug,
		        'pizza_pin_management',
		        NYPIZZA_URI . '/media/icons/icon.png',
		        30
		    );
		}



		/**
		 * Options Media
		 * styles and scripts
		 *
		 * @since  1.2
		 */
		public function media()
		{

			wp_register_style(
				'pizza-pin-css',
				NYPIZZA_URI . '/media/css/pin.css'
			);						

		}


}