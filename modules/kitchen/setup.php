<?php
/**
 * Module: POS
 * Desription: Ability to add custom POS
 * in product creation
 * 
 * @since  1.2
 */
class NYPIZZA_KITCHEN
{		
		
		public $slug = 'kitchen';
		public $folder = 'kitchen';	
		public $table = 'pizza_kitchen';	 



		/**
		 * Default function to load
		 * after instantiation
		 *
		 * @since  1.2
		 */
		function __construct()
		{

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
			add_action( 'get_header', array( $this, 'media' ) );
			add_shortcode( 'nypizza_kitchen', 'pizza_kitchen_shortcode' );	

			add_action('wp_ajax_kitchen_orders', 'pizza_kitchen_orders');
			add_action('wp_ajax_nopriv_kitchen_orders', 'pizza_kitchen_orders');

			add_action('wp_ajax_kitchen_update', 'pizza_kitchen_update');
			add_action('wp_ajax_nopriv_kitchen_update', 'pizza_kitchen_update');
			
		}



		/**
		 * POS Dependencies
		 * load dependencies
		 *
		 * @since  1.2
		 */
		public function dependencies()
		{				
			require_once( NYPIZZA_MODULES . "/{$this->folder}/inc/shortcode.php" );					
		}



		/**
		 * POS Media
		 * styles and scripts
		 *
		 * @since  1.2
		 */
		public function media()
		{
			global $post;			

			if ( $this->slug == $post->post_name ) {
			

				wp_enqueue_style(
					'font-roboto-slab',
					'//fonts.googleapis.com/css?family=Roboto+Slab:400,700'
				);							
				wp_enqueue_style(
					'pizza-kitchen-theme-css',
					NYPIZZA_URI . '/media/css/kitchen.css'
				);					
				
				
				
				wp_enqueue_script(
					'pizza-kitchen-script',
					NYPIZZA_URI . '/media/script/kitchen.js',
					array('jquery'),
					'',
					true
				);				
				wp_localize_script(
					'pizza-kitchen-script', 
					'ajax_object',
					array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => 1234 ) 
				);


			}

		}


}