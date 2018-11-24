<?php
/**
 * Module: Options
 * Desription: Management
 * 
 * @since  1.2
 */
function pizza_options() {

	global $nypizza;


	$option_id = isset( $_GET['option'] )? $_GET['option'] : 0;

	pizza_options_actions( $option_id );
	
	$option_data = pizza_options_get_option( $option_id );

	wp_enqueue_style('sortable-css');	
	wp_enqueue_script('sortable-script');

	wp_enqueue_style('pizza-options-css');	
	wp_enqueue_script('pizza-options-script');
	
	?>

	
	<div class="wrap option-container">

		<div class="option-wrap">



			<div class="option-content">

				<div class="option-iconic option-search">
					<label class="dashicons dashicons-search pos-right" for="search-option"></label>
					<input type="text" value="" id="search-option" placeholder="Search option here" class="option-text">
					<div class="search-feeds"></div>
				</div>

				<?php
				include('lists.php');
				include('fields.php');
				?>
				
			</div>
			
		</div>



		<div id="ny-popup">
			<div class="ny-popup-wrap">
				<a href="#" id="ny-popup-close" class="dashicons dashicons-dismiss"></a>
				<form action="" method="post">
					<h2>Add New Option</h2>
					
					<select name="ny-option-category"  class="option-text" style="width:100%;margin-bottom:10px">
						<option value="">Select Category</option>
						<?php
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

						foreach ($all_categories as $cat) {

							if($cat->category_parent == 0 && strtolower( $cat->name ) != 'uncategorized') { ?>
							
							<option value="<?php echo $cat->name ?>"><?php echo $cat->name ?></option>

							<?php
							}
						}
						?>
					</select>


					<input type="text" name="ny-option-name" placeholder="Enter option name here" class="option-text">
					<div class="ny-popup-cta">
						<div class="option-iconic option-cta">
							<span class="dashicons dashicons-welcome-add-page pos-left"></span>
							<input type="submit" name="ny-option-save" value="Save Option Fields" class="button option-button">	
						</div>
					</div>
				</form>
			</div>
		</div>
		

	</div>



<?php
}




/**
 * Module: Options
 * Desription: Management Actions
 * 
 * @since  1.2
 */
function pizza_options_actions( $option_id = 0 ) {

	global $nypizza, $wpdb;
	$table = $wpdb->prefix . $nypizza->options->table;
	

	/**
	 * Add New Option
	 */
	if ( isset( $_POST['ny-option-save'] ) ) {

		$category = sanitize_text_field( $_POST['ny-option-category'] );
		$option_name = sanitize_text_field( $_POST['ny-option-name'] );

		if ( !empty( $option_name ) ) {

			$wpdb->insert(
				$table,
				array(
					'category' => $category,
					'name' => $option_name					
				)
			);
			?>
			

			<script>
				window.location = '?page=<?php echo $nypizza->options->slug ?>&option=<?php echo $wpdb->insert_id ?>';
			</script>
			
			<?php

		}

	}



	/**
	 * Save Option Fields
	 */
	if ( isset( $_POST['ny-option-fields-save'] ) ) {


		$index = 0;
		$preselected = (isset($_POST['preselected']))? 1 : 0;
		$is_multiple = (isset($_POST['is-multiple']))? 1 : 0;

		if ($preselected)
			$is_multiple = 1;

		$allow_half = (isset($_POST['allow-half']))? 1 : 0;
		$allow_quantity = (isset($_POST['allow-quantity']))? 1 : 0;
		

		$names = $_POST['list-name'];
		$description = $_POST['list-desc'];
		$amounts = $_POST['list-amount'];
		$options = array();

		foreach ( $names as $name ) {
			if ( !empty($name) ) {

				$data = array(
					'name' => sanitize_text_field( $name ),
					'description' => sanitize_text_field( $description[ $index ] ),
					'amount' => $amounts[ $index ]
				);

				$options[] = $data;

			}
			$index++;
		}


		$args = array(
			'is_multiple' => $is_multiple,
			'allow_half' => $allow_half,
			'allow_quantity' => $allow_quantity,
			'preselected' => $preselected,
			'options' => serialize( $options ),
			'is_logic' => 0,
			'logic' => ''
		);


		if ($_POST['option-create-logic-checkbox']) {
			

			$logic_display = $_POST['conditional-logic-display'];
			$logic_group = $_POST['conditional-logic-option-group'];
			$logic_option = $_POST['conditional-logic-option'];

			$args['is_logic'] = 1;
			$args['logic'] = "{$logic_display}::{$logic_group}::{$logic_option}";

		}



		$wpdb->update(
			$table,
			$args,
			array( 'id' => $option_id )
		);

		$nypizza->message['success'][] = 'Option fields saved.';


	}



	/**
	 * Enable Option
	 */
	if ( isset( $_POST['ny-option-fields-enable'] ) ) {

		$wpdb->update(
			$table,
			array( 'status' => 0 ),
			array( 'id' => $option_id )
		);

		$nypizza->message['success'][] = 'Option been enabled.';

	}



	/**
	 * Disable Option
	 */
	if ( isset( $_POST['ny-option-fields-disable'] ) ) {

		$wpdb->update(
			$table,
			array( 'status' => 1 ),
			array( 'id' => $option_id )
		);

		$nypizza->message['success'][] = 'Option been disabled.';

	}


}





/**
 * Module: Options
 * Description: Get related options
 *
 * @since  1.2
 */
function pizza_options_related( $category ) {
	global $nypizza, $wpdb;
	$table = $wpdb->prefix . $nypizza->options->table;

	$query = "SELECT * FROM {$table} WHERE category = '{$category}' ORDER BY name";
	return $wpdb->get_results( $query );
}




/**
 * Module: Options
 * Desription: Management Get All Options
 * 
 * @since  1.2
 */
function pizza_options_options( $search = '', $is_product = false, $limit = 20 ) {
	global $nypizza, $wpdb;
	$table = $wpdb->prefix . $nypizza->options->table;

	$query = "SELECT * FROM {$table} ORDER BY category";
	if ( !empty( $search ) )
		$query = "SELECT * FROM {$table} WHERE name LIKE '%{$search}%' ORDER BY category";
	if ( $is_product )
		$query = "SELECT * FROM {$table} WHERE status = 0  ORDER BY category";
	//$query .= " LIMIT {$limit}";

	return $wpdb->get_results( $query );
}




/**
 * Module: Options
 * Desription: Management Get All Options
 * 
 * @since  1.2
 */
function pizza_options_get_option( $option_id, $return = OBJECT ) {
	global $nypizza, $wpdb;
	$table = $wpdb->prefix . $nypizza->options->table;

	return $wpdb->get_results( "SELECT * FROM {$table} WHERE id = {$option_id}", $return )[0];
}




/**
 * Module: Options
 * Desription: Feeds
 * 
 * @since  1.2
 */
function pizza_options_lists( $option_id = 0, $is_product = false, $group = array() ) {

	global $nypizza;

	$options = pizza_options_options( '', $is_product );

	foreach ( $options as $option ): ?>
	
	

		<?php
		if (!in_array($option->category, $group)) {
			array_push($group, $option->category);
			?>
		
			
			<div class="option-group"><?php echo $option->category ?></div>


			<?php
		}


		$active_option = '';
		if ($option_id == $option->id || in_array($option->id, (array)$option_id)):
			$active_option = 'active-option'; 
		endif;
		?>


		<a href="?page=<?php echo $nypizza->options->slug ?>&option=<?php echo $option->id ?>" class="option-list <?php echo $active_option ?>" data-option="<?php echo $option->id ?>">
			<span class="dashicons dashicons-clipboard"></span>							
			<span class="option-name"><?php echo $option->name ?></span>
			
			<?php if ($option->status): ?>
				<span class="dashicons dashicons-lock"></span>
			<?php endif; ?>

			<?php if (!empty( $active_option )): ?>
				<input type="hidden" name="options[]" value="<?php echo $option->id ?>">
			<?php endif; ?>
		</a>


	<?php endforeach;
}




/**
 * Module: Options
 * Desription: Feeds
 * 
 * @since  1.2
 */
function pizza_options_feeds( $html = '' ) {
	global $nypizza;

	$name = sanitize_text_field( $_POST['name'] );
	$icon = sanitize_text_field( $_POST['icon'] );
	$options = pizza_options_options( $name );

	foreach ( $options as $option ): 
		$html .= '<a href="?page='. $nypizza->options->slug .'&option='. $option->id .'" class="option-list">';
		$html .= '<span class="'. $icon .'"></span>';
		$html .= '<span class="option-name">'. $option->name .'</span>';								
		$html .= '</a>';
	endforeach;

	echo json_encode(
		array(
			'results' => $html
		)
	);
	wp_die();

}





/**
 * Get option fields
 * to override
 */
function pizza_options_fields( $html = '' ) {	

	$post_id = $_POST['post_id'];
	$option_id = $_POST['option_id'];

	$option_prefix = "_pizza_option_override_{$post_id}_option_{$option_id}";
	$override_option = get_option($option_prefix);

	if ($override_option) {		
		$row =  unserialize( $override_option );
	} else {
		$option_data = pizza_options_get_option( $option_id );
		$row =  unserialize( $option_data->options );
	}
	

	$data = is_array( $row )? $row : array();

	ob_start(); ?>

	<a href="#" class="close-override-modal">x</a>

	<div class="option-override-label">
		Override <?php echo $option_data->name ?> Options Pricing?
	</div>


	<?php foreach ($data as $option): ?>
	<div class="override-fields">
		<div class="option-name">
			<span><?php echo $option['name']; ?></span>
		</div>
		<input type="text" name="option-override-price" class="option-override-price" value="<?php echo number_format($option['amount'], 2) ?>">
		<input type="hidden" name="option-override-description" class="option-override-description" value="<?php echo $option['description'] ?>">
	</div>
	<?php endforeach; ?>


	<a href="#" class="option-override-save">Save Changes</a>
	<a href="#" class="option-override-remove">Remove Override</a>

	<input type="hidden" name="option-override-id" class="option-override-id" value="<?php echo $option_id ?>">

	<div style="clear: both;"></div>
	<?php
	$html = count($data)? ob_get_clean() : '';

	echo json_encode(
		array(
			'results' => $html,
			'data' => $data
		)
	);
	wp_die();
}





/**
 * Save override
 */
function pizza_options_override_save() {


	$post_id = $_POST['post_id'];
	$option_id = $_POST['option_id'];
	$option_prefix = "_pizza_option_override_{$post_id}_option_{$option_id}";

	$new_options = $_POST['options'];
	$options = get_option(option_prefix);
	
	if (count( $new_options ) <= 0) {
		delete_option($option_prefix);
	} else if ($options) {
		update_option($option_prefix, serialize( $new_options ));
	} else {
		add_option($option_prefix, serialize( $new_options ));
	}
		



	echo json_encode(
		array(
			'results' => 'Override saved.'
		)
	);
	wp_die();
}