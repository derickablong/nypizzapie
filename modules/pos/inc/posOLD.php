<?php
$orders = pizza_pos_get_orders();
$has_orders = count($orders) > 0;
?>
<script>
	var pos_user_order_id 		= parseInt('<?php echo ($has_orders)? $orders[0]->id : '0' ?>');
	var pos_user_orders 		= JSON.parse('<?php echo ($has_orders)? stripslashes($orders[0]->orders) : '[]' ?>');
	var pos_user_orders_notes 	= '<?php echo $orders[0]->notes ?>';
	var pos_user_discounts 		= parseFloat('<?php echo $orders[0]->discounts ?>');
	var pos_user_coupon 		= '<?php echo $orders[0]->coupon ?>';
</script>



<div id="pos-container">
		
	<!-- SIDEBAR -->
	<div class="pos-sidebar">
		<a href="/" class="pos-logo"></a>
		
		<div class="pos-menu">
			<div class="pos-menu-title font-bowlby-one">
				Menu
			</div>
			<?php pizza_pos_store_categories(); ?>
		</div>

	</div>
	<!-- END OF SIDEBAR -->

	

	<!-- PRODUCTS -->
	<div class="pos-products">
		
		<div class="pos-products-feed"></div>

		<?php
		require_once( NYPIZZA_MODULES . "/{$nypizza->pos->folder}/inc/product.php" );
		require_once( NYPIZZA_MODULES . "/{$nypizza->pos->folder}/inc/notes.php" );
		?>
	
	</div>
	<!-- END OF PRODUCTS -->



	<!-- CART -->
	<div class="pos-cart">
		<div class="pos-cart-wrap">
			<div class="pos-cart-header">
				<div class="pos-cart-title font-alfa-slab">
					ORDER DETAILS
				</div>
				<div class="pos-cart-header-cta">
					<a href="#" class="pos-c-arrow-up tooltip" title="Previous Orders"></a>
					<a href="#" class="pos-c-arrow-down  tooltip" title="Next Orders"></a>
				</div>
			</div>
			<div class="pos-cart-orders"></div>
			<div class="pos-cart-coupon">
				<input type="text" class="pos-coupon-code font-alfa-slab" placeholder="Enter Coupon Code">
				<a href="#" class="pos-coupon-add"></a>
			</div>
			<div class="pos-cart-summary font-alfa-slab">
				<div class="pos-summary">
					<div class="pos-summary-label">Subtotal</div>
					<div class="pos-summary-amount pos-subtotal">$0.00</div>
				</div>
				<div class="pos-summary">
					<div class="pos-summary-label">Discounts</div>
					<div class="pos-summary-amount pos-discounts">$0.00</div>
				</div>
				<div class="pos-summary">
					<div class="pos-summary-label">Total Order</div>
					<div class="pos-summary-amount pos-total">$0.00</div>
				</div>
			</div>
			<div class="post-cart-cta">
				<a href="#" class="post-void tooltip" title="Void Orders"></a>
				<a href="#" class="post-notes tooltip" title="Add Notes"></a>
				<a href="#" class="post-shipping tooltip" title="View Shipping"></a>
				<a href="#" class="post-checkout tooltip" title="Checkout Orders"></a>
			</div>
		</div>
	</div>
	<!-- END OF CART -->


</div>


<div id="pos-bg"></div>