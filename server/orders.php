<?php
/**
 * Module: Orders
 * Description: Change Order Status
 */
function pizza_orders_change_status() {

	$order_id = $_POST['order_id'];	
	$new_status = $_POST['status'];

	$order = wc_get_order( $order_id );
	$order->update_status($new_status);
	
	$args = array(
		'order_status' => $new_status,
		'checkout' => 1
	);
	$id = pizza_pos_save_orders( $_POST['PID'], $args );

	wp_send_json(array(
		'response' => "#{$order_id} status was changed."		
	));
	wp_die();
}






/**
 * Module: Orders
 * Description: Get All Orders
 */
function pizza_orders_get() {

	$status = $_POST['status'];
	$all_orders = pizza_pos_all_orders("order_status = '{$status}'");

	foreach ($all_orders as $user_order) {

		if ($user_order->wc_id <= 0 ) continue;

		$orders_qry = wc_get_order( $user_order->wc_id );
		$order = $orders_qry->get_data();

		
		$timestamp = wc_format_datetime($orders_qry->get_date_created());	
		
		$name = $order['billing']['first_name'] . ' ' . $order['billing']['last_name'];

		
		$content .= '<div class="otr">';
			$content .= '<div class="orow oh-order-name font-baloo-bhai">#'. $user_order->wc_id .' '. $name .'</div>';				
			$content .= '<div class="orow oh-order-date font-baloo-bhai">'. $timestamp .'</div>';
			$content .= '<div class="orow oh-order-status font-baloo-bhai '. $status .'">'. ucwords($status) .'</div>';
			$content .= '<div class="orow oh-order-total font-baloo-bhai">$'. $order['total'] .'</div>';
			$content .= '<div class="orow oh-cta">';
				$content .= '<a href="#orders/details/'. $user_order->wc_id .'" class="order-view" data-id="'. $user_order->wc_id .'"></a>';				
			$content .= '</div>';
		$content .= '</div>';
		$content .= '<input type="hidden" class="pizza_order_id" value="'. $user_order->id .'">';
		

	}


	if (count($all_orders) <= 0) {
		$content .= '<div class="otr">';
			$content .= '<div class="orow oh-order-name font-baloo-bhai">No orders found</div>';				
			$content .= '<div class="orow oh-order-date font-baloo-bhai">---</div>';
			$content .= '<div class="orow oh-order-status font-baloo-bhai '. $status .'">---</div>';
			$content .= '<div class="orow oh-order-total font-baloo-bhai">---</div>';			
		$content .= '</div>';
	}
	

	wp_send_json(array(
		'response' => 'Connected to server.',
		'content' => $content		
	));
	wp_die();
}





/**
 * Module: Orders
 * Description: Orders Details Orders
 */
function pizza_orders_details_orders( $all_orders ) {
	$options_group = array();
	foreach ($all_orders as $user_order) {

		$orders_data = json_decode($user_order->orders);
		
		foreach ( $orders_data as $orders_content ) {

			$options = $orders_content->options;
			foreach ( $options as $option ) {

				$data = $option->options;		
				$data->variation = $option->variation;

				$options_group[ $option->name ][] = $data;				

			}

		}


		foreach ( $orders_data as $orders_content ) {
		
		
			$product = wc_get_product( $orders_content->product_id );
			if (!$product)
				continue;


			$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $product->post->ID ), 'single-post-thumbnail' );


			$content .= '<div class="orders-details-orders font-baloo-bhai">';
				$content .= '<div class="orders-details-wrap">';
					
					$content .= '<div class="orders-box">';
						$content .= '<div class="orders-box-left">';
							$content .= '<img src="'. $thumbnail[0] . '" alt="">';
							$content .= '<div class="orders-box-title">'. $product->get_title() .'</div>';
							$content .= '<div class="orders-box-desc">'. $product->post->post_excerpt .'</div>';
						$content .= '</div>';
						$content .= '<div class="orders-box-right">';
							$content .= '<div class="orders-box-pprice">';
								$content .= 'Regular Price: <span>$'. number_format($orders_content->price,2) .'</span>';
							$content .= '</div>';
							$content .= '<div class="orders-box-cart">';
								$content .= '<div class="box-cart-grp qty">';
									$content .= '<label for="">QTY</label>';
									$content .= '<span>'. $orders_content->quantity .'</span>';
								$content .= '</div>';
								$content .= '<div class="box-cart-grp price">';
									$content .= '<label for="">Price</label>';
									$content .= '<span class="price">$'. number_format($orders_content->total/$orders_content->quantity, 2) .'</span>';
								$content .= '</div>';
								$content .= '<div class="box-cart-grp total">';
									$content .= '<label for="">Total</label>';
									$content .= '<span class="price">$'. number_format($orders_content->total, 2) .'</span>';
								$content .= '</div>';
							$content .= '</div>';



							foreach ($options_group as $group => $options) {


								$content .= '<div class="orders-box-options">';
									$content .= '<div class="box-options-title">'. $group .'</div>';

									foreach ( $options as $option ) {

										$variation = $option->variation;			
										if ( $variation == 0.5 )
											$variation = 'Half';
										else if ($variation == 1)
											$variation = 'Whole';
										else
											$variation = '---';


										$content .= '<div class="box-options-grp">';
											$content .= '<div class="box-option">'. $option->name .'</div>';
											$content .= '<div class="box-option">'. $variation .'</div>';
											$content .= '<div class="box-option price">$'. number_format($option->amount, 2) .'</div>';
										$content .= '</div>';

									}
									
								$content .= '</div>';
							

							}

							
							
						$content .= '</div>';
					$content .= '</div>';

				$content .= '</div>';
			$content .= '</div>';


		}//end foreach
	}//end foreach

	return $content;
}





/**
 * Module: Orders
 * Description: Orders Details Billing
 */
function pizza_orders_details_billing( $order ) {

	$phone = $order->get_billing_phone()? $order->get_billing_phone() : '';
	$email = $order->get_billing_email()? $order->get_billing_email() : '';

	$content .= '<div class="orders-details-orders font-baloo-bhai">';
		$content .= '<div class="orders-details-wrap">';
			$content .= '<div class="orders-box">';
				$content .= '<div class="billing-title">Billing Information</div>';
				$content .= '<div class="billing-row">'. $order->get_formatted_billing_address() .'</div>';

				if ($phone) {
					$content .= '<div class="billing-row">';
						$content .= '<div class="label">Phone</div>';
						$content .= '<a href="tel:'. $phone .'">'. $phone .'</a>';
					$content .= '</div>';
				}

				if ($email) {
					$content .= '<div class="billing-row">';
						$content .= '<div class="label">Email Address</div>';
						$content .= '<a href="mailto:'. $email .'">'. $email .'</a>';
					$content .= '</div>';
				}

			$content .= '</div>';
		$content .= '</div>';
	$content .= '</div>';

	return $content;
}





/**
 * Module: Orders
 * Description: Orders Details Shipping
 */
function pizza_orders_details_shipping( $order, $data ) {

	$content .= '<div class="orders-details-orders font-baloo-bhai">';
		$content .= '<div class="orders-details-wrap">';
			$content .= '<div class="orders-box billing-box">';
				$content .= '<div class="billing-title">Shipping Information</div>';
				$content .= '<div class="billing-row">'. $order->get_formatted_billing_address() .'</div>';
			$content .= '</div>';
			$content .= '<div class="orders-box billing-box billing-status">';
				$content .= '<div class="billing-title">Change Order Status</div>';
				$content .= '<div class="billing-row">';
					$content .= '<div class="change-order-status">';
						$content .= '<ul class="font-baloo-bhai">';
							$content .= '<li data-status="processing" '. (($data['status'] == 'processing')? 'class="active"' : '') .'>Processing</li>';
							$content .= '<li data-status="completed" '. (($data['status'] == 'completed')? 'class="active"' : '') .'>Completed</li>';
							$content .= '<li data-status="on-hold" '. (($data['status'] == 'on-hold')? 'class="active"' : '') .'>On Hold</li>';
							$content .= '<li data-status="pending" '. (($data['status'] == 'pending-payment')? 'class="active"' : '') .'>Pending Payment</li>';
							$content .= '<li data-status="cancelled" '. (($data['status'] == 'cancelled')? 'class="active"' : '') .'>Cancelled</li>';
							$content .= '<li data-status="refunded" '. (($data['status'] == 'refunded')? 'class="active"' : '') .'>Refunded</li>';
							$content .= '<li data-status="failed" '. (($data['status'] == 'failed')? 'class="active"' : '') .'>Failed</li>';
						$content .= '</ul>';
					$content .= '</div>';
				$content .= '</div>';
			$content .= '</div>';
			$content .= '<div class="orders-box billing-box">';
				$content .= '<div class="billing-title">Customer Notes</div>';
				$content .= '<div class="billing-row">'. $data['customer_note'] .'</div>';
			$content .= '</div>';
		$content .= '</div>';
	$content .= '</div>';

	return $content;
}





/**
 * Module: Orders
 * Description: Orders Details
 */
function pizza_orders_details() {

	$order_id = $_POST['order_id'];
	$view = $_POST['view'];

	$all_orders = pizza_pos_all_orders("wc_id = {$order_id}");		


	$orders_qry = wc_get_order( $order_id );
	$order = $orders_qry->get_data();
	

	$content .= '<div class="orders-details-header font-baloo-bhai">';
		$content .= '<div class="orders-details-wrap">';
			$content .= '<div class="grp-col-left">';
				$content .= '<div class="order-title font-baloo-bhai">#'. $order_id .' Order</div>';
				$content .= '<div class="order-method">Payment via '. $order['payment_method_title'] .'</div>';
			$content .= '</div>';
			$content .= '<div class="grp-col-right orders-details-cta">';
				$content .= '<a href="#orders/details/'. $order_id .'" class="menu-orders active">';
					$content .= '<span class="icon"></span>';
					$content .= '<span class="label">Orders</span>';
				$content .= '</a>';
				$content .= '<a href="#" class="menu-billing">';
					$content .= '<span class="icon"></span>';
					$content .= '<span class="label">Billing</span>';
				$content .= '</a>';
				$content .= '<a href="#" class="menu-shipping">';
					$content .= '<span class="icon"></span>';
					$content .= '<span class="label">Shipping</span>';
				$content .= '</a>';
			$content .= '</div>';
		$content .= '</div>';
	$content .= '</div>';


	$content .= '<div class="orders-section orders-header">';
		$content .= '<div class="orders-details-orders font-baloo-bhai">';
			$content .= '<div class="orders-details-wrap">';
				$content .= '<div class="orders-box">';
					$content .= '<div class="orders-date">';
						$content .= '<div class="label">Date Created</div>';
						$content .= '<div class="date">'. wc_format_datetime($orders_qry->get_date_created()) .'</div>';						
					$content .= '</div>';
					$content .= '<div class="orders-total">';
						$content .= '<div class="label">Total Orders</div>';
						$content .= '<div class="amount">$'. number_format($order['total'], 2) .'</div>';						
					$content .= '</div>';
				$content .= '</div>';
			$content .= '</div>';
		$content .= '</div>';
	$content .= '</div>';



	//Orders
	$content .= '<div class="orders-section orders-orders">';
	$content .= pizza_orders_details_orders( $all_orders );
	$content .= '</div>';


	//Billing
	$content .= '<div class="orders-section orders-billing">';
	$content .= pizza_orders_details_billing( $orders_qry );
	$content .= '</div>';


	//Shipping
	$content .= '<div class="orders-section orders-shipping">';
	$content .= pizza_orders_details_shipping( $orders_qry, $order );
	$content .= '</div>';
	
	


	wp_send_json(array(
		'response' => 'Connected to server...',
		'content' => $content,
		'order' => $order
	));
	wp_die();
}