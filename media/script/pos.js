var pos 			= jQuery.noConflict();

var $category_id 	= 0,
	$category_name 	= '',
	$product_id 	= 0,
	$product_total 	= 0,
	$product 		= {},
	$product_json 	= [];

var $options_selected 	= [];

var $orders 			= [],
	$orders_id 			= 0,
	$orders_index 		= -1,
	$orders_options 	= [],
	$orders_coupon 		= '',
	$orders_subtotal 	= 0,
	$orders_discounts 	= 0,
	$orders_total 		= 0,
	$orders_role 		= 'save',
	$orders_notes 		= '',
	$orders_is_update 	= false,
	$orders_nav_limit 	= 2,
	$orders_current_nav	= 0,
	$orders_total_items = 0;

var $active_group;


NYPIZZA_POS = {





	build: function() {
		NYPIZZA_POS.set();
		NYPIZZA_POS.products();
		NYPIZZA_POS.actions();
	},





	set: function() {
		
		$orders_id 	 		= window.pos_user_order_id;
		$orders 	 		= window.pos_user_orders;
		$orders_notes 		= window.pos_user_orders_notes;
		$orders_discounts 	= isNaN(window.pos_user_discounts)? 0 : window.pos_user_discounts;
		$orders_coupon 		= window.pos_user_coupon;
		$orders_role 		= 'update';

		NYPIZZA_POS.updateCart();

	},





	control: function(e, callback) {
		e.preventDefault();
		e.stopPropagation();
		callback(e);
	},





	category: function() {
		$category_id = pos('.pos-sidebar-ui li a.active').data('category');		
		$category_name = pos('.pos-sidebar-ui li a.active').data('name');
	},





	products: function() {
		NYPIZZA_POS.category();
		NYPIZZA_POS.server({
			action: 'pos_categories',
			category: $category_id,
			name: $category_name
		}, function(results) {

			pos.each(results.scripts, function(index, product) {																
				if (NYPIZZA_POS.isJSON(product))
					$product_json.push( JSON.parse(product) );													
			});	

			pos('.pos-content-ui').html(results.products);
			
			NYPIZZA_POS.displayCart();
			NYPIZZA_POS.productOptions();

		});
	},





	productOptions: function() {
		pos('.pos-product').each(function() {
			
			var pID = pos(this).data('id');
			var element = pos(this);

			NYPIZZA_POS.productData(pID, function() {
				NYPIZZA_POS.displayOptions( element );
				NYPIZZA_POS.displayLogic();
				NYPIZZA_POS.displayCart();
			});
		});
	},





	displayLogic: function() {
		pos('.pos-option-add').each(function() {

			var option = pos(this);
			var lid = option.data('gid');
			var name = option.data('name');
			var doption = pos('.pos-product-edit-option[data-logic="'+ lid + ':' + name +'"]');

			if (option.hasClass('active'))
				doption.show();
			else
				doption.hide();

		});
	},





	displayCart: function() {

		var total = NYPIZZA_POS.format( ($orders_subtotal - $orders_discounts), true );

		var cart_display = `
		<div class="pos-cart-display">
			<div class="cart-col">
				<span class="cart-total">`+ total +`</span>
			</div>
			<div class="cart-col">
				<span class="cart-items">`+ $orders_total_items +` items</span>
			</div>
		</div>
		`;

		if (pos('.pos-content-ui').find('.pos-cart-display').length <= 0)
			pos('.pos-content-ui').append(cart_display);
	},





	productData: function( pID, callback ) {		
		
		$product_total = 0;
		
		pos.each($product_json, function(index, product) {
			
			if ( parseInt(product.product_id) === pID ) {

				$product = product;
				$product_total = $product.price;

				$options_selected.push({
					product_id: pID,
					options: NYPIZZA_POS.ordersOptions()
				});

				return true;

			}				
		});			

		callback();
	},










	ordersOptions: function() {

		var options = [];

		pos.each($orders, function(index, order) {				
			if (index === $orders_index) {
				options = order.options;
				return false;
			}
		});		
		
		return options;

	},





	isOptionSelected: function( group, row ) {

		var selected = ['', ''];

		pos.each($options_selected, function( index, options ) {			
			pos.each(options.options, function( index_two, option ) {
				if (group === parseInt( option.group ) && row === parseInt( option.row )) {										
					selected = ['active', option.type];
					return false;
				}
			});			
		});
	
		return selected;
	},





	displayOptions: function( element ) {

		var content = '';
		
		pos.each($product.options, function(group, options) {

			var style = '', dlogic = '';

			if (1 == parseInt(options.is_logic)) {		
				if (options.logic !== '') {
					var logic = options.logic.split('::');
						dlogic = logic[1] + ':' + logic[2];

					if ('show' == logic[0])
						style = 'style="display: none"';
				}			
			}


			if (options.name !== null) {
				
				content += `
					<div class="pos-product-edit-option"  `+ style +` data-logic="`+ dlogic +`">
						<div class="title font-bowlby-one"><span class="pos-product-option-cta"></span><span>`+ (options.name).replace(/\\/g, '') +`</span><em></em></div>
						<div class="pos-options-group">
				`;

				pos.each(options.options, function(row, option) {


					var whole = '';
					var half_left  = '';
					var half_right  = '';
					var selected = NYPIZZA_POS.isOptionSelected( group, row );
					
					if (typeof(selected[0]) !== 'undefined') {					
						if (selected[1] == 'whole' || selected[1] == '')
							whole = 'active';
						if (selected[1] == 'half-left')
							half_left = 'active';
						if (selected[1] == 'half-right')
							half_right = 'active';
					} else {
						whole = 'active';
					}

					
					var variation = `
						<a href="#" class="pos-option-half-left `+ half_left +`" data-type="half-left" data-variation="0.5">Left</a>
						<a href="#" class="pos-option-whole `+ whole +`" data-type="whole" data-variation="1">Whole</a>
						<a href="#" class="pos-option-half-right `+ half_right +`" data-type="half-right" data-variation="0.5">Right</a>
					`;


					if (parseFloat(option.amount) <= 0 || parseInt( options.allow ) <= 0)
						variation = '';				

					var quantity = (typeof(option.quantity) === 'undefined')? 1 : parseInt(option.quantity);


					if (selected[0] === '') {
						if (parseInt(options.preselected))
							selected[0] = 'active';
					}


					content += `
						<div class="pos-option-item `+ selected[0] +`" data-multiple="`+ options.is_multiple +`" data-allow="`+ options.allow +`">
							<a href="#" class="pos-option-add font-baloo-bhai `+ selected[0] +`" data-id="`+ $product.product_id +`" data-group="`+ group + `:` + row +`" data-gid="`+ options.ID + `" data-name="`+ option.name +`">Add</a>
							<div class="pos-option-name font-baloo-bhai">
								<span class="option-name">
									`+ option.name +`
									<em class="option-description">
										`+ option.description +`
									</em>
								</span>
							</div>
							<div class="pos-option-price font-baloo-bhai">
								<span>`+ NYPIZZA_POS.format( option.amount, false ) +`</span>
							</div>						
							<div class="pos-option-selections-popup">
								<div class="pos-option-selections-popup-wrap">`;



									if (parseInt( options.is_quantity )) {
										content += `
										<div class="pos-option-qty-wrap">
											<span>QTY</span>
											<input type="text" class="pos-option-qty" value="1">
										</div>
										`;
									}

									if (variation !== '') {

										content += `<div class="pos-option-family">
											<div class="pos-option-cta font-baloo-bhai">							
												`+ variation +`
											</div>							
										</div>`;

									}
					content += `</div></div></div>`;

				});



				var actions = `<div class="pos-option-actions">							
								<a href="#" class="pos-send-to-oven" data-id="`+ $product.product_id +`">Send To My Oven</a>
								<a href="#" class="pos-cancel">Back</a>
						   </div>`;

				content += actions;

				content += `</div></div>`;			


			} else {
				content += '';
			}

		});


		var content_wrap = '<div class="pos-option-toogle-content">';			
			content_wrap += content;			

		if (content !== '') {
			element
				.find('.pos-product-options')
				.html( content_wrap );
		}


		pos('.pos-option-add.active').each(function() {

			var button = pos(this);
				button.removeClass('active');

			NYPIZZA_POS.activateOptions(
				button,
				NYPIZZA_POS.addOption
			);	
		});	

	},





	format: function( price, is_amount ) {
		var price = parseFloat( price );
		var amount = price.toFixed(2);
		if ( price > 0 ) {
			return '$' + amount;
		} else {
			if (is_amount)
				return '$0.00';
			return 'FREE';
		}
	},





	isJSON: function( str ) {
		try { JSON.parse(str); } catch (e) { return false; }
    	return true;
	},





	server: function( data, callback ) {
		NYPIZZA_POS.wait(true);
		pos.ajax({
			url: ajax_object.ajax_url,
			type: 'POST',			
			data: data,
			dataType: 'JSON'
		})
		.done(function(results) {							
			
			callback( results );
		
			NYPIZZA_POS.wait(false);

		});
	},





	wait: function( load ) {
		if (load)
			pos('.pos-content-ui, .pos-transactions-report').html('<div class="pos-loading"></div>');
		else
			pos('.pos-content-ui, .pos-transactions-report').find('.pos-loading').remove();
	},





	activateOptions: function( active_option, callback ) {		

		var button 		= active_option;		
		var row 		= button.closest('.pos-option-item');
		var section 	= row.parent();
		var multiple 	= parseInt(row.data('multiple'));
		var quantity 	= row.find('.pos-option-qty-wrap');	


		NYPIZZA_POS.productData(parseInt(button.data('id')), function() {

			if (button.hasClass('active')) {
				row.removeClass('active');
				button
					.removeClass('active')
					.text('Add');
				
			} else {
				if (multiple <= 0) {				
					section
						.find('.pos-option-item')
						.removeClass('active');

					section
						.find('.pos-option-add')
						.removeClass('active')
						.text('Add');
				}

				row.addClass('active');
				button
					.addClass('active')
					.text('Cancel');
				
			}


			callback();

		});

		
	},





	clearProductOptions: function( callback ) {	
		
		$product_total = $product.price;	

		pos.each($options_selected, function(index, option) {
			if (option.product_id === $product_id)
				$options_selected[index].options = [];
		});

		callback();

	},





	addOption: function(e) {				
		NYPIZZA_POS.clearProductOptions(function() {

			var options = [], added_options = [], options_total = 0;

			pos('.pos-option-add.active').each(function(index, el) {


				var par_price = pos(el).closest('.pos-product-options').parent();
				$active_group = par_price.find('.pos-product-price');


				var data 	= (pos(el).data('group')).split(':');
				var group 	= parseInt( data[0] );
				var row 	= parseInt( data[1] );

				var cta 	= pos(el)
								.parent()								
								.find('.pos-option-cta .active');

				var has_variant = true;
				var variant 	= parseFloat( cta.data('variation') );
				var type 		= cta.data('type');
					type 		= (typeof(type) == 'undefined')? '' : type;
				
				var quantity = 1;
				if (pos(el).parent().find('.pos-option-qty').length > 0)
					quantity = parseInt( pos(el).parent().find('.pos-option-qty').val() );


				if (isNaN( variant )) {
					variant 	= 0;
					has_variant = false;
				}				


				var data_group 		= $product.options[group];

				if (typeof(data_group) !== 'undefined' && typeof(data_group.options[row]) !== 'undefined') {

					var data_options 	= data_group.options[row];
					var name 			= data_group.name;


					var pgroup = $product.product_id + ':' + data_options.name;
				
					if (pos.inArray(pgroup, added_options) === -1) {

						options.push({
							group: group,
							row: row,
							name: name,
							variation: variant,
							type: type,
							options: data_options,
							quantity: quantity
						});	
						
						var amount = parseFloat( data_options.amount );
						if (isNaN(amount))
							amount = 0;

						options_total += ((has_variant)? (variant * amount) : amount) * quantity;

						added_options.push( pgroup );

					}			


				}	
				
			});
			
			$product_total += options_total;
			$options_selected = options;							

			
			if ( typeof($active_group) !== 'undefined' && $product_total > 0) {
				$active_group.text( NYPIZZA_POS.format( $product_total, true ) );		
			}

		});
	},





	processProduct: function(e) {

		NYPIZZA_POS.addOption();

		var order = {
			product_id	: $product.product_id,
			quantity 	: $product_quantity,
			title 		: $product.title,
			notes 		: $orders_notes,
			price 		: $product.price,
			total 		: ($product_total * $product_quantity),
			options 	: $options_selected,
			key 		: '',
			status 		: 'processing'
		};

		

		if ($orders == null)
			$orders = [];

		if ( $orders_is_update )
			$orders[ $orders_index ] = order;
		else
			$orders.push( order );		
		
		
		$orders_role = 'save';
		NYPIZZA_POS.updateCart();
		

	},





	updateCart: function() {

		var content = '', count = 1;
		$orders_subtotal = 0;
		$orders_total_items = 0;

		pos.each($orders, function(index, order) {

			$orders_subtotal += parseFloat(order.total);			
			$orders_total_items = index + 1;					

			content += `
				<div class="pos-cart-order">
					<div class="pos-order font-baloo-bhai">
						<div class="pos-order-name">
							`+ order.quantity +` x `+ order.title +`
						</div>
						<div class="pos-order-price">
							`+ NYPIZZA_POS.format( order.total, false ) +`
						</div>
					</div>
					<div class="pos-order-options font-baloo-bhai">
					`+ NYPIZZA_POS.getCartOptions( order.options ) +`
					</div>
					<div class="pos-order-cta">
						<a href="#" class="pos-order-remove tooltip" title="Remove Order" data-id="`+ order.product_id +`" data-index="`+ index +`">Remove</a>							
					</div>
				</div>
			`;
			
		});

		$orders_total = $orders_subtotal;

		pos('.pos-cart-products').html(content);

		//NYPIZZA_POS.tooltip();
		NYPIZZA_POS.summarize();

	},





	groupOptions: function( options ) {

		var option_group = [];

		pos.each(options, function(row, option) {
			if (typeof(option.name) !== 'undefined') {
				var data = option.options;
				var name = (option.name).toString();

				data.variation = option.variation;	

				if (typeof(option_group[ name ]) === 'undefined')
					option_group[ name ] = [];
				option_group[ name ].push( data );
			}
		});

		return option_group;

	},





	getCartOptions: function( data_options ) {

		var content = '';		
		
		data_options = NYPIZZA_POS.groupOptions( data_options );
		
		for ( var group in data_options ) {		


			content += `			
					<div class="pos-order-option option-group">
						<div class="pos-order-option-name">
							<strong>`+ group +`</strong>
						</div>
						<div class="pos-order-option-price"></div>
					</div>					
				`;


			var options = data_options[group];			

			pos.each(options, function(row, option) {

				if (typeof(option) !== 'undefined' && typeof(option.name) !== 'undefined') {

					var amount 		= option.amount;
					var variation 	= '';					
					var variant 	= parseFloat(option.variation);

					if (variant === 0.5) {
						variation 	= 'Half';
						amount = (amount * 0.5);
					}
					else if (variant === 1) {
						variation 	= 'Whole';
						amount = (amount * 1);
					} else {
						variation = null;
					}

					var variation_name = (variation !== null)? `<span class="pos-order-variation">`+ variation +`</span>` : '';


					content += `			
						<div class="pos-order-option">
							<div class="pos-order-option-name">
								`+ option.name +`
								`+ variation_name +`								
							</div>
							<div class="pos-order-option-price">
								`+ NYPIZZA_POS.format( amount, false ) +`
							</div>
						</div>					
					`;

				}



			});


		}

		return content;

	},





	summarize: function() {
		NYPIZZA_POS.store(function() {
			
			var total = NYPIZZA_POS.format( ($orders_subtotal - $orders_discounts), true );			

			// $el_cart_coupon.val( $orders_coupon );
			// $el_cart_subtotal.text( NYPIZZA_POS.format( $orders_subtotal, true ) );
			// $el_cart_discounts.text( NYPIZZA_POS.format( $orders_discounts, true ) );
			// $el_cart_total.text( total );
			
			NYPIZZA_POS.products();
			pos(document).find('.cart-total').text( total );			

		});
	},





	removeOrder: function(e) {
		var con = confirm('Remove this order?');
		if (con) {

			$options_selected 	= [];
			$orders_is_update 	= false;			
			$orders_index 		= parseInt($el_active_product.data('index'));
			$orders_role 		= 'save';

			$orders.splice( $orders_index, 1 );

			NYPIZZA_POS.updateCart();

		}
	},





	store: function( callback ) {
		
		if ($orders_role === 'update' || $orders_role === 'cart-nav') {
			return callback();
		}

		NYPIZZA_POS.server({
			action 		: 'pos_store',
			role 		: $orders_role,
			order_id 	: $orders_id,
			orders 		: $orders,			
			subtotal 	: $orders_subtotal,
			total 		: $orders_total,
			discounts 	: $orders_discounts,
			coupon 		: $orders_coupon,
			notes 		: $orders_notes			
		}, function( results ) {
			
			$orders_id 		= parseInt(results.order_id);
			$orders 		= results.orders;			
			$orders_role 	= 'save';

			//NYPIZZA_POS.addCouponCode();
			
			callback();

		});
	},





	addToCart: function(pID) {	
		$product_quantity = 1;
		NYPIZZA_POS.productData(pID, NYPIZZA_POS.processProduct );
	},





	transactions: function(e) {
		pos('.pos-transactions').css('display', 'table');
		NYPIZZA_POS.server({
			action: 'pos_transactions'
		}, function( results ) {			
			pos('.pos-transactions-report').html( results.transactions );			
		});
	},





	actions: function() {

		pos(document).on('click', '.pos-sidebar-ui li a', function(e) {
			NYPIZZA_POS.control(e, function() {
				pos('.pos-sidebar-ui li a').removeClass('active');
				pos(e.target).addClass('active');
				NYPIZZA_POS.products();
			});
		});

		pos(document).on('click', '.pos-option-add', function(e) {			
			NYPIZZA_POS.control(e, function(e) {
				NYPIZZA_POS.activateOptions(
					pos(e.target),
					NYPIZZA_POS.addOption
				);
				NYPIZZA_POS.displayLogic();
			});
		});


		pos(document).on('click', '.pos-option-cta a', function(e) {
			NYPIZZA_POS.control(e, function(e) {

				var cta = pos(e.target).parent();
				cta
					.find('a')
					.removeClass('active');

				pos(e.target).addClass('active');

				NYPIZZA_POS.addOption(e);

			});
		});


		pos(document).on('click', '.pos-send-to-oven', function(e) {
			NYPIZZA_POS.control(e, function(e) {
				NYPIZZA_POS.addToCart( pos(e.target).data('id') );
			});
		});


		pos(document).on('click', '.pos-product-option-cta', function(e) {
			NYPIZZA_POS.control(e, function(e) {

				var container = pos('.pos-content-ui');
				var cta = pos(e.target).parent();
				var el = cta.parent();

				var main = cta.parent().parent().parent().parent();
				$active_group = main.find('.pos-product-price');	

				if (cta.hasClass('active')) cta.removeClass('active');
				else cta.addClass('active');
				
				el.find('.pos-options-group').toggle();

				container.animate({
					scrollTop: cta.offset().top - container.offset().top + container.scrollTop()			        
			    }, 2000);
			});
		});


		pos(document).on('keyup', '.pos-option-qty', NYPIZZA_POS.addOption);

		pos(document).on('click', '.pos-cancel', function(e) {
			NYPIZZA_POS.control(e, function(e) {
				pos('.pos-options-group').hide();
				pos('.title').removeClass('active');
			});
		});


		pos(document).on('click', '.close-logout', function(e) {
			NYPIZZA_POS.control(e, function(e) {
				pos('.pos-logout').hide();
			});
		});
		pos(document).on('click', '.pos-user-con', function(e) {
			NYPIZZA_POS.control(e, function(e) {
				pos('.pos-logout').css('display', 'table');
			});
		});


		pos(document).on('click', '.pos-cart-display', function(e) {
			NYPIZZA_POS.control(e, function(e) {
				pos('.pos-cart-orders').css('display', 'table');
			});
		});
		pos(document).on('click', '.pos-cart-close', function(e) {
			NYPIZZA_POS.control(e, function(e) {
				pos('.pos-cart-orders').hide();
			});
		});


		pos(document).on('click', '.pos-order-remove', function(e) {
			$el_active_product 	= pos(e.target);			
			NYPIZZA_POS.control(e, NYPIZZA_POS.removeOrder);
		});


		pos(document).on('click', '.user-transactions', function(e) {			
			NYPIZZA_POS.control(e, NYPIZZA_POS.transactions);
		});
		pos(document).on('click', '.pos-back', function(e) {			
			NYPIZZA_POS.control(e, function(e) {
				pos('.pos-transactions, .pos-logout').hide();
				NYPIZZA_POS.products();
			});
		});
		

	}





};
NYPIZZA_POS.build();