<?php
/**
 * Pin Management
 */
function pizza_pin_management( $message = array(), $response = array() ) {

	$response = pizza_pin_create_user();
	if (count( $response ))
		$message = $response;

	$response = pizza_pin_update();
	if (count( $response ))
		$message = $response;

	$response = pizza_pin_delete();
	if (count( $response ))
		$message = $response;

	pizza_pin_message( $message );
	pizza_pin_create_user_ui();
}





function pizza_pin_save( $user_id = 0, $users = array(), $update = false ) {
	global $nypizza, $wpdb;
	$table = $wpdb->prefix . $nypizza->pin->table;

	if (false == $update) {
		$wpdb->insert(
			$table,
			$users
		);
	} else {
		$wpdb->update(
			$table,
			$users,
			array( 'user_id' => $user_id )
		);
	}
}





function pizza_pin_update( $message = array() ) {
	if (isset($_POST['user_update_pin'])) {
		global $wpdb;


		$user_id = $_POST['user']['ID'];
		$user_name = $_POST['user']['name'];
		$user_pin = $_POST['user']['pin'];
		$user_email = $_POST['user']['email'];


		$user_data = wp_update_user(
			array(
				'ID' => $user_id,
				'user_login' => $user_name,
				'user_nicename' => $user_name,
				'display_name' => $user_name,
				'user_email' => $user_email,
				'user_pass' => $user_pin
			)
		);

		if ( is_wp_error( $user_data ) ) {		    
		    $message['message'] = 'There was an error found. Kindly check if username, email and pin is not empty.';
		    $message['status'] = 'error';
		} else {

			$wpdb->update($wpdb->users, array('user_login' => $user_name), array('ID' => $user_id));

			pizza_pin_save(
		    	$user_id,
		    	array(
		    		'user_id' => $user_id,
		    		'user_name' => $user_name,
		    		'user_email' => $user_email,
		    		'pin' => $user_pin
		    	), 
		    	true
		    );

			$message['message'] = 'User profile updated.';
		    $message['status'] = 'updated';
		}

	}
	return $message;
}





function pizza_pin_create_user( $message = array() ) {
	if (isset($_POST['user_save_pin'])) {
		

		$user_name = $_POST['user']['name'];
		$user_pin = $_POST['user']['pin'];
		$user_email = $_POST['user']['email'];


		$user_id = username_exists( $user_name );

		if (empty($user_name) || empty($user_pin) || empty($user_email)) {
			$message['message'] = 'Username, email or pin number is required.';
			$message['status'] = 'error';
		} else if ( ! $user_id && false == email_exists( $user_email ) ) {		    
		    
		    $user_id = wp_create_user( $user_name, $user_pin, $user_email );
		    pizza_pin_save(
		    	$user_id,
		    	array(
		    		'user_id' => $user_id,
		    		'user_name' => $user_name,
		    		'user_email' => $user_email,
		    		'pin' => $user_pin
		    	), 
		    	false
		    );

		    $message['message'] = 'New user added.';
		    $message['status'] = 'updated';
		} else {
		    $message['message'] = 'Something wrong. Please check username, emal and pin should not already used.';
		    $message['status'] = 'error';
		}

	}
	return $message;
}





function pizza_pin_delete( $message = array() ) {
	if (isset($_GET['delete'])) {
		global $nypizza, $wpdb;
		$table = $wpdb->prefix . $nypizza->pin->table;

		$user_id = $_GET['delete'];
		$user = get_userdata( $user_id );

		if (false == is_numeric($user_id) || false == $user) {
			$message['message'] = 'User profile not found.';
			$message['status'] = 'error';

			return $message;
		}
		

		$wpdb->delete(
			$table,
			array( 'user_id' => $user_id )
		);
		wp_delete_user( $user_id );

		?>
		<script type="text/javascript">
			window.location = '?page=nypizza_pin';
		</script>
		<?php
	}	
}





function pizza_pin_message( $message = array() ) {
	if ( false == array_key_exists('message', $message) ) return false;
	?>

	<div id="message" class="<?php echo $message['status'] ?> notice notice-success is-dismissible">
		<p><?php echo $message['message'] ?></p>
	</div>


	<?php

}





function pizza_pin_create_user_ui() {

	wp_enqueue_style('pizza-pin-css');
	?>


	<div class="wrap pin-wrap">
		<h1 class="wp-heading-inline">Pin Management</h1>

		<div class="pin-row pin-create-user">
			<form method="post" action="">
				<div class="pin-field">
					<label>Username:</label>
					<input type="text" name="user[name]" value="<?php echo $_POST['user']['name'] ?>">
				</div>
				<div class="pin-field">
					<label>Pin Number:</label>
					<input type="text" name="user[pin]" value="<?php echo $_POST['user']['pin'] ?>">
				</div>
				<div class="pin-field">
					<label>Email:</label>
					<input type="text" name="user[email]" value="<?php echo $_POST['user']['email'] ?>">
				</div>
				<input type="submit" name="user_save_pin" class="button button-primary user_save_pin" value="Create">
			</form>
		</div>


		<?php pizza_pin_users(); ?>

	</div>

	<?php
}





function pizza_pin_users() {
	global $nypizza, $wpdb;
	$table = $wpdb->prefix . $nypizza->pin->table;
	?>

	<form method="post" action="">
		<table class="pin-row pin-user-lists" cellpadding="0" cellspacing="0" border="0">
			<thead>
				<tr>
					<th>Username</th>
					<th>Email</th>
					<th>Pin Number</th>
					<th>Action</th>
				</tr>
			</thead>

		<?php
		$users = $wpdb->get_results("SELECT * FROM {$table}");
		foreach ($users as $user): ?>

			<tr>
				<td>
					<input type="hidden" name="user[ID]" value="<?php echo $user->user_id ?>">
					<input type="text" name="user[name]" value="<?php echo $user->user_name ?>">
				</td>
				<td>
					<input type="text" name="user[email]" value="<?php echo $user->user_email ?>">
				</td>
				<td>
					<input type="text" name="user[pin]" value="<?php echo $user->pin ?>">
				</td>
				<td style="text-align: center;">
					<a href="?page=nypizza_pin&delete=<?php echo $user->user_id ?>" class="button button-secondary">Remove</a>
				</td>
			</tr>

		<?php endforeach; ?>
			
			<tr>
				<td colspan="4" style="text-align: right;">
					<input type="submit" name="user_update_pin" class="button button-primary user_update_pin" value="Save Changes">
				</td>
			</tr>

		</table>
	</form>

<?php
}