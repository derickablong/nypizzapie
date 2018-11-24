<?php
/**
 * Module: Employee
 * Desription: Ability to add custom Employee
 * in product creation
 * 
 * @since  1.2
 */
class NYPIZZA_EMPLOYEE
{		
		
		public $slug = 'employee';
		public $folder = 'employee';	
		public $table = 'pizza_order';
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
		 * Employee Login
		 * custom login
		 *
		 * @since  1.2
		 */
		public function employee_login()
		{
			global $nypizza;

			if (isset($_POST['pos-user-login'])) {

				$username = $_POST['username'];
				$password = $_POST['password'];

		        $login_data = array();
		        $login_data['user_login'] = sanitize_user($username);
		        $login_data['user_password'] = esc_attr($password);		        

		        $user = wp_signon( $login_data, false );
		       	
		       	try {

			        if ( is_wp_error($user) ) {

			            $nypizza->employee->error = '<div class="pos-login-error">'. $user->get_error_message() .'</div>';
			        } else {    
			            wp_set_current_user($user->ID, $user->user_login);
				        wp_set_auth_cookie($user->ID, true);				       
			            ?>
						<script>window.location = '?trn=<?php echo time() ?>';</script>
			            <?php			          
			        }

			    } catch (Exception $e) {

			    }
		    }


		    if (isset($_GET['logout'])) {
		    	wp_destroy_current_session();
    			wp_clear_auth_cookie();
    			?>
				<script>window.location = '?user=logout';</script>
    			<?php
		    }
		}



		/**
		 * Hooks
		 * wp add hooks
		 *
		 * @since  1.2
		 */
		public function hooks()
		{		

			add_action( 'after_setup_theme', array( $this, 'employee_login' ) );

			add_action( 'get_header', array( $this, 'media' ) );
			add_shortcode( 'nypizza_employee', 'pizza_employee_shortcode' );
			
			add_action('wp_ajax_pos_store', 'pizza_pos_store');
			add_action('wp_ajax_nopriv_pos_store', 'pizza_pos_store');			
			
			add_action('wp_ajax_pos_categories', 'pizza_pos_product_categories');
			add_action('wp_ajax_nopriv_pos_categories', 'pizza_pos_product_categories');
			
			add_action('wp_ajax_pos_coupon', 'pizza_pos_coupon');
			add_action('wp_ajax_nopriv_pos_coupon', 'pizza_pos_coupon');
			
			add_action('wp_ajax_pos_coupon_add', 'pizza_pos_coupon_add');
			add_action('wp_ajax_nopriv_pos_coupon_add', 'pizza_pos_coupon_add');
			
			add_action('wp_ajax_pos_checkout', 'pizza_pos_checkout');
			add_action('wp_ajax_nopriv_pos_checkout', 'pizza_pos_checkout');

			add_action('wp_ajax_pos_transactions', 'pizza_pos_transactions');
			add_action('wp_ajax_nopriv_pos_transactions', 'pizza_pos_transactions');

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

			wp_enqueue_style(
				'pos-user',
				NYPIZZA_URI . '/media/css/pos-user.css'
			);

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
					'pizza-pos-tooltips-css',
					NYPIZZA_URI . '/media/tooltipster/css/tooltipster.bundle.min.css'
				);					
				wp_enqueue_style(
					'pizza-pos-theme-css',
					NYPIZZA_URI . '/media/css/pos.css'
				);	
				wp_enqueue_style(
					'pizza-pos-employee-css',
					NYPIZZA_URI . '/media/css/employee.css'
				);					


				
				wp_enqueue_script(
					'pizza-pos-tooltips-script',
					NYPIZZA_URI . '/media/tooltipster/js/tooltipster.bundle.min.js',
					array('jquery'),
					'',
					true
				);				
				wp_enqueue_script(
					'pizza-pos-script',
					NYPIZZA_URI . '/media/script/pos.js',
					array('jquery'),
					'',
					true
				);				
				wp_localize_script(
					'pizza-pos-script', 
					'ajax_object',
					array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => 1234 ) 
				);


			}

		}


}