var kit  			= jQuery.noConflict();
var order_id 		= 0;
var db_id 			= 0;
var row_id 			= -1;
var kit_table 		= '';
var kit_status 		= '';
NYPIZZA_KITCHEN		= {





	build: function() {
		NYPIZZA_KITCHEN.clean();
		NYPIZZA_KITCHEN.actions();
	},





	clean: function() {
		kit('header, footer, #secondary').remove();			
	},





	server: function( data, callback ) {
		NYPIZZA_KITCHEN.wait(true);		
		kit.ajax({
			url: ajax_object.ajax_url,
			type: 'POST',			
			data: data,
			dataType: 'JSON'
		})
		.done(function(results) {			
			callback( results );		
			NYPIZZA_KITCHEN.wait(false);
		});
	},





	wait: function( load ) {		
		if (load) kit('body').append('<div class="kitchen-loading"></div>');
		else kit('body').find('.kitchen-loading').remove();
	},





	getOrders: function() {		
		NYPIZZA_KITCHEN.server({
			action: 'kitchen_orders'			
		}, function(results) {			
			NYPIZZA_KITCHEN.clean();
			kit('.kitchen-orders')
				.html(results.content)
				.show();
		});
	},





	kitchenOrders: function() {		
		NYPIZZA_KITCHEN.server({
			action: 'kitchen_orders'			
		}, function(results) {			
			NYPIZZA_KITCHEN.clean();
			kit('.kitchen-orders')
				.html(results.content)
				.show();
		});
	},





	update: function() {
		NYPIZZA_KITCHEN.server({
			action: 'kitchen_update',
			table: kit_table,
			status: kit_status,			
			order_id: order_id,
			db_id: db_id,
			row_id: row_id
		}, function(results) {		
			NYPIZZA_KITCHEN.clean();			
			NYPIZZA_KITCHEN.kitchenOrders();
		});
	},





	actions: function() {
		kit(document).on('click', '.kitchen-box', function(e) {
			e.preventDefault();
			e.stopPropagation();
			kit(this).find('.kitchen-popup').show();
		});
		kit(document).on('click', '.kitchen-cancel', function(e) {
			e.preventDefault();
			e.stopPropagation();
			kit(this).closest('.kitchen-popup').hide();
		});
		kit(document).on('click', '.kitchen-status', function(e) {
			e.preventDefault();
			e.stopPropagation();
			
			
			kit_table 	= kit(this).data('table');
			kit_status 	= kit(this).data('status');


			var plug 	= kit(this).data('plug').split(':');
			order_id 	= plug[0];
			db_id 		= plug[1];
			row_id 		= plug[2];

		
			NYPIZZA_KITCHEN.update();
		
		});		
		kit(window).on('load', NYPIZZA_KITCHEN.kitchenOrders);		
	}






};
NYPIZZA_KITCHEN.build();