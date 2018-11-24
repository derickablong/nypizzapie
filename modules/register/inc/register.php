<script type="text/javascript">
	var register_user_id = parseInt('<?php echo is_user_logged_in()? get_current_user_id() : 0 ?>');
	var register_site = '<?php echo get_bloginfo('siteurl') ?>/register-checkout/';
</script>
<div class="emp-app">
	<div class="emp-screen">
		<div class="emp-wrap">
			
			<div class="emp-header">
				<div class="emp-wrap">
					<div class="emp-back-screen emp-col">
						<span class="emp-back">Back</span>
					</div>					
					<div class="emp-order-type emp-col">					
						<span class="emp-order-label">NYPIZZA PIE</span>
					</div>					
				</div>
			</div>

			
			<!-- Order Type -->
			<div class="emp-content" data-display="ordertype">	
				<div class="emp-actions emp-order-new">
					<span class="emp-button emp-dine-in" data-action="ordertype" data-value="dine_in" data-sequence="1" data-color="#ee4037">
						<span>Dine In</span>
					</span>
					<span class="emp-button emp-pick-up" data-action="ordertype" data-value="pick_up" data-sequence="1" data-color="#e58e26">
						<span>Pick Up</span>
					</span>
					<span class="emp-button emp-delivery" data-action="ordertype" data-value="delivery" data-sequence="1" data-color="#FF6421">
						<span>Delivery</span>
					</span>
				</div>
			</div>
			<!-- end of Order Type -->


			<!-- Categories -->
			<div class="emp-content" data-display="category">	
				<div class="emp-actions emp-color-group emp-order-category"></div>	
			</div>
			<!-- end of Categories -->


			<!-- Products -->
			<div class="emp-content" data-display="products">	
				<div class="emp-actions emp-color-group emp-category-products"></div>	
			</div>
			<!-- end of Products -->


			<!-- Options -->
			<div class="emp-content emp-content-options" data-display="options">
				<span class="emp-button emp-add-to-cart" data-action="add-to-oven" data-role="add">
					<span>Add To Oven</span>
				</span>
				<div class="emp-actions emp-color-group emp-product-options"></div>
			</div>
			<!-- end of Options -->


			<!-- Variations -->
			<div class="emp-content emp-content-variations" data-display="variations">	
				<span class="emp-button emp-add-to-cart" data-action="add-to-oven" data-role="add">
					<span>Add To Oven</span>
				</span>
				<div class="emp-actions emp-color-group emp-product-variations"></div>
			</div>
			<!-- end of Variations -->


			<!-- Half and Half -->
			<div class="emp-half-n-half">	
				<div class="emp-wrap">	

					<div class="emp-half-cta">
						<span class="emp-half-cancel">
							<span>Done</span>
						</span>						
					</div>

					<span class="emp-half-button unselected" data-value="Half Left">
						<span>Half Left</span>
					</span>
					<span class="emp-half-button unselected" data-value="Whole">
						<span>Whole</span>
					</span>
					<span class="emp-half-button unselected" data-value="Half Right">
						<span>Half Right</span>
					</span>
				</div>
			</div>
			<!-- end of Half and Half -->


			<!-- Ask User -->
			<div class="emp-ask-user">
				<div class="emp-wrap">
					<div class="emp-ask-user-cta">						
						<span class="emp-option-unselect" data-action="remove-option">
							<span>Unselect?</span>
						</span>
						<span class="emp-option-update" data-action="change-option">
							<span>Make Changes?</span>
						</span>
					</div>
				</div>
			</div>
			<!-- end of Ask User -->


			<!-- Void Confirm -->
			<div class="emp-order-void">
				<div class="emp-wrap">
					<div class="emp-order-void-wrap">
						<span class="emp-confirm-sure">
							<span>Really void?</span>
						</span>
						<span class="emp-confirm-cancel">
							<span>Nope</span>
						</span>
					</div>
				</div>
			</div>
			<!-- end of Void Confirm -->


			<!-- Add Notes -->
			<div class="emp-content-notes">
				<div class="emp-wrap">
					<div class="emp-content-notes-wrap">
						<textarea class="emp-order-notes" placeholder="Enter notes"></textarea>
						<span class="emp-notes-add">
							<span>Add Notes</span>
						</span>
						<span class="emp-notes-cancel">
							<span>Cancel</span>
						</span>
					</div>
				</div>
			</div>
			<!-- end of Add Notes -->


			<!-- Cart -->
			<div class="emp-cart-summary">
				<div class="emp-cart-wrap">
					<div class="emp-title"></div>
					<div class="emp-cart-orders"></div>
				</div>
			</div>
			<!-- end of Cart -->			


			<!-- Quantity -->
			<div class="emp-quantity-options">
				<div class="emp-wrap">
					<div class="emp-quantity-options-box">
						<span class="emp-quantity-num">
							<span>1</span>
						</span>
						<span class="emp-quantity-num">
							<span>2</span>
						</span>
						<span class="emp-quantity-num">
							<span>3</span>
						</span>
						<span class="emp-quantity-num">
							<span>4</span>
						</span>
						<span class="emp-quantity-num">
							<span>5</span>
						</span>
						<span class="emp-quantity-num">
							<span>6</span>
						</span>
						<span class="emp-quantity-num">
							<span>7</span>
						</span>
						<span class="emp-quantity-num">
							<span>8</span>
						</span>
						<span class="emp-quantity-num">
							<span>9</span>
						</span>
						<span class="emp-quantity-num">
							<span>10</span>
						</span>
						<span class="emp-quantity-num">
							<span>11</span>
						</span>
						<span class="emp-quantity-num">
							<span>12</span>
						</span>
						<span class="emp-quantity-num">
							<span>13</span>
						</span>
						<span class="emp-quantity-num">
							<span>14</span>
						</span>
						<span class="emp-quantity-num">
							<span>15</span>
						</span>
						<span class="emp-quantity-num">
							<span>16</span>
						</span>
						<span class="emp-quantity-num">
							<span>17</span>
						</span>
						<span class="emp-quantity-num">
							<span>18</span>
						</span>
						<span class="emp-quantity-num">
							<span>19</span>
						</span>
						<span class="emp-quantity-num">
							<span>20</span>
						</span>
					</div>
				</div>
			</div>
			<!-- end of Quanity -->


			<div class="emp-footer">
				<div class="emp-wrap">
					<span class="emp-foot-box emp-user">
						<span class="emp-foot-box-wrap"></span>
					</span>
					<span class="emp-foot-box emp-cart-sales closed">
						<span class="emp-foot-box-wrap emp-cart-total">
							<span class="cart-whole">$100</span>
							<span class="cart-decimal">.00</span>
						</span>
					</span>
					<span class="emp-foot-box emp-cart-checkout">
						<span class="emp-foot-box-wrap">Send To Oven</span>
					</span>
					<span class="emp-foot-box emp-cart-notes">
						<span class="emp-foot-box-wrap">Notes</span>
					</span>
					<span class="emp-foot-box emp-cart-void">
						<span class="emp-foot-box-wrap">Void</span>
					</span>
				</div>
			</div>


			<!-- Login -->
			<div class="emp-login-screen">
				<div class="emp-wrap">
					<div class="emp-login-screen-wrap">

						<div class="emp-login-numbers">
							<span class="emp-login-display-cover"></span>
							<input type="password" class="emp-login-display" value="">

							<span class="emp-login-num" data-value="1">
								<span>1</span>
							</span>
							<span class="emp-login-num" data-value="2">
								<span>2</span>
							</span>
							<span class="emp-login-num" data-value="3">
								<span>3</span>
							</span>
							<span class="emp-login-num" data-value="4">
								<span>4</span>
							</span>
							<span class="emp-login-num" data-value="5">
								<span>5</span>
							</span>
							<span class="emp-login-num" data-value="6">
								<span>6</span>
							</span>
							<span class="emp-login-num" data-value="7">
								<span>7</span>
							</span>
							<span class="emp-login-num" data-value="8">
								<span>8</span>
							</span>
							<span class="emp-login-num" data-value="9">
								<span>9</span>
							</span>
							<span class="emp-login-num emp-login-clr" data-value="clr">
								<span>C</span>
							</span>
							<span class="emp-login-num" data-value="0">
								<span>0</span>
							</span>
							<span class="emp-login-num emp-login-del" data-value="del">
								<span></span>
							</span>							
						</div>

						<div class="emp-login-cta">
							<span class="emp-login-cta-btn emp-login-singin">
								<span>Sign In</span>
							</span>
							<span class="emp-login-cta-btn emp-login-checkin">
								<span>Checkin</span>
							</span>
							<span class="emp-login-cta-btn emp-login-checkout">
								<span>Checkout</span>
							</span>
						</div>

					</div>
				</div>
			</div>
			<!-- end of Login -->


			<!-- User menu -->
			<div class="emp-user-menu">
				<div class="emp-wrap">
					<span class="emp-user-menu-close">
						<span>x</span>
					</span>
					<div class="emp-user-info">
						<span class="avatar"></span>
						<span class="username">Derick Ablong</span>
					</div>
					<div class="emp-user-menu-wrap">
						<span class="emp-user-btn emp-user-checkout">
							<span>Checkout</span>
						</span>
						<span class="emp-user-btn emp-user-logout">
							<span>Logout</span>
						</span>
						<span class="emp-user-btn emp-user-transactions">
							<span>Transactions</span>
						</span>
					</div>
				</div>
			</div>
			<!-- end of User menu -->


			<!-- Send To Oven -->
			<div class="emp-server-request">
				<div class="emp-wrap">
					<div class="emp-server-message">
						<span class="emp-server-icon"></span>
						<span class="emp-server-text"></span>
					</div>
				</div>
			</div>
			<!-- end of Send To Oven -->


			<!-- Transactions -->
			<div class="emp-transactions">
				<div class="emp-wrap">
					<span class="emp-transactions-close">
						<span>x</span>
					</span>
					<div class="emp-transactions-wrap"></div>
				</div>
			</div>
			<!-- end of Transactions -->

			
		</div>
	</div>
</div>





<div class="emp-checkout-window">
	<div class="emp-chechout-header">
		<span class="emp-checkout-title">Checkout</span>
		<div class="emp-checkout-cta">			
			<span class="emp-checkout-close">
				<span>Back</span>
			</span>
		</div>
	</div>
	
	<iframe src="" class="emp-checkout-frame"></iframe>	
</div>