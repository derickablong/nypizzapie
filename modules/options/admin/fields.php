<?php
if ( $option_id ): 

	$row =  unserialize( $option_data->options );
	$data = is_array( $row )? $row : array();

	$related_options = pizza_options_related( $option_data->category );

?>

<div class="option-fields">
	<div class="option-col col-lists">
		<h2>
			<?php echo $option_data->category . ': ' . $option_data->name ?>
			<a href="?page=<?php echo $nypizza->options->slug ?>">
				<span class="dashicons dashicons-arrow-left-alt"></span>
				Back
			</a>
		</h2>
		
		<?php $nypizza->message(); ?>

		<form action="" method="post">
	
			<div class="option-multiple">
				<label for="allow-half" style="margin-right: 20px;">
					<input type="checkbox" name="allow-half" id="allow-half" value="1" <?php echo ($option_data->allow_half)? 'checked' : ''; ?>>
					Allow Half and Half?
				</label>

				<label for="list-multiple" style="margin-right: 20px;">
					<input type="checkbox" name="is-multiple" id="list-multiple" value="1" <?php echo ($option_data->is_multiple)? 'checked' : ''; ?>>
					Allow multiple selections?
				</label>

				<label for="allow-quantity" style="margin-right: 20px;">
					<input type="checkbox" name="allow-quantity" id="allow-quantity" value="1" <?php echo ($option_data->allow_quantity)? 'checked' : ''; ?>>
					Allow quantity?
				</label>

				<label for="preselected">
					<input type="checkbox" name="preselected" id="preselected" value="1" <?php echo ($option_data->preselected)? 'checked' : ''; ?>>
					Preselected?
				</label>
			</div>

			<div id="sortable-header">
				<div class="list-name">
					<strong>Name</strong>
				</div>
				<div class="list-desc">
					<strong>Description</strong>
				</div>
				<div class="list-amount">
					<strong>Amount</strong>
				</div>
			</div>
			
			<ul id="options-sortable">
				
				<?php if (count( $data )): foreach ( $data as $option ): ?>
				
				<li>
					<div class="list-name">
						<a href="#" class="dashicons dashicons-move icon-move move-row"></a>
						<input type="text" value="<?php echo $option['name'] ?>" name="list-name[]" class="input-name">
					</div>
					<div class="list-desc">
						<textarea name="list-desc[]" cols="30" rows="10">
							<?php echo $option['description'] ?>
						</textarea>
					</div>
					<div class="list-amount">
						<input type="text" value="<?php echo $option['amount'] ?>" name="list-amount[]" class="input-amount">
						<a href="#" class="dashicons dashicons-plus icon-plus add-row"></a>
						<a href="#" class="dashicons dashicons-no icon-remove remove-row"></a>
					</div>
				</li>

				<?php endforeach; endif; ?>

				<li>
					<div class="list-name">
						<a href="#" class="dashicons dashicons-move icon-move move-row"></a>
						<input type="text" value="" name="list-name[]" class="input-name">
					</div>
					<div class="list-desc">
						<textarea name="list-desc[]" cols="30" rows="10"></textarea>
					</div>
					<div class="list-amount">
						<input type="text" value="0" name="list-amount[]" class="input-amount">
						<a href="#" class="dashicons dashicons-plus icon-plus add-row"></a>
						<a href="#" class="dashicons dashicons-no icon-remove remove-row"></a>
					</div>
				</li>
			</ul>	


			<div class="options-logic">
				<a href="#" class="option-create-logic <?php echo ($option_data->is_logic)? 'selected' : '' ?>">
					<input type="checkbox" name="option-create-logic-checkbox" value="1" <?php echo ($option_data->is_logic)? 'checked' : '' ?>> Create conditional logic?
				</a>

				<div class="options-logic-conditions" <?php echo ($option_data->is_logic)? 'style="display:block"' : '' ?>>

					<?php $logic = explode('::', $option_data->logic); ?>

					<div class="conditional-field">						
						<select name="conditional-logic-display" class="conditional-logic-display">							
							<option value="show" <?php echo ($logic[0] == 'show')? 'selected' : '' ?>>
								Show this options if
							</option>				
							<option value="hide" <?php echo ($logic[0] == 'hide')? 'selected' : '' ?>>
								Hide this options if
							</option>				
						</select>
					</div>
					<div class="conditional-field">						
						<select name="conditional-logic-option-group" class="conditional-logic-option-group">
							<option value="">---------</option>

							<?php foreach ($related_options as $related): if ($related->id == $option_data->id) continue; ?>			
								<option value="<?php echo $related->id ?>" <?php echo ($logic[1] == $related->id)? 'selected' : '' ?>>
									<?php echo $related->name ?> is equal to
								</option>
							<?php endforeach; ?>

						</select>
					</div>
					<div class="conditional-field">
						<select name="conditional-logic-option" class="conditional-logic-option">
							<option value="">---------</option>

							<?php foreach ($related_options as $related): if ($related->id == $option_data->id) continue; ?>			
								

								<?php $group_options = unserialize($related->options); ?>
								<?php foreach ($group_options as $option): ?>
								<option value="<?php echo $option['name'] ?>" class="grp-option-<?php echo $related->id ?>" <?php echo ($logic[2] == $option['name'])? 'selected' : '' ?>>
									<?php echo $option['name'] ?>
								</option>
								<?php endforeach; ?>


							<?php endforeach; ?>

						</select>											
					</div>
				</div>
			</div>


			<div class="option-footer">			

				<?php if ($option_data->status): ?>
				<div class="option-iconic option-cta">
					<span class="dashicons dashicons-unlock pos-left"></span>
					<input type="submit" name="ny-option-fields-enable" value="Enable" class="button option-button">	
				</div>
				<?php else: ?>
				<div class="option-iconic option-cta">
					<span class="dashicons dashicons-lock pos-left"></span>
					<input type="submit" name="ny-option-fields-disable" value="Disable" class="button option-button">	
				</div>
				<?php endif; ?>
				
				<div class="option-iconic option-cta">
					<span class="dashicons dashicons-welcome-add-page pos-left"></span>
					<input type="submit" name="ny-option-fields-save" value="Save Option Fields" class="button option-button">	
				</div>
				
			</div>			

		</form>
	</div>
</div>

<?php endif; ?>