<?php
/**
 * Module: POS
 * Desription: Ability to add custom POS
 * in product creation
 * 
 * @since  1.2
 */
class NYPIZZA_ORDERS
{		
		
		public $slug = 'orders';
		public $folder = 'orders';	
		public $table = 'pizza_order';	 



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
			add_shortcode( 'nypizza_orders', 'pizza_orders_shortcode' );

			add_action('wp_ajax_orders_get', 'pizza_orders_get');
			add_action('wp_ajax_nopriv_orders_get', 'pizza_orders_get');

			add_action('wp_ajax_orders_details', 'pizza_orders_details');
			add_action('wp_ajax_nopriv_orders_details', 'pizza_orders_details');

			add_action('wp_ajax_change_status', 'pizza_orders_change_status');
			add_action('wp_ajax_nopriv_change_status', 'pizza_orders_change_status');
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
					'font-bowlby',
					'//fonts.googleapis.com/css?family=Bowlby+One'
				);
				wp_enqueue_style(
					'font-baloo',
					'//fonts.googleapis.com/css?family=Baloo+Bhai'
				);
				wp_enqueue_style(
					'font-alfa',
					'//fonts.googleapis.com/css?family=Alfa+Slab+One'
				);	
				wp_enqueue_style(
					'font-patua',
					'//fonts.googleapis.com/css?family=Patua+One'
				);		
				wp_enqueue_style(
					'pizza-orders-tooltips-css',
					NYPIZZA_URI . '/media/tooltipster/css/tooltipster.bundle.min.css'
				);					
				wp_enqueue_style(
					'pizza-pos-theme-css',
					NYPIZZA_URI . '/media/css/pos.css'
				);
				wp_enqueue_style(
					'pizza-orders-theme-css',
					NYPIZZA_URI . '/media/css/orders.css'
				);					


				
				wp_enqueue_script(
					'pizza-orders-tooltips-script',
					NYPIZZA_URI . '/media/tooltipster/js/tooltipster.bundle.min.js',
					array('jquery'),
					'',
					true
				);				
				wp_enqueue_script(
					'pizza-orders-script',
					NYPIZZA_URI . '/media/script/orders.js',
					array('jquery'),
					'',
					true
				);				
				wp_localize_script(
					'pizza-orders-script', 
					'ajax_object',
					array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => 1234 ) 
				);


			}

		}


}