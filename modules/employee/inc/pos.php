<?php
$current_user = wp_get_current_user();
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


<div class="pos-logout">
	<div class="pos-logout-wrap">
		<div class="pos-logout-con">
			<h2>
				<span>YOUR ARE LOGIN AS:</span>
				<?php echo $current_user->user_firstname . ' ' . $current_user->user_lastname ?>
			</h2>
			<a href="#" class="user-transactions">Transactions</a>
			<a href="?logout=1" class="logout-user">Logout</a>
			<a href="#" class="close-logout">Back</a>
		</div>
	</div>
</div>


<div class="pos-transactions">
	<div class="pos-transactions-wrap">
		<div class="pos-transactions-con">
			<div class="pos-transactions-header">
				<a href="#" class="pos-back">Back To Store</a>
				<h2>Daily Transactions<span><?php echo date('l') ?> (<?php echo date('M d, Y') ?>)</span></h2>
			</div>
			<div class="pos-transactions-report"></div>
		</div>
	</div>
</div>


<div class="pos-app">
	<div class="pos-sidebar-ui">
		<div class="pos-user-con">
			<span class="pos-avatar" style="background-image:url(<?php echo get_avatar_url($current_user->user_email) ?>)"></span>
			<span class="pos-username"><?php echo $current_user->user_firstname . ' ' . $current_user->user_lastname ?></span>
		</div>
		<?php pizza_pos_store_categories(); ?>
	</div>
	<div class="pos-content-ui"></div>
</div>