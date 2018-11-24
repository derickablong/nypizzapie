<?php if ($option_id == 0): ?>
	<div class="option-lists default-lists">
		
		<a href="#" class="option-list option-add" id="ny-new-option">
			<span class="dashicons dashicons-plus"></span>							
			<span class="option-name">Add New Option</span>
		</a>
		
		<?php pizza_options_lists( $option_id ); ?>
		
	</div>
<?php endif; ?>