var ord  			= jQuery.noConflict();
var order_id 		= 0;
var action 			= 'orders_get';
var order_status	= ['processing', 'on-hold', 'completed', 'refunded', 'failed', 'cancelled', 'pending-payment'];
var status  		= 'processing';
var orders_view 	= 'orders';
var is_interval 	= false;
var interval;
NYPIZZA_ORDERS 		= {





	build: function() {
		NYPIZZA_ORDERS.clean();
		NYPIZZA_ORDERS.actions();
	},





	clean: function() {
		ord('header, footer, #secondary').remove();
		ord.each(order_status, function(index, sts) {
			ord('.oh-order-status').removeClass(sts);
		});
		ord('.oh-order-status').addClass(status);
		ord('.orders-sidebar a').removeClass('active');
		ord('.menu-'+status).addClass('active');		
	},





	server: function( data, callback ) {
		NYPIZZA_ORDERS.wait(true);		
		ord.ajax({
			url: ajax_object.ajax_url,
			type: 'POST',			
			data: data,
			dataType: 'JSON'
		})
		.done(function(results) {				
			callback( results );		
			NYPIZZA_ORDERS.wait(false);
		});
	},





	wait: function( load ) {		
		if (load) ord('body').append('<div class="pos-loading"></div>');
		else ord('body').find('.pos-loading').remove();
	},





	maxH: function(el) {
		var maxHeight = Math.max.apply(null, el.map(function ()	{
		    return ord(this).height();
		}).get());
		return maxHeight;
	},





	ordersGet: function() {		
		NYPIZZA_ORDERS.server({
			action: action,
			status: status
		}, function(results) {			
			NYPIZZA_ORDERS.clean();
			ord('.orders-lists-body').html(results.content);
		});
	},





	ordersDetails: function() {
		try {
			
			var qry = (window.location.hash.substr(1)).split('/');
			order_id = qry[2];
			
			NYPIZZA_ORDERS.server({
				action: 'orders_details',
				order_id: order_id				
			}, function(results) {
				console.log(results.order);
				ord('.orders-sidebar a').removeClass('active');	
				ord('.orders-details')
					.html(results.content)
					.show();	
			});

		} catch(e) {}
	},





	orderChangeStatus: function(new_status) {
		NYPIZZA_ORDERS.server({
			action: 'change_status',
			order_id: order_id,
			PID: ord('.pizza_order_id').val(),
			status: new_status
		}, function(response) {
			alert(response.response);
		});
	},





	ordersPage: function() {		
		if (window.location.hash) {
			status = window.location.hash.substr(1);
			if (order_status.indexOf(status) >= 0) {				

				ord('.orders-lists').show();
				ord('.orders-details').hide();

				NYPIZZA_ORDERS.ordersGet();				

			} else {
				
				ord('.orders-lists').hide();				
				NYPIZZA_ORDERS.ordersDetails();

			}
		} else {
			window.location.hash = '#processing';
		}
	},





	feed: function() {
		interval = setInterval(function() {
			is_interval = true;
			NYPIZZA_ORDERS.ordersPage();
		}, 60000);
	},





	ordersTab: function(section, tab) {
		ord('.orders-details-header a').removeClass('active');
		tab.addClass('active');
		ord('.orders-section').hide();
		ord('.orders-' + section).show();
	},





	actions: function() {
		ord(document).on('click', '.order-view', function(e) {
			e.preventDefault();
			e.stopPropagation();					
			ord('.orders-lists').hide();
			orders_view = 'orders';		
			window.location.hash = ord(this)[0].hash;
		});

		ord(document).on('click', '.menu-orders', function(e) {
			e.preventDefault();
			e.stopPropagation();			
			
			NYPIZZA_ORDERS.ordersTab( 'orders', ord(this) );
		});

		ord(document).on('click', '.menu-billing', function(e) {
			e.preventDefault();
			e.stopPropagation();			
			
			NYPIZZA_ORDERS.ordersTab( 'billing', ord(this) );
		});

		ord(document).on('click', '.menu-shipping', function(e) {
			e.preventDefault();
			e.stopPropagation();			
			
			NYPIZZA_ORDERS.ordersTab( 'shipping', ord(this) );
		});

		ord(document).on('click', '.change-order-status li', function(e) {
			e.preventDefault();
			e.stopPropagation();

			var new_status = ord(this).data('status');
			ord('.change-order-status li').removeClass('active');
			ord(this).addClass('active');

			NYPIZZA_ORDERS.orderChangeStatus( new_status );
		});

		ord(window).on('hashchange', NYPIZZA_ORDERS.ordersPage);
		ord(window).on('load', NYPIZZA_ORDERS.ordersPage);		
	}






};
NYPIZZA_ORDERS.build();