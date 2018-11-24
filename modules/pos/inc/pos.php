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

<div class="pos-app">
	<div class="pos-sidebar-ui">
		<a href="/" class="pos-logo">
			<img src="<?php echo NYPIZZA_URI ?>/media/img/logov2.png" alt="" title="">
		</a>
		<?php pizza_pos_store_categories(); ?>
	</div>
	<div class="pos-content-ui"></div>
</div>