var pos = jQuery.noConflict();
var $el_sidebar,
	$el_nav,
	$el_products,
	$el_products_feed,
	$el_products_edit,
	$el_cart,
	$el_cart_orders,
	$el_cart_subtotal,
	$el_cart_discounts,
	$el_cart_total,
	$el_cart_coupon,
	$el_active_product,	
	$el_active_option,
	$el_add_to_order,
	$el_notes,
	$el_body,
	$el_doc;

var $product_id 		= 0,
	$product 			= {},
	$product_json 		= [],
	$product_quantity 	= 1,
	$product_total 		= 0;

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
	$orders_current_nav	= 0;


NYPIZZA_POS = {





	build: function() {

		NYPIZZA_POS.el();
		NYPIZZA_POS.interval();
		NYPIZZA_POS.actions();
		NYPIZZA_POS.displayProducts(0);

	},





	el: function() {


		$el_doc 			= pos(document);
		$el_body 			= pos('body');
		$el_sidebar 		= pos('.pos-sidebar');
		$el_nav 			= pos('.pos-menu-nav a');
		$el_products 		= pos('.pos-products');
		$el_products_feed 	= pos('.pos-products-feed');
		$el_products_edit 	= pos('.pos-product-edit');
		$el_cart 			= pos('.pos-cart');
		$el_cart_orders 	= pos('.pos-cart-orders');
		$el_cart_subtotal	= pos('.pos-subtotal');
		$el_cart_discounts	= pos('.pos-discounts');
		$el_cart_total 		= pos('.pos-total');
		$el_cart_coupon 	= pos('.pos-coupon-code');
		$el_add_to_order 	= pos('.pos-add-to-order');
		$el_notes 			= pos('.pos-notes-content');
		

	},





	tooltip: function() {
		pos('.tooltip').tooltipster();
	},





	fluid: function() {

		var doc_width 		= $el_doc.width();
		var sidebar_width 	= $el_sidebar.width();
		var products_with 	= $el_products.width();
		var cart_width 		= $el_cart.width();


		/**
		 * Products
		 */
		$el_products.css({			
			width: ( doc_width - (sidebar_width + cart_width + 134) ) + 'px'
		});
		NYPIZZA_POS.setHeight( $el_products.find('.pos-product') );



		/**
		 * Remove Site Elements
		 */
		pos('header, footer, #secondary').remove();	

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





	actions: function() {
		

		$el_nav.on('click', function(e) {
			NYPIZZA_POS.control(e, NYPIZZA_POS.navigate);
		});


		$el_doc.on('click', '.pos-p-add', function(e) {
			$el_active_product = pos(e.target);
			NYPIZZA_POS.control(e, NYPIZZA_POS.addToCart);
		});


		$el_doc.on('click', '.pos-p-edit', function(e) {

			$orders_is_update = false;
			$el_active_product = pos(e.target);

			$el_add_to_order.text('Add To Order');
			$el_products_edit
				.find('#pos-quantity-field')
				.val( 1 );

			NYPIZZA_POS.control(e, NYPIZZA_POS.editProduct);

		});


		$el_doc.on('click', '.pos-order-edit', function(e) {

			$el_active_product 	= pos(e.target);			
			$orders_is_update 	= true;			
			$orders_index 		= parseInt($el_active_product.data('index'));
			$options_selected 	= [];

			$el_add_to_order.text('Update Order');

			NYPIZZA_POS.control(e, NYPIZZA_POS.editProduct);

		});


		$el_doc.on('click', '.pos-order-remove', function(e) {

			$el_active_product 	= pos(e.target);			
			NYPIZZA_POS.control(e, NYPIZZA_POS.removeOrder);

		});


		$el_doc.on('click', '.pos-option-add', function(e) {			
			
			$el_active_option = pos(e.target);

			NYPIZZA_POS.control(e, function(e) {
				NYPIZZA_POS.activateOptions(NYPIZZA_POS.addOption);
			});
		});


		$el_doc.on('click', '.pos-option-cta a', function(e) {
			NYPIZZA_POS.control(e, function(e) {

				var cta = pos(e.target).parent();
				cta
					.find('a')
					.removeClass('active');

				pos(e.target).addClass('active');

				NYPIZZA_POS.addOption(e);

			});
		});


		$el_doc.on('click', '.pos-add-to-order', function(e) {
			$product_quantity = parseInt(pos('input#pos-quantity-field').val());
			NYPIZZA_POS.control(e, NYPIZZA_POS.processProduct);
		});


		$el_doc.on('click', '.post-void', function(e) {
			NYPIZZA_POS.control(e, NYPIZZA_POS.void);
		});


		$el_doc.on('click', '.pos-close-edit', function(e) {
			NYPIZZA_POS.control(e, NYPIZZA_POS.home);
		});


		$el_doc.on('click', '.pos-c-arrow-up', function(e) {

			$orders_role  		= 'cart-nav';
			$orders_current_nav -= $orders_nav_limit;

			if ( $orders_current_nav < 0 )
				$orders_current_nav = 0;

			NYPIZZA_POS.updateCart();

		});


		$el_doc.on('click', '.pos-c-arrow-down', function(e) {

			$orders_role  		= 'cart-nav';
			$orders_current_nav += $orders_nav_limit;

			NYPIZZA_POS.updateCart();

		});


		$el_doc.on('click', '.post-notes', function(e) {
			NYPIZZA_POS.control(e, NYPIZZA_POS.addNotes);
		});


		$el_doc.on('click', '.pos-notes-close', function(e) {
			NYPIZZA_POS.control(e, NYPIZZA_POS.home);
		});


		$el_doc.on('click', '.pos-notes-add', function(e) {
			NYPIZZA_POS.control(e, function(e) {
				$orders_role  = 'save';
				$orders_notes = pos('#pos-notes-field').val();
				NYPIZZA_POS.store(NYPIZZA_POS.home);
			});
		});


		$el_doc.on('click', '.post-checkout', function(e) {
			NYPIZZA_POS.control(e, NYPIZZA_POS.checkout);
		});


		$el_doc.on('click', '.pos-coupon-add', function(e) {
			NYPIZZA_POS.control(e, NYPIZZA_POS.addCouponCode);
		});

	},





	resetOptions: function() {
		$el_products_edit
			.find('.active')
			.removeClass('active');
		$el_products_edit
			.find('.pos-option-whole')
			.addClass('active');
	},





	activateOptions: function( callback ) {		

		var button 		= $el_active_option;		
		var row 		= button.closest('.pos-option-item');
		var section 	= row.parent();
		var multiple 	= parseInt(row.data('multiple'));	


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
	},





	setSelectedOptions: function( callback ) {
		if ($orders_is_update) {
			pos('.pos-option-add.active').each(function(index, el) {

				var button 		= pos(el);		
				var row 		= button.closest('.pos-option-item');

				row.addClass('active');

				button
					.addClass('active')
					.text('Cancel');
				

			});
		}
		callback();
	},





	home: function(e) {

		$orders_index 		= -1;		
		$orders_is_update 	= false;
		$options_selected 	= [];

		$el_products_edit.hide();
		$el_notes.hide();

		$el_products
			.find('.pos-product')
			.show();

	},





	processProduct: function(e) {			
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

			NYPIZZA_POS.addCouponCode();
			
			callback();

		});
	},





	void: function(e) {
		var con = confirm('Void your orders?');
		if (con) {
			$orders_role 		= 'void';	
			$orders 			= [];
			$orders_discounts 	= 0;
			$orders_coupon 		= '';

			NYPIZZA_POS.updateCart();
		}
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





	addToCart: function(e) {	
		$product_quantity = 1;
		NYPIZZA_POS.productData( NYPIZZA_POS.processProduct );
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

			var options = [], options_total = 0;

			pos('.pos-option-add.active').each(function(index, el) {

				var data 	= (pos(el).data('group')).split(':');
				var group 	= parseInt( data[0] );
				var row 	= parseInt( data[1] );

				var cta 	= pos(el)
								.parent()								
								.find('.pos-option-cta .active');

				var has_variant = true;
				var variant 	= parseFloat( cta.data('variation') );

				if (isNaN( variant )) {
					variant 	= 0;
					has_variant = false;
				}
				

				var data_group 		= $product.options[group];
				var data_options 	= data_group.options[row];
				var name 			= data_group.name;

				options.push({
					group: group,
					row: row,
					name: name,
					variation: variant,
					options: data_options
				});	
				
				var amount = parseFloat( data_options.amount );
				if (isNaN(amount))
					amount = 0;

				options_total += (has_variant)? (variant * amount) : amount;
				
			});

			$product_total += options_total;
			$options_selected = options;							

			if ($product_total > 0) {
				pos('.pos-product-edit-price').text( NYPIZZA_POS.format( $product_total, true ) );		
			}

		});
	},




	editProduct: function(e) {
		NYPIZZA_POS.resetOptions();		
		NYPIZZA_POS.productData(function() {
			NYPIZZA_POS.displayOptions(function() {
				NYPIZZA_POS.setSelectedOptions(function() {

					$el_products
						.find('.pos-product')
						.hide();

					$el_products_edit
						.find('.thumbnail')
						.css({ 'background-image': 'url('+ $product.thumbnail +')' });

					$el_products_edit
						.find('.pos-product-edit-info .title')
						.text( $product.title );

					$el_products_edit
						.find('.description')
						.text( $product.description );

					$el_products_edit
						.find('.price')
						.text( NYPIZZA_POS.format( $product.price, true ) );

					if ( $orders_index >= 0 && typeof($orders[ $orders_index ]) !== 'undefined') {
						$el_products_edit
							.find('#pos-quantity-field')
							.val( $orders[ $orders_index ].quantity );
					}

					$el_products_edit.show();	

					NYPIZZA_POS.addOption(e);

				});				
			});		

		});
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





	productData: function( callback ) {
		
		$product_id = parseInt( $el_active_product.data('id') );
		$product_total = 0;

		pos.each($product_json, function(index, product) {
			
			if ( product.product_id === $product_id ) {

				$product = product;
				$product_total = $product.price;

				$options_selected.push({
					product_id: $product_id,
					options: NYPIZZA_POS.ordersOptions()
				});

				return false;

			}				
		});			

		callback();
	},





	updateCart: function() {

		var content = '', count = 1;
		$orders_subtotal = 0;

		pos.each($orders, function(index, order) {

			$orders_subtotal += parseFloat(order.total);
			
			if ( index >= $orders_current_nav && count <= $orders_nav_limit) {				

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
							<a href="#" class="pos-order-remove tooltip" title="Remove Order" data-id="`+ order.product_id +`" data-index="`+ index +`"></a>
							<a href="#" class="pos-order-edit tooltip" title="Edit Order" data-id="`+ order.product_id +`" data-index="`+ index +`"></a>
						</div>
					</div>
				`;


				count++;


			}
		});

		$orders_total = $orders_subtotal;

		$el_cart_orders.html( content );

		NYPIZZA_POS.tooltip();
		NYPIZZA_POS.summarize();

	},





	summarize: function() {
		NYPIZZA_POS.store(function() {
			
			var total = NYPIZZA_POS.format( ($orders_subtotal - $orders_discounts), true );			

			$el_cart_coupon.val( $orders_coupon );
			$el_cart_subtotal.text( NYPIZZA_POS.format( $orders_subtotal, true ) );
			$el_cart_discounts.text( NYPIZZA_POS.format( $orders_discounts, true ) );
			$el_cart_total.text( total );

			
			if ($orders_role === 'save')
				NYPIZZA_POS.home();			

		});
	},





	isOptionSelected: function( group, row ) {

		var selected = [];

		pos.each($options_selected, function( index, options ) {			
			pos.each(options.options, function( index_two, option ) {
				if (group === parseInt( option.group ) && row === parseInt( option.row )) {										
					selected = ['active', option.variation];
					return false;
				}
			});			
		});
	
		return selected;
	},





	displayOptions: function( callback ) {

		var content = '';
		
		pos.each($product.options, function(group, options) {


			content += `
				<div class="pos-product-edit-option">
					<div class="title font-bowlby-one">`+ options.name +`</div>
			`;

			pos.each(options.options, function(row, option) {


				var is_whole = 'active';
				var is_half  = 'active';
				var selected = NYPIZZA_POS.isOptionSelected( group, row );
				
				if (typeof(selected[0]) !== 'undefined' && parseInt(selected[1]) === 1)
					is_half = '';
				else
					is_whole = '';

				
				var variation = `
					<a href="#" class="pos-option-whole `+ is_whole +`" data-variation="1">Whole</a>
					<a href="#" class="pos-option-half `+ is_half +`" data-variation="0.5">Half</a>
				`;


				if (parseFloat(option.amount) <= 0 || parseInt( options.allow ) <= 0)
					variation = '';				


				content += `
					<div class="pos-option-item" data-multiple="`+ options.is_multiple +`" data-allow="`+ options.allow +`">
						<a href="#" class="pos-option-add font-baloo-bhai `+ selected[0] +`" data-group="`+ group + `:` + row +`">Add</a>
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
						<div class="pos-option-cta font-baloo-bhai">
							`+ variation +`
						</div>
					</div>				
				`;


			});

			content += `</div>`;			

		});

		$el_products_edit
			.find('.product-options')
			.html( content );

		callback();

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
					}

					content += `			
						<div class="pos-order-option">
							<div class="pos-order-option-name">
								`+ option.name +` <span class="pos-order-variation">`+ variation +`</span>
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





	addNotes: function(e) {

		$el_products_edit.hide();
		$el_products
			.find('.pos-product')
			.hide();

		$el_notes.show();
		$el_notes
			.find('textarea')
			.focus()
			.select();

	},




	checkout: function(e) {
		NYPIZZA_POS.server({
			action 		: 'pos_checkout',
			ID 			: $orders_id						
		}, function(results) {
			
			window.location = results.checkout;

		});
	},





	addCouponCode: function(e) {

		$orders_coupon = $el_cart_coupon.val();

		if ($orders_coupon !== '') {

			NYPIZZA_POS.server({
				action 		: 'pos_coupon',
				coupon 		: $orders_coupon,
				total 		: $orders_subtotal				
			}, function( results ) {

				if (results.valid) {
					
					var amount = parseFloat( results.discounts );
					$orders_discounts = (isNaN(amount))? 0  : amount;
					NYPIZZA_POS.saveCoupon();

				}

				$orders_role = 'update';
				NYPIZZA_POS.summarize();

			});


		}

	},





	saveCoupon: function() {
		if ($orders_id) {
			NYPIZZA_POS.server({
				action 		: 'pos_coupon_add',
				coupon 		: $orders_coupon,
				discounts 	: $orders_discounts,
				order_id	: $orders_id				
			}, function(results) {
				$orders_id = parseInt( results.order_id );
			});
		}
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
			$el_body.append('<div class="pos-loading"></div>');
		else
			$el_body.find('.pos-loading').remove();
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





	max: function(el, callback) {
		var maxHeight = Math.max.apply(null, el.map(function () {
		    return pos(this).height();
		}).get());
		callback( maxHeight );
	},





	setHeight: function(el, min) {
		NYPIZZA_POS.max( el, function( max ) {							
			max = (max < min)? min : max;
			el.height( max );
		});
	},





	interval: function() {
		setInterval(function() {
			NYPIZZA_POS.fluid();
		}, 500);
	},





	control: function(e, callback) {
		e.preventDefault();
		e.stopPropagation();
		callback(e);
	},





	isJSON: function( str ) {
		try { JSON.parse(str); } catch (e) { return false; }
    	return true;
	},





	displayProducts: function( category ) {
		NYPIZZA_POS.server({
			action 		: 'pos_categories',
			category 	: category			
		}, function(results) {		
			
			pos.each(results.scripts, function(index, product) {																
				if (NYPIZZA_POS.isJSON(product))
					$product_json.push( JSON.parse(product) );
				else
					console.log(product);
			});		

			
			$el_products_feed.html( results.products );
			

			NYPIZZA_POS.set();			
			NYPIZZA_POS.tooltip();

		});
	},





	navigate: function(e) {

		var menu 	 = pos(e.target);
		var category = menu.data('category');
		
		$el_nav
			.removeClass('active');
		menu.addClass('active');

		$el_products_edit
			.hide()
			.find('.product-options')
			.html( '' );

		NYPIZZA_POS.displayProducts( category );	

	}

};
NYPIZZA_POS.build();