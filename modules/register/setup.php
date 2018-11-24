<?php
/**
 * Module: Employee
 * Desription: Ability to add custom Employee
 * in product creation
 * 
 * @since  1.2
 */
class NYPIZZA_REGISTER
{		
		
		public $slug = 'register';
		public $folder = 'register';	
		public $table = 'pizza_register_order';
		public $error = '';



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
			add_shortcode( 'nypizza_register', 'pizza_register_shortcode' );
			
			add_action('wp_ajax_register_server', 'pizza_register_server');
			add_action('wp_ajax_nopriv_register_server', 'pizza_register_server');	
			
			add_action('wp_head', array($this, 'createCategoryData'));
			add_action('wp_head', array($this, 'createProductData'));
			

		}



		/**
		 * Register Dependencies
		 * load dependencies
		 *
		 * @since  1.2
		 */
		public function dependencies()
		{					
			require_once( NYPIZZA_MODULES . "/{$this->folder}/install.php" );
			require_once( NYPIZZA_MODULES . "/{$this->folder}/inc/shortcode.php" );					
		}



		/**
		 * Register Media
		 * styles and scripts
		 *
		 * @since  1.2
		 */
		public function media()
		{
			global $post;
			

			if ( $this->slug == $post->post_name ) {			

				
				wp_enqueue_style(
					'pizza-lato-fonts',
					'//fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700'
				);	
				wp_enqueue_style(
					'pizza-register-css',
					NYPIZZA_URI . '/media/css/register.css'
				);					


				
				wp_enqueue_script(
					'pizza-data-category',
					NYPIZZA_URI . '/modules/register/data/categories.js',
					array('jquery'),
					'',
					false
				);
				wp_enqueue_script(
					'pizza-data-product',
					NYPIZZA_URI . '/modules/register/data/products.js',
					array('jquery'),
					'',
					false
				);					
				wp_enqueue_script(
					'pizza-register-script',
					NYPIZZA_URI . '/media/script/register.js',
					array('jquery'),
					'',
					true
				);				
				wp_localize_script(
					'pizza-register-script', 
					'ajax_object',
					array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => 1234 ) 
				);


			}

		}





		/**
		 * Create Category Data
		 */
		public function createCategoryData() {		

			$arr = array();

			$categories = array(
				'Appetizers' => 30,
				'Beverages' => 27,
				'Breads' => 19,
				'Calzone' => 20,
				'Calzones' => 31,
				'Desserts' => 28,
				'Drinks' => 21,
				'Fried Items' => 22,
				'Pizza' => 18,
				'Salads' => 23,
				'Sandwiches' => 24,
				'Sauces & Dressings' => 25,
				'Specialty Pizza' => 32,				
				'Stromboli' => 26,
				"Whats Good" => 29,
				'Wings' => 33
			);

			foreach ($categories as $name => $category_id) {

				$tax_query = array(
					'relation' => 'AND',
					array(
						'taxonomy' 	=> 'product_cat',
						'field' 	=> 'term_id',
						'terms' 	=> $category_id,
						'operator' 	=> 'IN'
					)
				);

				$args = array(
				    'post_type'      => 'product',
				    'posts_per_page' => -1,	    
				    'tax_query' 	 => $tax_query,
				    'orderby' 		 => 'title',
				    'order' 		 => 'ASC'
				);
			    $loop = new WP_Query( $args );


			    $script_data = '';
		        $script_data .="[{" . "\r\n";
				$script_data .="ID: {$category_id}," . "\r\n";
				$script_data .="name: '{$name}'," . "\r\n";		    
			    
			    $pid_arr = array();

			    if ( $loop->have_posts() ): while ( $loop->have_posts() ): $loop->the_post();

			        global $product;

			        
			        $ID = $product->get_id();
			        $pid_arr[] = $ID;			        
			        

			    endwhile; endif; wp_reset_postdata();


			    $script_data .="PID: [". implode(',', $pid_arr) ."]" . "\r\n";
			    $script_data .="}]";

			    $arr[] = $script_data;


			}//end foreach		


		    $scripts = 'var data_categories = [' . implode(',', $arr) . '];';
		    


		    $product_file = plugin_dir_path( __FILE__ ) . 'data/categories.js';
		    $file = fopen($product_file, 'w');
		    fwrite($file, $scripts);
		    fclose($file);

		}





		/**
		 * Create Product Data
		 */
		public function createProductData() {		

			$args = array(
			    'post_type'      => 'product',
			    'posts_per_page' => -1,	    			   
			    'orderby' 		 => 'title',
			    'order' 		 => 'ASC'
			);
		    $loop = new WP_Query( $args );

		    
		    $arr = array();

		    if ( $loop->have_posts() ): while ( $loop->have_posts() ): $loop->the_post();

		        global $product;

		        $ID = ($product->get_id())? $product->get_id() : 0;
		        
		        $price = $product->get_price();
		        $price = ($price)? $price : 0;

		        $title = get_the_title();
		        $drescription = trim($product->post->post_excerpt);
		        $quantity = 1;


		        $script_data = '';

		        $script_data .= '[{' . "\r\n";
		        $script_data .= "ID: {$ID}," . "\r\n";
				$script_data .= "name: '{$title}'," . "\r\n";
				$script_data .= "description: '{$description}'," . "\r\n";
				$script_data .= "price: {$price}," . "\r\n";
				$script_data .= "quantity: 1," . "\r\n";
				$script_data .= "total: {$price}," . "\r\n";
				$script_data .= "status: 'processing'," . "\r\n";
				



				
				$options_arr = array();
				$options = unserialize( get_option("_pizza_option_{$product->post->ID}") );

				if (is_array( $options )) {
					foreach ($options as $option => $ID):
						
						$data = pizza_options_get_option( $ID );				    		

						if ( $data->status == 0 ):

							
							$post_options_arr = array();							

							$option_prefix = "_pizza_option_override_{$product->post->ID}_option_{$ID}";
							$override_option = get_option($option_prefix);

							$post_options = unserialize( $data->options );

							if ($override_option)
								$post_options = unserialize( $override_option );


							foreach ($post_options as $option_index => $p_option) {

								$p_option = (object) $p_option;

								$post_options_script = '';
								$post_options_script .= "[{". "\r\n";
								$post_options_script .= "name: '{$p_option->name}',". "\r\n";
								$post_options_script .= "description: '{$p_option->description}',". "\r\n";
								$post_options_script .= "amount: {$p_option->amount},". "\r\n";
								$post_options_script .= "selected: 0,". "\r\n";
								$post_options_script .= "type: 'Whole'". "\r\n";
								$post_options_script .= "}]". "\r\n";

								$post_options_arr[] = $post_options_script;


							}

							$multiple = ($data->is_multiple)? $data->is_multiple : 0;
							$allow_half = ($data->allow_half)? $data->allow_half : 0;
							$allow_quantity = ($data->allow_quantity)? $data->allow_quantity : 0;
							$preselected = ($data->preselected)? $data->preselected : 0;

							$option_id = ($data->id)? $data->id : 0;
							$is_logic = ($data->is_logic)? $data->is_logic : 0;

							$options_data = '';
							$options_data .= "[{". "\r\n";
							$options_data .= "ID: {$option_id},". "\r\n";
							$options_data .= "name: '{$data->name}',". "\r\n";
							$options_data .= "selected: 0,". "\r\n";
							$options_data .= "multiple: {$multiple},". "\r\n";
							$options_data .= "allow_half: {$allow_half},". "\r\n";
							$options_data .= "allow_quantity: {$allow_quantity},". "\r\n";
							$options_data .= "is_logic: {$is_logic},". "\r\n";
							$options_data .= "logic: '{$data->logic}',". "\r\n";
							$options_data .= "preselected: {$preselected},". "\r\n";
							$options_data .= "options: [". "\r\n";
							$options_data .= implode(',', $post_options_arr). "\r\n";
							$options_data .= "]". "\r\n";
							$options_data .= "}]". "\r\n";

							$options_arr[] = $options_data;

							
						endif;				    		
										    					    		
					endforeach;
				}
				

				$script_data .= 'options: [' . "\r\n";
				$script_data .= implode(',', $options_arr) . "\r\n";
				$script_data .= ']' . "\r\n";


				

				$script_data .= '}]';

				$arr[] = $script_data;
		        

		    endwhile; endif; wp_reset_postdata();


		    $scripts = 'var data_products = [' . implode(',', $arr) . '];';
		    


		    $product_file = plugin_dir_path( __FILE__ ) . 'data/products.js';
		    $file = fopen($product_file, 'w');
		    fwrite($file, $scripts);
		    fclose($file);

		}


}