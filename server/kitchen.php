<?php
define('ORDERS_LIMIT', 5);

/**
 * Module: Kitchen
 * Description: Cook Item
 */
function pizza_kitchen_update() {

	$new_orders = '';
	
	$table = $_POST['table'];
	$status = $_POST['status'];

	$order_id = $_POST['db_id'];
	$row_id = $_POST['row_id'];


	if ($table == 'register')
		$all_orders = pizza_register_all_orders("id = {$order_id}");
	else
		$all_orders = pizza_pos_all_orders("id = {$order_id}");

	foreach ($all_orders as $sec => $orders) {

		if ($orders->wc_id <= 0 ) continue;		

		$order = json_decode($orders->orders);
		foreach ( $order as $index => $order_data ) {

			if ($index <> $row_id) continue;

			
			$order_data->status = $status;
			$order[$index] = $order_data;
			
			$new_orders = json_encode($order);

		}
	}


	if ($table == 'register') {
		pizza_register_save_orders(
			$_POST['db_id'],
			array(
				'orders' => $new_orders
			)
		);
	} else {
		pizza_pos_save_orders(
			$_POST['db_id'],
			array(
				'orders' => $new_orders
			)
		);
	}
	

	wp_send_json(array(
		'response' => "Poduct submitted for cooking..."
	));
	wp_die();
}






/**
 * Module: Kitchen
 * Description: Check if not void and cook
 */
function pizza_kitchen_valid( $data ) {

	$data = (array) $data;

	if (array_key_exists('cook', $data)) {
		if ($data['cook'] == 1 || $data['void'] == 1)
			return false;
	}
	return true;
}






/**
 * Module: Kitchen
 * Description: Get all orders
 */
function pizza_kitchen_orders() {

	
	$box_count = 0;
	$filter = "order_status != 'completed'";
	

	$register_orders = pizza_register_all_orders( $filter );
	$prcs_register = pizza_kitchen_register_orders( $register_orders );
	$content = $prcs_register['content'];
	$box_count = $prcs_register['limit'];

	if ($prcs_register['limit'] < ORDERS_LIMIT) {
		$pos_orders = pizza_pos_all_orders( $filter );
		$prcs_pos = pizza_kitchen_pos_orders( $pos_orders );	
		$content .= $prcs_pos['content'];
		$box_count += $prcs_pos['limit'];
	}

	

	$html .= '<div class="kitchen-row">';
	$html .= $content;
	$html .= pizza_kitchen_pane( $box_count );
	$html .= '</div>';

	pizza_kitchen_check_order_status();

	wp_send_json(array(
		'response' => "Getting all orders...",
		'content' => $html		
	));
	wp_die();
}




/**
 * Module: Kitchen
 * Description: Get Register orders
 */
function pizza_kitchen_register_orders( $register_orders, $content = '', $box_count = 0, $count = 1 ) {
	foreach ($register_orders as $orders) {

		if ($orders->wc_id <= 0 ) continue;		

		$order = json_decode($orders->orders);
		foreach ( $order as $index => $order_data ) {

			if (!pizza_kitchen_valid( $order_data ) && $box_count < ORDERS_LIMIT) continue;

			$box_count++;
						
			

			$plug = "{$orders->wc_id}:{$orders->id}:{$index}";

			$datetime = $orders->date_ordered;
			$time = strtotime($datetime);			


			$content .= '<div class="kitchen-col">';
				$content .= '<div class="kitchen-box">';
					
					$content .= '<div class="kitchen-header">';
						$content .= '<div class="col-box priority">';
							$content .= '<span>No: '. $orders->wc_id .'</span>';
						$content .= '</div>';					
						$content .= '<div class="col-box time">';
							$content .= '<span>'. date('M/d/Y H:iA',$time) .'</span>';
						$content .= '</div>';
					$content .= '</div>';

					$content .= '<div class="kitchen-content">';
						
						$content .= '<div class="order-product">';
							$content .= '<span class="order-qty">'. $order_data->quantity .'x</span>';
							$content .= '<span class="order-name">'. $order_data->name .'</span>';
						$content .= '</div>';


			$option_group = $order_data->options;

			foreach ( $option_group as $options ) {
	            foreach ( $options as $option ) {

	                $option_data = (object) $option;

	                if ($option_data->selected == 'true') {

	                    

	                	$content .= '<div class="order-option option-group">';
							$content .= '<span class="option-name">'. $option_data->name .'</span>';								
						$content .= '</div>';


	                
	                    foreach ( $option_data->options as $data_option ) {
	                        foreach ( $data_option as $data ) {


								$content .= '<div class="order-option option-item">';
									$content .= '<span class="option-name">'. $data->name .'</span>';
									$content .= '<span class="option-half">'. $data->type .'</span>';
								$content .= '</div>';

	                        }
	                    }

	                }//end if

	            }
	        }



						


				$content .= '</div>';
				$content .= '<div class="kitchen-footer">'. $box_count .'</div>';


				$status = $order_data->status;


				$content .= '<div class="kitchen-popup">';
					$content .= '<div class="popup-wrap">';
						$content .= '<div class="popup-box">';		

							$content .= '<a href="#" class="kitchen-status '. (($status == 'processing')? 'selected' : '') .'" data-table="register" data-status="processing" data-plug="'. $plug .'"><span>Processing</span></a>';

							$content .= '<a href="#" class="kitchen-status '. (($status == 'completed')? 'selected' : '') .'" data-table="register" data-status="completed" data-plug="'. $plug .'"><span>Completed</span></a>';

							$content .= '<a href="#" class="kitchen-status '. (($status == 'on-hold')? 'selected' : '') .'" data-table="register" data-status="on-hold" data-plug="'. $plug .'"><span>On Hold</span></a>';

							$content .= '<a href="#" class="kitchen-status '. (($status == 'cancelled')? 'selected' : '') .'" data-table="register" data-status="cancelled" data-plug="'. $plug .'"><span>Cancel</span></a>';
							
							
							$content .= '<a href="#" class="kitchen-cancel" data-table="register" data-plug="'. $plug .'"><span>Back</span></a>';

						$content .= '</div>';
					$content .= '</div>';
				$content .= '</div>';


			$content .= '</div>';
		$content .= '</div>';
			


			

		}


	}	


	return array(
		'limit' => $box_count,
		'content' => $content
	);
}




/**
 * Module: Kitchen
 * Description: Get POS orders
 */
function pizza_kitchen_pos_orders( $pos_orders, $content = '', $box_count = 0, $count = 1 ) {
	foreach ($pos_orders as $orders) {

		if ($orders->wc_id <= 0 ) continue;		

		$order = json_decode($orders->orders);
		foreach ( $order as $index => $order_data ) {

			if (!pizza_kitchen_valid( $order_data ) && $box_count < ORDERS_LIMIT) continue;

			$box_count++;

			$options = $order_data->options;			
			foreach ( $options as $option ) {

				$data = $option->options;		
				$data->variation = $option->variation;
				$options_group[ $option->name ][] = $data;				

			}



			

			$plug = "{$orders->wc_id}:{$orders->id}:{$index}";

			$datetime = $orders->date_ordered;
			$time = strtotime($datetime);			


			$content .= '<div class="kitchen-col">';
				$content .= '<div class="kitchen-box">';
					
					$content .= '<div class="kitchen-header">';
						$content .= '<div class="col-box priority">';
							$content .= '<span>No: '. $orders->wc_id .'</span>';
						$content .= '</div>';					
						$content .= '<div class="col-box time">';
							$content .= '<span>'. date('M/d/Y H:iA',$time) .'</span>';
						$content .= '</div>';
					$content .= '</div>';

					$content .= '<div class="kitchen-content">';
						
						$content .= '<div class="order-product">';
							$content .= '<span class="order-qty">'. $order_data->quantity .'x</span>';
							$content .= '<span class="order-name">'. $order_data->title .'</span>';
						$content .= '</div>';



						foreach ( $options_group as $group => $opt_options ) {

							$content .= '<div class="order-option option-group">';
								$content .= '<span class="option-name">'. $group .'</span>';								
							$content .= '</div>';
			

							foreach ($opt_options as $opt_item) {

								$variation = $opt_item->variation;			
								
								if ( $variation == 0.5 )
									$variation = 'Half';
								else if ($variation == 1)
									$variation = 'Whole';
								else
									$variation = '';										


								$content .= '<div class="order-option option-item">';
									$content .= '<span class="option-name">'. $opt_item->name .'</span>';
									$content .= '<span class="option-half">'. $variation .'</span>';
								$content .= '</div>';

							

							}
							

						}


					$content .= '</div>';
					$content .= '<div class="kitchen-footer">'. $box_count .'</div>';


					$status = $order_data->order_status;


					$content .= '<div class="kitchen-popup">';
						$content .= '<div class="popup-wrap">';
							$content .= '<div class="popup-box">';		

								$content .= '<a href="#" class="kitchen-status '. (($status == 'processing')? 'selected' : '') .'" data-table="pos" data-status="processing" data-plug="'. $plug .'"><span>Processing</span></a>';

								$content .= '<a href="#" class="kitchen-status '. (($status == 'completed')? 'selected' : '') .'" data-table="pos" data-status="completed" data-plug="'. $plug .'"><span>Completed</span></a>';

								$content .= '<a href="#" class="kitchen-status '. (($status == 'on-hold')? 'selected' : '') .'" data-table="pos" data-status="on-hold" data-plug="'. $plug .'"><span>On Hold</span></a>';

								$content .= '<a href="#" class="kitchen-status '. (($status == 'cancelled')? 'selected' : '') .'" data-table="pos" data-status="cancelled" data-plug="'. $plug .'"><span>Cancel</span></a>';
								
								
								$content .= '<a href="#" class="kitchen-cancel" data-table="pos" data-plug="'. $plug .'"><span>Back</span></a>';

							$content .= '</div>';
						$content .= '</div>';
					$content .= '</div>';


				$content .= '</div>';
			$content .= '</div>';
			


			

		}


	}	


	return array(
		'limit' => $box_count,
		'content' => $content
	);
}





function pizza_kitchen_pane( $box_count ) {
	for (; $box_count < ORDERS_LIMIT; $box_count++) {


		$content .= '<div class="kitchen-col">';
			$content .= '<div class="kitchen-box default">';
				$content .= '<div class="kitchen-content"></div>';
				$content .= '<div class="kitchen-footer">'. ($box_count+1) .'</div>';
			$content .= '</div>';
		$content .= '</div>';


	}
	return $content;
}





function pizza_kitchen_check_order_status() {
	$filter = "order_status != 'completed'";	

	$register_orders = pizza_register_all_orders( $filter );
	pizza_kitchen_update_order_status( 'register', $register_orders );

	$pos_orders = pizza_pos_all_orders( $filter );
	pizza_kitchen_update_order_status( 'pos', $pos_orders );
	
}





function pizza_kitchen_update_order_status( $table, $user_orders, $is_completed = true ) {
	global $wpdb, $nypizza;

	foreach ($user_orders as $orders) {

		if ($orders->wc_id <= 0 ) continue;		

		$order = json_decode($orders->orders);
		foreach ( $order as $index => $order_data ) {
			$status = $order_data->status;
			if ($status != 'processing')
				$is_completed = false;
		}

		if ($is_completed) {
			
			$order = new WC_Order($orders->wc_id);
			$order->update_status('completed');

			if ($table == 'register')
		    	$table = $wpdb->prefix . $nypizza->register->table;	    
		    else
		    	$table = $wpdb->prefix . $nypizza->pos->table;	    
		    
	        $wpdb->update(
	            $table,
	            array( 'order_status' => 'completed' ),
	            array( 'wc_id' => $orders->wc_id )
	        );
		    

		}

	}	
}