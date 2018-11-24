<?php
/**
 * Module: Register
 * Request Handler
 */
function pizza_register_server() {

	switch ($_REQUEST['handler']) {
		
		case 'signin':
			$response = pizza_register_signin();
			break;

        case 'checkin':
            $response = pizza_register_checkin();
            break;

        case 'checkout':
            $response = pizza_register_checkout();
            break;

		case 'logout':
			$response = pizza_register_logout();
			break;

        case 'user-info':
            $response = pizza_register_current_user();
            break;

        case 'send-to-oven':
            $response = pizza_register_send_to_oven();
            break;

        case 'void':
            $response = pizza_register_void_oven();
            break;

        case 'transactions':
            $response = pizza_register_transactions();
            break;

	}

	wp_send_json( $response );
	wp_die();
}





/**
 * Module: Register
 * Sign In
 */
function pizza_register_signin() {
    if (is_user_logged_in() || pizza_register_signin_user()) {
        $response = array(
            'success' => true,
            'message' => 'Signed in.',
            'user_id' => get_current_user_id()
        );
    } else {
        $response = array(
            'success' => false,
            'message' => 'Invalid pin number.'
        );
    }
	
    return $response;
}





/**
 * Module: Register
 * Check In
 */
function pizza_register_checkin() {
    if (is_user_logged_in() || pizza_register_signin_user()) {
        $response = array(
            'success' => true,
            'message' => 'Checking in.',
            'user_id' => get_current_user_id()
        );
    } else {
        $response = array(
            'success' => false,
            'message' => 'Invalid pin number.'
        );
    }
    
    return $response;
}





/**
 * Module: Register
 * Check In
 */
function pizza_register_checkout() {
    wp_destroy_current_session();
    wp_clear_auth_cookie(); 
    return array(
        'success' => true,
        'message' => 'Checking out.'
    );
}





/**
 * Module: Register
 * Logout
 */
function pizza_register_logout() {
    wp_destroy_current_session();
    wp_clear_auth_cookie(); 
    return array(
        'success' => true,
        'message' => 'Logout. Comeback again later.'
    );
}





/**
 * Module: Register
 * Sign In
 */
function pizza_register_signin_user() {
    $username = 'nypizza_pie';
    $password = $_POST['pin'];

    $login_data = array();
    $login_data['user_login'] = $username;
    $login_data['user_password'] = esc_attr($password);             

    $user = wp_signon( $login_data, false );
    
    try {

        if ( is_wp_error($user) ) {
            return false;
        } else {                
            wp_set_current_user($user->ID, $user->user_login);
            wp_set_auth_cookie($user->ID, true);
            return true;
        }

    } catch (Exception $e) {

    }
}





/**
 * Module: Register
 * Logout
 */
function pizza_register_current_user() {
    $current_user = wp_get_current_user();
    return array(
        'success' => true,
        'message' => 'User verified.',
        'user' => $current_user
    );
}





/**
 * Module: Register
 * Send to Oven
 */
function pizza_register_send_to_oven() {    
  
    $orders = $_POST['orders'];
    $order_type = $_POST['order_type'];
    $notes = $_POST['notes'];


    if (count($orders)) {

        $args = array(
            'user_id'       => get_current_user_id(),
            'order_type'    => $order_type,
            'orders'        => json_encode( $orders ),        
            'subtotal'      => $_POST['subtotal'],        
            'order_total'   => $_POST['order_total'],
            'notes'         => sanitize_text_field( $notes )
        );

        $order_id = pizza_register_save_orders( 0, $args );


        foreach ($orders as $order) {        

            $product = (object) $order;
        
            $cart_item_key = WC()->cart->add_to_cart(
                $product->ID, 
                $product->quantity,
                0,
                array(),
                array(
                    '_register_order_id'         => $product->ID,
                    '_register_order_options'    => $product->options,             
                    '_register_total'            => $product->total,
                    '_register_notes'            => $notes
                )       
            );

            
        }


        return array(
            'success' => true,
            'message' => 'Sending to oven...',
            'order_id' => $order_id       
        );

    }
    else {
        return array(
            'success' => false,
            'message' => 'No orders has been maade.',
            'order_id' => $order_id       
        );
    }

}




/**
 * Module: Register
 * Description: Add Order Details To Cart
 */
function pizza_register_order_details($cart_item_html, $product_data) {
    global $_product;
    
    $details = '';

    if ( array_key_exists('_register_order_options', $product_data) ) {
        
        $option_group = $product_data['_register_order_options'];
    
        if (!is_array($option_group)) return false;

        foreach ( $option_group as $options ) {
            foreach ( $options as $option ) {

                $option_data = (object) $option;

                if ($option_data->selected == 'true') {

                    $details .= '<div class="pos-cart-details">';
                    $details .= '<div class="pos-cart-group">'. $option_data->name .'</div>';
                    $details .= '<div class="pos-cart-table">';
                    $details .= '<table border="0" cellpadding="0" cellspacing="0">';

                
                    foreach ( $option_data->options as $data_option ) {
                        foreach ( $data_option as $data ) {
                            if ($data['selected'] == 'true') {


                                $details .= '<tr>';
                        
                                $details .= '<td>';
                                $details .= '<div><strong>'. $data['name'] .'</strong></div>';
                                $details .= '<div>'. $data['description'] .'</div>';
                                $details .= '</td>';
                                

                                $details .= '<td>';
                                $details .= $data['type'];
                                $details .= '</td>';

                                $amount = $data['amount'];
                                if ($amount > 0) {
                                    $opt_total = $data['amount'] * (($data['type'] == 'Whole')? 1 : 0.5);
                                    $amount = '$' . number_format($opt_total,2,",",".");
                                }
                                else {
                                    $amount = 'FREE';
                                }


                                $details .= '<td>';
                                $details .= '<strong>'. $amount .'</strong>';
                                $details .= '</td>';

                                $details .= '</tr>';


                            }
                        }
                    }


                    $details .= '</table>';         
                    $details .= '</div>';
                    $details .= '</div>';

                }
                

            }
            

        }

        $details .= "<script type='text/javascript' id='pos-notes'>jQuery(document).ready(function($){ $('textarea[name=". '"order_comments"' ."]').val('". $product_data['_register_notes'] ."'); });</script>";

    }

    echo $cart_item_html . $details;
}
add_filter('woocommerce_cart_item_name', 'pizza_register_order_details', 40, 2);





/**
 * Module: Register
 * Description: Add Order Details To Cart
 */
function pizza_register_force_individual_orders( $cart_item_data, $product_id ) {
  $unique_cart_item_key = md5( microtime() . rand() );
  $cart_item_data['unique_key'] = $unique_cart_item_key;

  return $cart_item_data;
}
add_filter( 'woocommerce_add_cart_item_data', 'pizza_register_force_individual_orders', 10, 2 );




/**
 * Module: Register
 * Description: Add Order Details To Cart
 */
function pizza_register_update_product_price( $_cart ){ 

    foreach ( $_cart->cart_contents as $cart_item_key => &$item ) {                 
        if ( array_key_exists('_register_total', $item) ) {        
            $item['data']->set_price( $item['_register_total'] / $item['quantity'] );
        }                
    }

}
add_action( 'woocommerce_before_calculate_totals', 'pizza_register_update_product_price' );




/**
 * Module: Register
 * Desription: Check Out Complete
 * 
 * @since  1.2
 */
function pizza_register_checkout_complete( $order_id ) { 
    $args = array(
        'checkout' => 1,
        'checkout_date' => date('Y-m-d h:i:s', time()),
        'wc_id' => $order_id,
        'order_status' => 'processing'
    );

    $current_order = pizza_register_get_orders();
    $current_order_id = $current_order[0]->id;
    pizza_register_save_orders( $current_order_id, $args );
}
add_action('woocommerce_thankyou', 'pizza_register_checkout_complete', 10, 1);




/**
 * Module: Register
 * Desription: Get Orders
 * 
 * @since  1.2
 */
function pizza_register_get_orders( $user_id = 0, $checkout = 0 ) {
    
    global $nypizza, $wpdb;
    $table = $wpdb->prefix . $nypizza->register->table;

    if ($user_id <= 0)  
        $user_id = get_current_user_id();
    
    $query = "SELECT * FROM {$table} WHERE user_id = {$user_id}";
    $query .= " AND checkout = {$checkout} ORDER BY id DESC";

    return $wpdb->get_results( $query );
}




/**
 * Module: Register
 * Save orders
 * 
 * @since  1.2
 */
function pizza_register_save_orders( $order_id = 0, $args ) {

    global $nypizza, $wpdb;
    $table = $wpdb->prefix . $nypizza->register->table;
    
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
 * Module: Register
 * Desription: All Orders
 * 
 * @since  1.2
 */
function pizza_register_all_orders( $where ) {
    
    global $nypizza, $wpdb;
    $table = $wpdb->prefix . $nypizza->register->table;  
    
    $query = "SELECT * FROM {$table} WHERE {$where}";

    return $wpdb->get_results( $query );
}




/**
 * Module: Register
 * Desription: Void
 * 
 * @since  1.2
 */
function pizza_register_void_oven() {
    if (count($_POST['orders'])) {
        return array(
            'success' => true,
            'message' => 'Orders has been void.'        
        );
    } else {
        return array(
            'success' => false,
            'message' => 'No ordes has been made.'        
        );
    }
}





/**
 * Module: Register
 * Description: Employee Transactions
 */
function pizza_register_transactions() {


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


    return array(
        'success' => true,
        'message' => 'Preparing reports.',
        'content' => $content
    );
}