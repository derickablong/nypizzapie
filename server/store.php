<?php
/**
 * Module: POS
 * Description: Add to Cart
 */
function pizza_pos_add_to_cart( $order_id = 0, $orders = array(), $notes = '', $user_orders = array() ) {

	$user_orders = $orders;

	foreach ($orders as $index => $order) {

		$order = (object) $order;
		
		$cart_item_key = WC()->cart->add_to_cart(
			$order->product_id, 
			$order->quantity,
			0,
			array(),
			array(
				'_pos_order_id' 		=> $order_id,
				'_pos_order_options' 	=> $order->options,				
				'_pos_total' 			=> $order->total,
				'_pos_notes' 			=> $notes
			)		
		);
		

		$user_orders[$index]['key'] = $cart_item_key;

	}

	return $user_orders;
}






/**
 * Module: POS
 * Desription: POS Store Orders
 * 
 * @since  1.2
 */
function pizza_pos_store( $args = array() ) {
	

	WC()->cart->empty_cart();

	$order_id = (int) $_POST['order_id'];	

	switch ($_POST['role']) {


		case 'save':
			

			$orders = pizza_pos_add_to_cart( $order_id, $_POST['orders'], $_POST['notes'] );
			$coupon = sanitize_text_field( $_POST['coupon'] );


			$args = array(
				'user_id' 		=> get_current_user_id(),
				'orders' 		=> json_encode( $orders ),
				'coupon' 		=> $coupon,
				'subtotal' 		=> $_POST['subtotal'],
				'discounts' 	=> $_POST['discounts'],
				'order_total' 	=> $_POST['total'],
				'notes' 		=> sanitize_text_field( $_POST['notes'] )
			);


			if (!empty( $coupon ))
				WC()->cart->add_discount( $coupon );


			$order_id = pizza_pos_save_orders( $order_id, $args );

			break;


		case 'void':
			$order_id = pizza_pos_orders_void( $order_id );
			break;

	}
	

	wp_send_json(array(
		'order_id' 	=> $order_id,
		'orders' 	=> is_array( $orders )? $orders : array()
	));
	

	wp_die();
}




/**
 * Module: POS
 * Desription: Store
 * 
 * @since  1.2
 */
function pizza_pos_save_orders( $order_id, $args ) {

	global $nypizza, $wpdb;
	$table = $wpdb->prefix . $nypizza->pos->table;

	if ($order_id) {
		$wpdb->update(
			$table,
			$args,
			array( 'id' => $order_id )
		);
	} else {
		$wpdb->insert(
			$table,
			$args
		);
		$order_id = $wpdb->insert_id;
	}


	return $order_id;

}




/**
 * Module: POS
 * Desription: Store Void
 * 
 * @since  1.2
 */
function pizza_pos_orders_void( $order_id ) {

	global $nypizza, $wpdb;
	$table = $wpdb->prefix . $nypizza->pos->table;

	$wpdb->delete(
		$table,
		array( 'id' => $order_id )
	);

	return 0;

}




/**
 * Module: POS
 * Desription: Check Out
 * 
 * @since  1.2
 */
function pizza_pos_checkout() {
	wp_send_json( array( 'checkout' => WC()->cart->get_checkout_url() ) );
	wp_die();
}




/**
 * Module: POS
 * Desription: Check Out Complete
 * 
 * @since  1.2
 */
function pizza_pos_checkout_complete( $order_id ) {	
	$args = array(
		'checkout' => 1,
		'checkout_date' => date('Y-m-d h:i:s', time()),
		'wc_id' => $order_id,
		'order_status' => 'processing'
	);

	$current_order = pizza_pos_get_orders();
	$current_order_id = $current_order[0]->id;
    pizza_pos_save_orders( $current_order_id, $args );
}
add_action('woocommerce_thankyou', 'pizza_pos_checkout_complete', 10, 1);




/**
 * Module: POS
 * Desription: Get Orders
 * 
 * @since  1.2
 */
function pizza_pos_get_orders( $user_id = 0, $checkout = 0 ) {
	
	global $nypizza, $wpdb;
	$table = $wpdb->prefix . $nypizza->pos->table;

	if ($user_id <= 0)	
		$user_id = get_current_user_id();
	
	$query = "SELECT * FROM {$table} WHERE user_id = {$user_id}";
	$query .= " AND checkout = {$checkout} ORDER BY id DESC";

	return $wpdb->get_results( $query );
}




/**
 * Module: POS
 * Desription: All Orders
 * 
 * @since  1.2
 */
function pizza_pos_all_orders( $where ) {
	
	global $nypizza, $wpdb;
	$table = $wpdb->prefix . $nypizza->pos->table;	
	
	$query = "SELECT * FROM {$table} WHERE {$where}";

	return $wpdb->get_results( $query );
}





/**
 * Module: POS
 * Description: Add Order Details To Cart
 */
function pizza_pos_force_individual_orders( $cart_item_data, $product_id ) {
  $unique_cart_item_key = md5( microtime() . rand() );
  $cart_item_data['unique_key'] = $unique_cart_item_key;

  return $cart_item_data;
}
add_filter( 'woocommerce_add_cart_item_data', 'pizza_pos_force_individual_orders', 10, 2 );





/**
 * Module: POS
 * Description: Group Options
 */
function pizza_pos_order_group_options( $options = array(), $group = array() ) {	

	$options = (array)$options;

	foreach ( $options as $row => $option ) {

		$option = (array)$option;
		$data = (array)$option['options'];		
		$data['variation'] = $option['variation'];

		$group[ $option['name'] ][] = $data;

	}

	return $group;
}




/**
 * Module: POS
 * Description: Add Order Details To Cart
 */
function pizza_pos_order_details($cart_item_html, $product_data) {
	global $_product;
	
	$details = '';

	if ( array_key_exists('_pos_order_options', $product_data) ) {
		
		$options = pizza_pos_order_group_options( $product_data['_pos_order_options'] );

		foreach ( $options as $group => $option ) {

			$details .= '<div class="pos-cart-details">';
			$details .= '<div class="pos-cart-group">'. $group .'</div>';
			$details .= '<div class="pos-cart-table">';
			$details .= '<table border="0" cellpadding="0" cellspacing="0">';

			foreach ( $option as $data ) {

				$details .= '<tr>';
				
				$details .= '<td>';
				$details .= '<div><strong>'. $data['name'] .'</strong></div>';
				$details .= '<div>'. $data['description'] .'</div>';
				$details .= '</td>';

				$variation = $data['variation'];			
				if ( $variation == 0.5 )
					$variation = 'Half';
				else if ($variation == 1)
					$variation = 'Whole';
				else
					$variation = '---';

				$details .= '<td>';
				$details .= $variation;
				$details .= '</td>';

				$amount = $data['amount'];
				if ($amount > 0)
					$amount = '$' . number_format($data['amount'],2,",",".");
				else
					$amount = 'FREE';


				$details .= '<td>';
				$details .= '<strong>'. $amount .'</strong>';
				$details .= '</td>';

				$details .= '</tr>';

			}

			$details .= '</table>';			
			$details .= '</div>';
			$details .= '</div>';

		}

		$details .= "<script type='text/javascript' id='pos-notes'>jQuery(document).ready(function($){ $('textarea[name=". '"order_comments"' ."]').val('". $product_data['_pos_notes'] ."'); });</script>";

	}

	echo $cart_item_html . $details;
}
add_filter('woocommerce_cart_item_name', 'pizza_pos_order_details', 40, 2);




/**
 * Module: POS
 * Description: Add Order Details To Cart
 */
function pizza_pos_update_product_price( $_cart ){ 

    foreach ( $_cart->cart_contents as $cart_item_key => &$item ) {	    	    	
        if ( array_key_exists('_pos_total', $item) ) {        
        	$item['data']->set_price( $item['_pos_total'] / $item['quantity'] );
        }                
    }

}
add_action( 'woocommerce_before_calculate_totals', 'pizza_pos_update_product_price' );




/**
 * Module: POS
 * Description: Get All Product Categories
 */
function pizza_pos_store_categories( $menus = '' ) {


	$taxonomy     = 'product_cat';
	$orderby      = 'name';  
	$show_count   = 0;      
	$pad_counts   = 0;      
	$hierarchical = 1;      
	$title        = '';  
	$empty        = 0;

	$args = array(
	     'taxonomy'     => $taxonomy,
	     'orderby'      => $orderby,
	     'show_count'   => $show_count,
	     'pad_counts'   => $pad_counts,
	     'hierarchical' => $hierarchical,
	     'title_li'     => $title,
	     'hide_empty'   => $empty
	);

	$all_categories = get_categories( $args );
	

	$menus = '<ul class="pos-menu-nav font-baloo-bhai">';
	$count = 1;

	foreach ($all_categories as $cat) {

		if($cat->category_parent == 0 && strtolower( $cat->name ) != 'uncategorized') {
		
		    $category_id = $cat->term_id;       
			

			$menus .= '<li>';
			$menus .= '<a href="#" data-category="'. $category_id .'" data-name="'. $cat->name .'" class="'. (($count == 1)? 'active' : '') .'">'. $cat->name .'</a>';		    
			$menus .= '</li>';


			$count++;
		    
		}       
	}


	$menus .= '</ul>';


	echo $menus;


}




/**
 * Module: POS
 * Description: Load Products of Category
 */
function pizza_pos_product_categories( $category_id = 0, $tax_query = array(), $html = '', $script = array() ) {

	if ($_POST['category']) {

		$category_id = $_POST['category'];
		$tax_query = array(
			'relation' => 'AND',
			array(
				'taxonomy' 	=> 'product_cat',
				'field' 	=> 'term_id',
				'terms' 	=> $category_id,
				'operator' 	=> 'IN'
			)
		);

	}

	$args = array(
	    'post_type'      => 'product',
	    'posts_per_page' => -1,	    
	    'tax_query' 	 => $tax_query,
	    'orderby' 		 => 'title',
	    'order' 		 => 'ASC'
	);
	

	$loop = new WP_Query( $args );
	$index = 0;	
	
	while ( $loop->have_posts() ): $loop->the_post();
		global $product;


		$product_id = $product->get_id();

		$title = get_the_title();
		$description = trim($product->post->post_excerpt);

		$is_sale = '';				    	
		$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), 'single-post-thumbnail' )[0];
		$regular_price = number_format((float)$product->get_regular_price(), 2, '.', '');
		$sale_price = number_format((float)$product->get_sale_price(), 2, '.', '');

		$price = $regular_price;

		if ( $product->get_sale_price() ) {
			$price = $sale_price;
			$is_sale = 'sale';
		}


					    
		$pos_options = array();
		$options = unserialize( get_option("_pizza_option_{$product_id}") );

		foreach ($options as $option => $ID):

			$data = pizza_options_get_option( $ID );				    		

			$option_prefix = "_pizza_option_override_{$product_id}_option_{$ID}";
			$override_option = get_option($option_prefix);

			$product_options = $data->options;
			if ($override_option)
				$product_options = $override_option;



			if ( $product_options != '' && $data->status == 0 ):
				$pos_options[] = array(
					'ID' => $data->id,
					'name' => $data->name,
					'options' => unserialize( $product_options ),
					'is_multiple' => $data->is_multiple,
					'is_quantity' => $data->allow_quantity,
					'preselected' => $data->preselected,
					'allow' => $data->allow_half,
					'is_logic' => $data->is_logic,
					'logic' => $data->logic,
					'pos_id' => $product_id,
					'category_id' => $category_id
				);
			endif;				    		
							    					    		
		endforeach;
	

		$html .= '<div class="pos-product" data-id="'. $product_id .'">';
			$html .= '<div class="pos-product-thumbnail" style="background-image: url('. $thumbnail .');"></div>';
			$html .= '<div class="pos-product-name font-baloo-bhai">'. $title .'</div>';
			$html .= '<div class="pos-product-footer">';
				$html .= '<span class="pos-product-price font-bowlby-one">$'. $price .'</span>';
				$html .= '<div class="pos-product-cta">';
					$html .= '<a href="#" class="pos-p-edit tooltip" title="Customize Order" data-id="'. $product_id .'"></a>';
					$html .= '<a href="#" class="pos-p-add tooltip" title="Add To Order" data-id="'. $product_id .'"></a>';
				$html .= '</div>';
			$html .= '</div>';
			$html .= '<div class="pos-product-options"></div>';
			$html .= '<a href="#" class="pos-send-to-oven" data-id="'. $product_id .'">Send To My Oven</a>';
		$html .= '</div>';

			
		$data_script = '{';			
		$data_script .= '"product_id": '. $product_id .',';
		$data_script .= '"thumbnail": "'. $thumbnail .'",';
		$data_script .= '"title": "'. $title .'",';
		//$data_script .= '"description": "'. $description .'",';
		$data_script .= '"price": '. $price .',';
		$data_script .= '"sale": "'. $is_sale .'",';
		$data_script .= '"options": '. json_encode($pos_options);
		$data_script .= '}';

		$script[] = $data_script;		

	
		$index++;

	endwhile;

	// if ($index <= 1)
	// 	$html .= '<div class="pos-product"></div>';


	$content = '<div class="category-details">';	
	$content .= '<h1>'. $_POST['name'] .'</h1>';
	$content .= category_description( $category_id );
	$content .= '</div>';
	$content .= '<div class="pos-product-feeds">'. $html .'</div>';


	wp_send_json(
		array(
			'products' => $content,
			'scripts'  => $script
		)
	);

	wp_die();

}




/**
 * Module: POS
 * Description: Coupon
 */
function pizza_pos_coupon() {


	$code = sanitize_text_field( $_POST['coupon'] );
	$total = $_POST['total'];

	$coupon = new WC_Coupon($code);
	$coupon_post = get_post($coupon->id);
	$coupon_data = array(
	    'id' => $coupon->id,
	    'code' => $coupon->code,
	    'type' => $coupon->type,
	    'created_at' => $coupon_post->post_date_gmt,
	    'updated_at' => $coupon_post->post_modified_gmt,
	    'amount' => wc_format_decimal($coupon->coupon_amount, 2),
	    'individual_use' => ( 'yes' === $coupon->individual_use ),
	    'product_ids' => array_map('absint', (array) $coupon->product_ids),
	    'exclude_product_ids' => array_map('absint', (array) $coupon->exclude_product_ids),
	    'usage_limit' => (!empty($coupon->usage_limit) ) ? $coupon->usage_limit : null,
	    'usage_count' => (int) $coupon->usage_count,
	    'expiry_date' => (!empty($coupon->expiry_date) ) ? date('Y-m-d', $coupon->expiry_date) : null,
	    'enable_free_shipping' => $coupon->enable_free_shipping(),
	    'product_category_ids' => array_map('absint', (array) $coupon->product_categories),
	    'exclude_product_category_ids' => array_map('absint', (array) $coupon->exclude_product_categories),
	    'exclude_sale_items' => $coupon->exclude_sale_items(),
	    'minimum_amount' => wc_format_decimal($coupon->minimum_amount, 2),
	    'maximum_amount' => wc_format_decimal($coupon->maximum_amount, 2),
	    'customer_emails' => $coupon->customer_email,
	    'description' => $coupon_post->post_excerpt,
	);

	$usage_left = ($coupon_data['usage_limit'])? ($coupon_data['usage_limit'] - $coupon_data['usage_count']) : 1;

	$valid = 0;
	$message = 'Coupon Usage Limit Reached';

	if ($usage_left > 0) {

	    $message = 'Coupon Valid';		
	    $valid = 1;

	    WC()->cart->add_discount( $code );
	}
	
	wp_send_json(
		array(
			'message' => $message,
			'valid' => $valid,
			'data' => $coupon_data,
			'discounts' => $coupon->get_discount_amount( $total )
		)
	);

	wp_die();
}




/**
 * Module: POS
 * Description: Coupon Add
 */
function pizza_pos_coupon_add() {

	pizza_pos_save_orders(
		$_POST['order_id'],
		array(
			'coupon' => sanitize_text_field( $_POST['coupon'] ),
			'discounts' => $_POST['discounts']
		)
	);

	wp_send_json(
		array(
			'order_id' => $_POST['order_id']
		)
	);

	wp_die();
}




/**
 * Module: POS
 * Description: Employee Transactions
 */
function pizza_pos_transactions() {


	$where = 'user_id = '. get_current_user_id() . ' AND DATE(checkout_date) = CURDATE()';
	$transactions = pizza_pos_all_orders($where);	


	$content .= '<table cellpadding="0" cellspacing="0">';
		$content .= '<thead>';
			$content .= '<tr>';
				$content .= '<th width="50">No.</th>';
				$content .= '<th width="100">Time</th>';
				$content .= '<th>Items</th>';
				$content .= '<th width="100">Total</th>';
			$content .= '</tr>';
		$content .= '</thead>';
		$content .= '<tbody class="pos-reports">';


			$count = 1;
			foreach ($transactions as $trn):
				$total_trn += $trn->order_total;
				



				$details = '';
				$orders = json_decode(stripslashes($trn->orders));

				foreach ($orders as $order) {


					$options = pizza_pos_order_group_options( $order->options );


					$details .= '<div class="pos-trn-name">'. $order->quantity . 'x ' . $order->title .'</div>';

					foreach ( $options as $group => $option ) {

						$details .= '<div class="pos-cart-details">';
						$details .= '<div class="pos-cart-group">'. $group .'</div>';
						$details .= '<div class="pos-cart-table">';
						

						foreach ( $option as $data ) {

							
							$details .= '<div class="pos-trn-options">';

							$details .= '<div class="pos-trn-option">';
							$details .= '<div><strong>'. $data['name'] .'</strong></div>';
							$details .= '<div>'. $data['description'] .'</div>';
							$details .= '</div>';

							$variation = $data['variation'];			
							if ( $variation == 0.5 )
								$variation = 'Half';
							else if ($variation == 1)
								$variation = 'Whole';
							else
								$variation = '---';

							$details .= '<div class="pos-trn-variation">';
							$details .= $variation;
							$details .= '</div>';

							$amount = $data['amount'];
							if ($amount > 0)
								$amount = '$' . number_format($data['amount'],2,",",".");
							else
								$amount = 'FREE';


							$details .= '<div class="pos-trn-amount">';
							$details .= '<strong>'. $amount .'</strong>';
							$details .= '</div>';

							$details .= '</div>';

							

						}

						
						$details .= '</div>';
						$details .= '</div>';

					}


				}//end of foreach $orders
				





				$content .= '<tr>';
					$content .= '<td>#'. $count .'</td>';
					$content .= '<td>'. date('h:i:sa', strtotime($trn->checkout_date)) .'</td>';
					$content .= '<td>'. $details .'</td>';
					$content .= '<td>$'. number_format($trn->order_total, 2) .'</td>';
				$content .= '</tr>';

				$count++;

			endforeach;


		$content .= '</tbody>';
		$content .= '<tfoot>';
			$content .= '<tr>';
				$content .= '<th colspan="3">Total</th>';						
				$content .= '<th width="100">$'. number_format($total_trn, 2) .'</th>';
			$content .= '</tr>';
		$content .= '</tfoot>';
	$content .= '</table>';


	wp_send_json(
		array(
			'transactions' => $content
		)
	);

	wp_die();
}