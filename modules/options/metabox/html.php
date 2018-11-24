<?php
/**
 * Module: options
 * Desription: Metabox HTML
 * 
 * @since  1.2
 */
function pizza_metabox_html( $post ) {

	wp_enqueue_style('pizza-options-css');	
	wp_enqueue_script('pizza-options-script');

	$selected_options = unserialize( get_option("_pizza_option_{$post->ID}") );	
	?>
	
	
	<div class="option-container" style="padding: 30px;">
		<div class="option-wrap" style="margin-top: 0">
			<div class="option-content option-metabox">			

				<div class="option-lists option-option">
					<?php pizza_options_lists( $selected_options, true ); ?>
				</div>			
				
			</div>		
		</div>
	</div>


	<div class="option-override-modal" data-post="<?php echo $post->ID ?>">
		<div class="option-override-modal-wrap option-override-fields"></div>
	</div>


<?php
}