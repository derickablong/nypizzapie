<?php
/**
 * Module: options
 * Desription: Ability to add custom options
 * in product creation
 * 
 * @since  1.2
 */
class NYPIZZA_OPTIONS
{
		
		public $slug = 'nypizza_options';
		public $folder = 'options';		 
		public $table = 'pizza_options';



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
			add_action( 'add_meta_boxes', array( $this, 'metabox' ) );
			add_action( 'save_post', array( $this, 'save_metabox' ) );

			add_action('wp_ajax_options_feed', 'pizza_options_feeds');
			add_action('wp_ajax_nopriv_options_feed', 'pizza_options_feeds');

			add_action('wp_ajax_options_fields', 'pizza_options_fields');
			add_action('wp_ajax_nopriv_options_fields', 'pizza_options_fields');

			add_action('wp_ajax_override_save', 'pizza_options_override_save');
			add_action('wp_ajax_nopriv_override_save', 'pizza_options_override_save');


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
			require_once( NYPIZZA_MODULES . "/{$this->folder}/admin/options.php" );
			require_once( NYPIZZA_MODULES . "/{$this->folder}/metabox/html.php" );
			
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
		        'Pizza Options',
		        'Pizza Options',
		        'manage_options',
		        $this->slug,
		        'pizza_options',
		        NYPIZZA_URI . '/media/icons/icon.png',
		        30
		    );
		}



		/**
		 * Options Metabox
		 * add meta box to product editor page
		 *
		 * @since  1.2
		 */
		public function metabox()
		{
			add_meta_box(
	            'pizza-meta-box',           
	            'Pizza Options',  
	            'pizza_metabox_html',  
	            'product',
	            'normal',
	            'high'
	        );	
		}



		/**
		 * Options Save Metabox
		 * save meta box to product
		 *
		 * @since  1.2
		 */
		public function save_metabox( $post_id )
		{			

			$options = $_POST['options'];
			
			if (get_option("_pizza_option_{$post_id}"))
				update_option("_pizza_option_{$post_id}", serialize( $options ));
			else
				add_option("_pizza_option_{$post_id}", serialize( $options ));

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
				'pizza-options-css',
				NYPIZZA_URI . '/media/css/options.css'
			);			
			wp_register_style(
				'sortable-css',
				'//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css'
			);


			wp_register_script(
				'pizza-options-script',
				NYPIZZA_URI . '/media/script/options.js',
				array('jquery')
			);
			wp_localize_script(
				'pizza-options-script', 
				'ajax_object',
				array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => 1234 ) 
			);


			wp_register_script(
				'jquery-script',
				'//code.jquery.com/jquery-1.12.4.js'
			);
			wp_register_script(
				'sortable-script',
				'//code.jquery.com/ui/1.12.1/jquery-ui.js'
			);

		}


}