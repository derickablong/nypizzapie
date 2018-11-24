"use strict";

var em = jQuery.noConflict();
var user_id = (register_user_id)? register_user_id : 0;
var login_input_pin = '';
var header_title = '';
var button;
var color = '';
var category_pid = [];

var sequence = 0;
var sequence_display = [
	'ordertype',
	'category',
	'products',
	'options',
	'variations'
];
var sequence_title = ['NYPIZZA PIE'];


var orders = {
	'dine_in': [],
	'pick_up': [],
	'delivery': []
};
var order_options = [];
var order_id = 0;
var ordertype = '';
var order_index = 0;
var order_total = 0;
var order_prev_total = 0;
var order_update = false;
var order_notes = '';

var product_index = -1;
var product;
var product_selected;
var product_options;
var option_variations;
var option_index = -1;
var variation_index = -1;
var add_to_oven = false;
var is_change = false;
var active_e;
var active_option;

var update_index = -1;
var update_button;
var update_order;


var REGISTER = {

	build: function() {		
		REGISTER.checkUser(function() {
			REGISTER.userInfo();
			REGISTER.dataCategories();
			REGISTER.dataProducts();			
			REGISTER.display();
			REGISTER.cartDisplay();
		});
		REGISTER.actions();
	},


	control: function(e, callback) {
		e.preventDefault();
		e.stopPropagation();
		callback(e);

		return false;
	},


	format: function( price, is_amount ) {
		var price = parseFloat( price );
		var amount = price.toFixed(2);
		if ( price > 0 ) {
			return '$' + amount;
		} else {
			if (is_amount)
				return '$0.00';
			return '';
		}
	},


	copy: function(obj) {

		var copy;
	    
	    if (null == obj || "object" != typeof obj) return obj;

	    
	    if (obj instanceof Date) {
	        copy = new Date();
	        copy.setTime(obj.getTime());
	        return copy;
	    }

	    
	    if (obj instanceof Array) {
	        copy = [];
	        for (var i = 0, len = obj.length; i < len; i++) {
	            copy[i] = REGISTER.copy(obj[i]);
	        }
	        return copy;
	    }

	    
	    if (obj instanceof Object) {
	        copy = {};
	        for (var attr in obj) {
	            if (obj.hasOwnProperty(attr)) copy[attr] = REGISTER.copy(obj[attr]);
	        }
	        return copy;
	    }
	    
	},


	addToOven: function() {

		add_to_oven = true;
		
		orders[ordertype].push( product_selected );
		order_index = orders[ordertype].length - 1;		

		REGISTER.updateTotal();		
		REGISTER.checkOven();

	},


	updateTotal: function() {

		order_total = 0;

		REGISTER.getTotal( orders[ordertype] );		
		REGISTER.cartDisplay();

	},


	getTotal: function( emp_orders ) {
		em.each(emp_orders, function(index, order) {
			if (isNaN(parseFloat(order.price))) return true;

			var total = parseFloat( order.price );
			var options = order.options;

			if (typeof(options) !== 'undefined') {

				em.each(options, function(opt_index, opt) {
					em.each(opt[0].options, function( oin_index, oin_opt ) {

						if (oin_opt[0].selected) {

							var type = (oin_opt[0].type === 'Whole')? 1 : 0.5;
							var amount = parseFloat( oin_opt[0].amount ) * type;						
							total += parseFloat( amount );
						}

					});				
				});

			}

			orders[ordertype][index]['total'] = total;

			order_total += total * parseInt( order.quantity );

		});
	},


	cartDisplay: function() {

		var total = (REGISTER.format( order_total, true )).split('.');
		em('.cart-whole').text( total[0] );
		em('.cart-decimal').text( '.' + total[1] );

		if (order_prev_total !== order_total) {

			em('.emp-cart-sales').animate({
				'background-color': '#000'
			}, 500);
			em('.emp-cart-sales').animate({
				'background-color': '#ee4037'
			}, 500);

		}

		order_prev_total = order_total;
		
	},


	checkOven: function() {
		if (add_to_oven) {
			em('.emp-add-to-cart')
				.data('role', 'update')
				.addClass('added')
				.find('span')
					.text('Update Oven');
		}
		else {
			em('.emp-add-to-cart')
				.data('role', 'add')
				.removeClass('added')
				.find('span')
					.text('Add To Oven');
		}
	},


	title: function() {
		em('.emp-order-label').html( sequence_title[sequence] );
	},


	dataCategories: function() {	
		var str = '';
		em.each(window.data_categories, function(index, cat) {
			var category = cat[0];
			str += `
			<span class="emp-menu emp-menu-category emp-button" data-cat="`+ category.ID +`" data-action="category" data-pid="`+ (category.PID.join('|')) +`" data-sequence="2">
				<span>`+ category.name +`</span>
			</span>
			`;
		});
		em('.emp-order-category').html( str );
	},


	dataProducts: function() {		
		var str = '';
		em.each(window.data_products, function(index, prd) {			
			str += `
			<span class="emp-menu emp-menu-product emp-button" data-pid="`+ prd[0].ID +`" data-action="options" data-index="`+ index +`" data-sequence="3">
				<span>`+ prd[0].name +`<em class="prd-price">`+ REGISTER.format(prd[0].price, true) +`</em></span>				
			</span>		
			`;
		});
		em('.emp-category-products').html( str );
	},


	dataOptions: function() {
		if (product_index < 0) return;

		var str = '';

		var prd 			= window.data_products[product_index][0];
		product 			= REGISTER.copy(prd);		
		product_options 	= product.options;	
		
		em.each(product_options, function(index, options) {	
			em.each(options, function(opt_index, option) {				
				
				
				var style = '', dlogic = '';

				if (1 == parseInt(option.is_logic)) {		
					if (option.logic !== '') {
						var logic = option.logic.split('::');
							dlogic = logic[1] + ':' + logic[2];

						if ('show' == logic[0])
							style = 'style="display: none"';
					}			
				}


				var selected = REGISTER.isSelected('option', index, 0);				

				str += `
				<span class="emp-button emp-option `+ selected +`" data-action="variations" data-index="`+ index +`" data-sequence="4" data-logic="`+ dlogic +`" `+ style +`>
					<span>`+ option.name +`</span>
				</span>	
				`;
			});			
		});

		str = (str !== '')? str : '<span class="emp-default-box"></span>';

		em('.emp-product-options').html( str );
	},


	dataVariations: function() {
		if (option_index < 0) return;


		em('.emp-half-button')
			.removeClass('selected');
		em('.emp-half-button[data-value="Whole"]')
			.addClass('selected');


		var str = '';		
		option_variations = product_options[option_index][0];
		
		em.each(option_variations.options, function(index, opt) {
			
			var option = opt[0];
			var selected = REGISTER.isSelected('variation', option_index, index);
			
			str += `
			<span class="emp-button emp-option-variations `+ selected +`" data-action="halfnhalf" data-index="`+ index +`" data-sequence="4" data-gid="`+ option_variations.ID +`" data-name="`+ option.name +`">
				<span>`+ option.name +`</span>
			</span>
			`;
		});
		em('.emp-product-variations').html( str );
	},


	displayProducts: function() {
		em('.emp-menu-product').hide();
		em('.emp-menu-product').each(function() {

			var pid = em(this).data('pid') + '';		
			if (em.inArray( pid, category_pid ) >= 0)
				em(this).css('display', 'table');
		});
	},


	display: function() {
		
		if (sequence) em('.emp-back-screen').css('opacity', '1');
		else em('.emp-back-screen').css('opacity', '0');

		REGISTER.displayProducts();

		em('.emp-content').hide();
		em('.emp-color-group .emp-button').css('background-color', color);		
		em('.emp-content[data-display="'+ sequence_display[sequence] +'"]')				
			.css('display', 'block');		
	},


	isSelected: function( type, o_index, v_index ) {
		if (typeof( product_selected ) === 'undefined') return 'unselected';
		
		if (type === 'option')
			return (product_selected['options'][o_index][0]['selected'])? 'selected' : 'unselected';
		else if (type === 'variation')			
			return (product_selected['options'][o_index][0]['options'][v_index][0]['selected'])? 'selected' : 'unselected';		
	},


	selectAllOptions: function() {
		if (typeof(product_selected) === 'undefined') return false;

		var options = product_selected['options'][option_index][0]['options'];
		em.each(options, function(index, option) {
			product_selected['options'][option_index][0]['options'][index][0]['selected'] = true;
		});
	},


	updateOption: function() {		
		if (typeof(product_selected) === 'undefined') return false;

		product_selected['options'][option_index][0]['selected'] = true;

		if (variation_index >= 0 && active_option === 'variation') {
			
			product_selected['options'][option_index][0]['options'][variation_index][0]['selected'] = true;
			

			var type = product_selected['options'][option_index][0]['options'][variation_index][0]['type'];
			
			em('.emp-half-button')
				.removeClass('selected')
				.addClass('unselected');
			em('.emp-half-button[data-value="'+ type +'"]')
				.removeClass('unselected')
				.addClass('selected');
		}

		orders[ordertype][order_index] = product_selected;
		
		REGISTER.updateTotal();	
	},


	removeOption: function() {		
		if (option_index >= 0) {
			if (active_option === 'option') {
				product_selected['options'][option_index][0]['selected'] = false;
				em.each(product_selected['options'][option_index][0]['options'], function(index, opt) {
					product_selected['options'][option_index][0]['options'][index][0]['selected'] = false;
				});	

				option_index = -1;
				variation_index = -1;			

			} else if (active_option === 'variation') {
				
				button = em(active_e.target).closest('.emp-button');

				variation_index = parseInt( button.data('index') );
				variation_index = isNaN(variation_index)? -1 : variation_index;	

				product_selected['options'][option_index][0]['options'][variation_index][0]['selected'] = false;

				variation_index = -1;
			}
		}

		orders[ordertype][order_index] = product_selected;		

		REGISTER.updateTotal();		
		REGISTER.dataOptions();
		REGISTER.dataVariations();
	},


	signIn: function() {
		REGISTER.server({
			action: 'register_server',
			handler: 'signin',
			pin: window.login_input_pin			
		}, function(request) {			
			user_id = parseInt(request.user_id);
			REGISTER.build();	
		});
	},


	checkIn: function() {
		REGISTER.server({
			action: 'register_server',
			handler: 'checkin',
			pin: window.login_input_pin			
		}, function(request) {			
			user_id = parseInt(request.user_id);
			REGISTER.build();	
		});
	},


	checkOut: function() {
		REGISTER.server({
			action: 'register_server',
			handler: 'checkout',
			pin: window.login_input_pin			
		}, function(request) {	

			em('.emp-user-menu').hide();
			em('.emp-login-display').val('');			

			user_id = 0;			
			window.login_input_pin = '';
			
			REGISTER.resetVar();			
			REGISTER.build();

		});
	},


	logOut: function(e) {
		REGISTER.server({
			action: 'register_server',
			handler: 'logout'			
		}, function(request) {			

			em('.emp-user-menu').hide();
			em('.emp-login-display').val('');			

			user_id = 0;			
			window.login_input_pin = '';

			REGISTER.build();

		});
	},


	userInfo: function() {
		REGISTER.server({
			action: 'register_server',
			handler: 'user-info'			
		}, function(request) {			

			em('.emp-user-info .username')
				.text(request.user.data.display_name);

		});
	},


	voidOven: function(e) {
		REGISTER.server({
			action: 'register_server',
			handler: 'void',
			orders: orders[ordertype]					
		}, function(request) {
			
			orders[ordertype] = [];
			sequence = 0;

			REGISTER.updateTotal();
			REGISTER.screen();

			em('.emp-order-void').hide();

		});
	},


	sendToOven: function() {

		em('.emp-cart-summary, .emp-quantity-options, .emp-half-n-half').hide();
		em('.emp-foot-box').removeAttr('style');		
		
		REGISTER.server({
			action: 'register_server',
			handler: 'send-to-oven',			
			orders: orders[ordertype],
			order_type: ordertype,
			order_total: order_total,
			subtotal: order_total,
			notes: order_notes
		}, function(request) {
			
			orders[ordertype] = [];
			ordertype = '';
			REGISTER.resetVar();
			sequence = 0;
			REGISTER.updateTotal();
			REGISTER.cartSummary();
			REGISTER.screen();


			REGISTER.displayCheckout();

			//window.location.href = register_site;

		});
	},


	displayCheckout: function() {
		em('.emp-checkout-frame').attr('src', register_site);
		em('.emp-checkout-window').show();
	},


	transactions: function(e) {
		REGISTER.server({
			action: 'register_server',
			handler: 'transactions'					
		}, function(request) {

			em('.emp-transactions-wrap')
				.html(request.content);			

			setTimeout(function (){
				em('.emp-transactions').css('display', 'table');
			}, 1000);

		});
	},


	wait: function(sts) {
		if (sts) {
			em('.emp-server-text').text('Processing request...');
			em('.emp-server-request').css('display', 'table');
		} else {
			setTimeout(function() {
				em('.emp-server-request')
					.hide()
					.removeClass('success failed');	
			}, 3000);			
		}		
	},


	server: function(data, callback) {
		
		REGISTER.wait(true);
		

		em.ajax({
			url: ajax_object.ajax_url,
			type: 'POST',			
			data: data,
			dataType: 'JSON'
		})
		.done(function(request) {						
			if (!request.success) {
				em('.emp-server-request').addClass('failed');				
			} else {
				em('.emp-server-request').addClass('success');
				callback( request );
			}
			em('.emp-server-text').text( request.message );
			REGISTER.wait(false);
		});
	},


	halfNHalf: function(e) {
		em('.emp-half-button')
			.removeClass('selected')
			.addClass('unselected');

		var btn = em(e.target).closest('.emp-half-button');
			btn
				.removeClass('unselected')
				.addClass('selected');

		product_selected['options'][option_index][0]['options'][variation_index][0]['type'] = btn.data('value');
		REGISTER.updateTotal();					
	},


	displayLogic: function() {
		em('.emp-option-variations').each(function() {

			var option = em(this);
			var lid = option.data('gid');
			var name = option.data('name');
			var doption = em('.emp-option[data-logic="'+ lid + ':' + name +'"]');

			if (option.hasClass('selected'))
				doption.show();
			else
				doption.hide();

		});
	},


	screen: function() {
		REGISTER.dataOptions();
		REGISTER.dataVariations();
		REGISTER.title();
		REGISTER.display();
		REGISTER.checkOven();
		REGISTER.displayLogic();
	},


	userChoice: function() {
		return is_change;
	},


	askUser: function(e) {
		active_e = e;
		em('.emp-ask-user').css('display', 'table');
	},


	resetOptions: function() {
		em('.emp-button.selected')
			.addClass('unselected')
			.removeClass('selected');
	},


	resetVar: function() {

		add_to_oven 		= false;
		is_change 			= false;
		product_index 		= -1;
		option_index 		= -1;
		variation_index 	= -1;
		product_selected 	= undefined;

		REGISTER.resetOptions();

	},


	button: function(e) {

		button 		= em(e.target).closest('.emp-button');
		

		var action 	= button.data('action');
		var value 	= button.data('value');


		switch (action) {
			case 'ordertype': 
				ordertype = value;
				break;


			case 'category':			
				REGISTER.resetVar();

				var pid = em(e.target).closest('.emp-menu').data('pid') + '';
				category_pid = pid.split('|');

				break;


			case 'options': 
				
				REGISTER.resetVar();

				product_index = parseInt( button.data('index') );
				product_index = isNaN(product_index)? -1 : product_index;				
				break;


			case 'variations': 

				if (!add_to_oven) {
					alert('Add to Oven first before adding this option.');					
					return false;
				}


				option_index  = parseInt( button.data('index') );
				option_index  = isNaN(option_index)? -1 : option_index;	
				active_option = 'option';


				if (REGISTER.userChoice() || button.hasClass('unselected')) {						
					REGISTER.selectAllOptions();
					REGISTER.updateOption();	
				} else {					
					REGISTER.askUser(e);
					return false;
				}

				break;


			case 'halfnhalf':


				variation_index = parseInt( button.data('index') );
				variation_index = isNaN(variation_index)? -1 : variation_index;	
				active_option 	= 'variation';

				if (REGISTER.userChoice() || button.hasClass('unselected')) {					
					REGISTER.updateOption();
					REGISTER.dataVariations();

					em('.emp-color-group .emp-button').css('background-color', color);
					em('.emp-half-button').css('background-color', color);
					em('.emp-half-n-half').css('display', 'table');				
					em('.emp-half-n-half')
						.animate({
							'right': '0'
						}, 'fast');			

					return false;
				} else {					
					REGISTER.askUser(e);
					return false;
				}
				break;


			case 'add-to-oven':
				
				if (button.data('role') === 'add') {				
					
					var prd 			= window.data_products[product_index][0];
					order_update 		= false;
					product_selected 	= REGISTER.copy(prd);				
					
					REGISTER.addToOven();

				}

				return false;
				break;

		}	
		
		
		sequence 		= button.data('sequence');					
		color			= (typeof(button.data('color')) !== 'undefined')? button.data('color') : color;	
		header_title	= button.find('span').html();
		sequence_title[sequence] = header_title;		
		
		REGISTER.screen();		
		
	},


	cartOptions: function(prd_options) {

		var content = '';

		if (typeof(prd_options) !== 'undefined') {

			em.each(prd_options, function(index, options) {									
				if (options[0]['selected']) {			
					

					var content_options = '';
					em.each(options[0]['options'], function(opt_index, option) {
						if (option[0]['selected']) {

							var type 	= (option[0]['type'] !== 'Whole')? 0.5 : 1;
							var amount 	= parseFloat(option[0]['amount']) * type;

							content_options += `
							<div class="emp-order-option-row">
								<span class="option-name">`+ option[0]['name'] +`</span>
								<span class="option-type">`+ option[0]['type'] +`</span>
								<span class="option-amount">`+ REGISTER.format(amount, true) +`</span>
							</div>
							`;

						}
					});


					content += `
					<div class="emp-order-options">
						<div class="emp-order-option-group">`+ options[0]['name'] +`</div>
						`+ content_options +`
					</div>`;

				}
			});		

		}

		return content;
	},


	cartSummary: function() {

		var cart_title = ordertype.split('_').join(' ');
			cart_title = (cart_title === '')? 'No Order' : cart_title;
		em('.emp-cart-summary .emp-title').text( cart_title );


		var content = '';
		var cart = orders[ordertype];		
		if (typeof(cart) === 'undefined') {
			em('.emp-cart-orders').html( content );
			return false;
		}

		
		em.each(cart, function(index, prd) {

			content += `
			<div class="emp-order" data-index="`+ index +`">
				<span class="emp-order-quantity">
					<span>`+ prd.quantity +`</span>
				</span>
				<span class="emp-order-name">`+ prd.name +`</span>
				<span class="emp-order-price">`+ REGISTER.format(prd.price, true) +`</span>
				`+ REGISTER.cartOptions(prd.options) +`
				<div class="emp-order-cta">
					<div class="emp-order-cta-wrap">
						<span class="emp-order-remove">
							<span>Remove?</span>
						</span>
						<span class="emp-order-change">
							<span>Change Quantity?</span>
						</span>
					</div>
				</div>
			</div>
			`;


		});	
		

		em('.emp-cart-orders').html( content );

	},


	checkUser: function(callback) {		
		if (user_id) {
			em('.emp-login-screen').hide();
			em('.emp-login-display').val('');
			callback();
		}
		else em('.emp-login-screen').css('display', 'table');		
	},


	actions: function() {

		em(document).on('click', '.emp-button', function(e) {
			REGISTER.control(e, REGISTER.button);
		});


		em(document).on('click', '.emp-back-screen, .emp-back', function(e) {
			REGISTER.control(e, function(e) {			
				

				if (sequence === 2 || sequence === 3)
					REGISTER.resetVar();

				is_change = false;
				sequence -= 1;
				sequence = (sequence < 0)? 0 : sequence;

				if (ordertype !== '' && sequence === 0)
					sequence = 1;

				REGISTER.screen();
			});
		});

		em(document).on('click', '.emp-half-cancel', function(e) {
			REGISTER.control(e, function(e) {
				is_change = false;
				em('.emp-half-n-half').hide();
			});
		});

		em(document).on('click', '.emp-half-button', function(e) {
			REGISTER.control(e, REGISTER.halfNHalf);
		});

		em(document).on('click', '.emp-option-update', function(e) {
			REGISTER.control(e, function(e) {
				em('.emp-ask-user').hide();
				is_change = true;
				REGISTER.button( active_e );
				is_change = false;
			});
		});

		em(document).on('click', '.emp-option-unselect', function(e) {
			REGISTER.control(e, function(e) {
				em('.emp-ask-user').hide();
				is_change = false;
				REGISTER.removeOption();
				em('.emp-color-group .emp-button').css('background-color', color);
			});
		});

		em(document).on('click', '.emp-cart-sales', function(e) {
			REGISTER.control(e, function(e) {

				var cart_button = em(e.target);
				
				if (cart_button.hasClass('closed')) {

					cart_button
						.removeClass('closed')
						.addClass('opened');
					em('.emp-cart-summary').show();
					em('.emp-foot-box').css('opacity', '0');
					em('.emp-cart-checkout, .emp-cart-sales').css('opacity', '1');


					REGISTER.cartSummary();					

				} else {
					cart_button
						.removeClass('opened')
						.addClass('closed');
					em('.emp-cart-summary, .emp-quantity-options').hide();
					em('.emp-foot-box').removeAttr('style');
				}

				return false;

			});
		});

		em(document).on('click', '.emp-order-quantity', function(e) {
			REGISTER.control(e, function(e) {

				update_button = em(e.target).closest('.emp-order-quantity');
				update_button.addClass('selected');

				update_index  = parseInt(update_button.parent().data('index'));

				
				em('.emp-quantity-num').removeClass('selected');
				em('.emp-quantity-num').each(function() {
					if (parseInt(em(this).text()) === parseInt(update_button.text()))
						em(this).addClass('selected');
				});

				em('.emp-quantity-options').css('display', 'table');


			});
		});

		em(document).on('click', '.emp-quantity-num', function(e) {
			REGISTER.control(e, function(e) {

				var qty = parseInt(em(e.target).text());
				orders[ordertype][update_index]['quantity'] = qty;
				update_button.find('span').text( qty );	
				update_button.removeClass('selected');

				REGISTER.updateTotal();	

				em('.emp-cart-sales')
					.removeClass('opened')
					.addClass('closed')
					.trigger('click');

				em('.emp-quantity-options').hide();

			});
		});

		em(document).on('click', '.emp-order-name, .emp-order-options', function(e) {
			REGISTER.control(e, function(e) {

				update_order = em(e.target).closest('.emp-order');
				update_index = parseInt(update_order.data('index'));

				update_order
					.find('.emp-order-cta')
					.css({
						'display': 'table',
						'height': update_order.height() + 'px'
					});

			});
		});

		em(document).on('click', '.emp-order-remove', function(e) {
			REGISTER.control(e, function(e) {

				orders[ordertype].splice(update_index, 1);
				REGISTER.updateTotal();	
				em('.emp-cart-sales')
					.removeClass('opened')
					.addClass('closed')
					.trigger('click');
				em('.emp-quantity-options').hide();

				if (orders[ordertype].length <= 0) {
					sequence = 2;
					REGISTER.screen();
				}

			});
		});

		em(document).on('click', '.emp-order-change', function(e) {
			REGISTER.control(e, function(e) {

				update_order
					.find('.emp-order-quantity')
					.trigger('click');
				em('.emp-order-cta').hide();

			});
		});

		em(document).on('click', '.emp-cart-void', function(e) {
			REGISTER.control(e, function(e) {
				em('.emp-order-void').css('display', 'table');
			});
		});

		em(document).on('click', '.emp-confirm-sure', function(e) {
			REGISTER.control(e, REGISTER.voidOven);
		});

		em(document).on('click', '.emp-confirm-cancel', function(e) {
			REGISTER.control(e, function(e) {
				em('.emp-order-void').hide();
			});
		});

		em(document).on('click', '.emp-cart-notes', function(e) {
			REGISTER.control(e, function(e) {

				em('.emp-content-notes').css('display', 'table');
				em('.emp-content-notes')
					.find('textarea')
					.val( order_notes )
					.focus();

			});
		});

		em(document).on('click', '.emp-notes-cancel', function(e) {
			REGISTER.control(e, function(e) {
				em('.emp-content-notes').hide();
			});
		});

		em(document).on('click', '.emp-notes-add', function(e) {
			REGISTER.control(e, function(e) {
				
				order_notes = em('.emp-content-notes').find('textarea').val();
				em('.emp-content-notes').hide();

			});
		});

		em(document).on('click', '.emp-cart-checkout', function(e) {
			REGISTER.control(e, function(e) {
				REGISTER.sendToOven();
			});
		});

		em('.emp-login-num').unbind().click(function(e) {
			REGISTER.control(e, function(e) {
				
				var button_pin = em(e.target).closest('.emp-login-num');
				var pin = button_pin.data('value');

				if (pin === 'clr')
					window.login_input_pin = '';
				else if (pin === 'del')
					window.login_input_pin = window.login_input_pin.substring(0, window.login_input_pin.length - 1);
				else
					window.login_input_pin += pin;
				
				em('.emp-login-display').val( window.login_input_pin );

			});
		});

		em(document).on('click', '.emp-login-singin', function(e) {
			REGISTER.control(e, function(e) {
				REGISTER.signIn();
			});
		});

		em(document).on('click', '.emp-login-checkin', function(e) {
			REGISTER.control(e, function(e) {
				REGISTER.checkIn();
			});
		});

		em(document).on('click', '.emp-user-checkout, .emp-login-checkout', function(e) {
			REGISTER.control(e, function(e) {
				REGISTER.checkOut();
			});
		});

		em(document).on('click', '.emp-foot-box.emp-user', function(e) {
			REGISTER.control(e, function(e) {
				em('.emp-user-menu').css('display', 'table');
			});
		});

		em(document).on('click', '.emp-user-menu-close', function(e) {
			REGISTER.control(e, function(e) {
				em('.emp-user-menu').hide();
			});
		});

		em(document).on('click', '.emp-user-logout', function(e) {
			REGISTER.control(e, REGISTER.logOut);
		});

		em(document).on('click', '.emp-transactions-close', function(e) {
			REGISTER.control(e, function(e) {
				em('.emp-transactions').hide();
			});
		});

		em(document).on('click', '.emp-user-transactions', function(e) {
			REGISTER.control(e, REGISTER.transactions);
		});

		em(document).on('click', '.emp-checkout-close', function(e) {
			REGISTER.control(e, function(e) {
				em('.emp-checkout-window').hide();
				em('.emp-checkout-frame').attr('src', '');
			});
		});

		em(document).mouseup(function(e) {			
				
			if (typeof(update_order) !== 'undefined') {
				var order_cta = update_order.find('.emp-order-cta');
				if (!order_cta.is(e.target) && order_cta.has(e.target).length === 0) 
					order_cta.hide();
			}

			var element = em('.emp-quantity-options');
			if (!element.is(e.target) && element.has(e.target).length === 0) {
				element.hide();
				em('.emp-order-quantity').removeClass('selected');
			}

			var element = em('.emp-order-void');
			if (!element.is(e.target) && element.has(e.target).length === 0) {
				element.hide();				
			}

			var element = em('.emp-content-notes');
			if (!element.is(e.target) && element.has(e.target).length === 0) {
				element.hide();				
			}

			var element = em('.emp-ask-user-cta');
			if (!element.is(e.target) && element.has(e.target).length === 0) {
				em('.emp-ask-user').hide();				
			}

			var element = em('.emp-content-notes-wrap');
			if (!element.is(e.target) && element.has(e.target).length === 0) {
				em('.emp-content-notes').hide();				
			}

			var element = em('.emp-order-void-wrap');
			if (!element.is(e.target) && element.has(e.target).length === 0) {
				em('.emp-order-void').hide();				
			}

			
		});

	}

};
REGISTER.build();