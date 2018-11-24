/**
 * Module: Options
 * Description: Management
 */
var ny = jQuery.noConflict();

NY_OPTIONS = {


	build: function() {

		NY_OPTIONS.action();
		NY_OPTIONS.options();

	},



	/**
	 * Ajax Feeds
	 * @return {[type]} [description]
	 */
	feed: function( data, callback ) {

		NY_OPTIONS.wait( data.el, true );

		ny.ajax({
			url: ajax_object.ajax_url,
			type: 'POST',			
			data: data
		})
		.done(function(response) {
			NY_OPTIONS.wait( data.el, false );
			callback( ny.parseJSON(response));				 		
		});

	},



	/**
	 * Wait
	 * @param  {[type]} el [description]
	 * @return {[type]}    [description]
	 */
	wait: function(el, wait) {
		if (wait)
			ny(el).addClass('waiting');
		else
			ny(el).removeClass('waiting');
	},




	/**
	 * Controller
	 * @return {[type]} [description]
	 */
	action: function() {
		

		/**
		 * Show Popup
		 */
		ny('#ny-new-option').on('click', function(e) {
			NY_OPTIONS.control(e, function() {

				ny('#ny-popup').show();

			});
		});


		/**
		 * Close Popup
		 */
		ny('#ny-popup-close').on('click', function(e) {
			NY_OPTIONS.control(e, function() {
				
				ny('#ny-popup')
					.hide()
					.find('input[type="text"]').val('');

			});
		});


		/**
		 * Add Option Row
		 */
		ny(document).on('click', '.add-row', function(e) {
			NY_OPTIONS.control(e, function() {				

				var list = ny(e.target).parent().parent();
				var option = `
					<li class="ui-sortable-handle">`+ list.html() +`</li>
				`;

				var row = ny(option);
					row.find('.input-name').val('');
					row.find('.input-amount').val(0);

				ny( row ).insertAfter( list );
				

			});
		});


		/**
		 * Remove Option Row
		 */
		ny(document).on('click', '.remove-row', function(e) {
			NY_OPTIONS.control(e, function() {				

				var list = ny(e.target).parent().parent();
				list.fadeOut('fast', function() {
					list.remove();
				});				

			});
		});


		/**
		 * Search Focus
		 */
		ny(document).on('focus', '#search-option', function() {
			
			ny('.option-search').addClass('search-focus');
			ny('.default-lists').addClass('option-blur');

		}).on('keyup', '#search-option', function() {

			var keyword = this.value;
			var icon = 'dashicons dashicons-clipboard';
			

			if (ny(this).hasClass('option-product'))
				icon = 'dashicons dashicons-plus';

			setTimeout(function() {

				NY_OPTIONS.feed({
					action: 'options_feed',
					name: keyword,
					icon: icon,
					el: '.option-search .dashicons-search'
				}, function( response ) {
					
					ny('.search-feeds')
						.html( response.results )
						.show();

				});

			}, 500);

			
			
		});


		/**
		 * Click Outside Element
		 */
		ny(document).mouseup(function(e) {

			var search = ny('#search-option');
			var feeds = ny('.search-feeds');

			if ( (!search.is(e.target) && search.has(e.target).length === 0)
				 && (!feeds.is(e.target) && feeds.has(e.target).length === 0) ) {

				ny('.option-search').removeClass('search-focus');

				ny('.default-lists').removeClass('option-blur');

				ny('.search-feeds')
					.html('')
					.hide();

			}

		});


		/**
		 * Add Option to Product
		 */
		ny(document).on('click', '.option-option', function(e) {
			NY_OPTIONS.control(e, function() {

				var option = ny(e.target);
				if (!option.is('a'))
					option = option.parent();

				option.find('input').remove();

				if (option.hasClass('active-option')) {
					option.removeClass('active-option');					
				} else {
					option.addClass('active-option');
					option.append('<input type="hidden" name="options[]" value="'+ option.data('option') +'">');

					NY_OPTIONS.override(
						ny('.option-override-modal').data('post'),
						option.data('option')
					);
				}

			});
		});


		/**
		 * Close override modal
		 */
		ny(document).on('click', '.close-override-modal', function(e) {
			NY_OPTIONS.control(e, function() {
				ny('.option-override-modal').hide();
				ny('.option-override-fields').html('');
			});
		});


		/**
		 * Save override changes
		 */
		ny(document).on('click', '.option-override-save', function(e) {
			NY_OPTIONS.control(e, function() {

				var post_id = ny('.option-override-modal').data('post');
				var option_id = ny('.option-override-modal').data('id');
				var option = [];

				ny('.option-override-save').text('Saving...');
				ny('.override-fields').each(function() {

					var name = ny(this).find('span').text();
					var price = ny(this).find('.option-override-price').val();
					var description = ny(this).find('.option-override-description').val();

					option.push({
						amount: price,
						description: description,
						name: name
					});
				});

				NY_OPTIONS.saveOverride( post_id, option_id, option );

			});
		});


		/**
		 * Remove option's override
		 */
		ny(document).on('click', '.option-override-remove', function(e) {
			NY_OPTIONS.control(e, function() {
				ny('.option-override-remove').text('Removing...');
				ny('.option-override-modal').find('.option-override-price').val(0);
				var post_id = ny('.option-override-modal').data('post');
				var option_id = ny('.option-override-modal').data('id');
				var option = [];
				NY_OPTIONS.saveOverride( post_id, option_id, option );
			});
		});


		/**
		 * Display conditional logic
		 */
		ny(document).on('click', '.option-create-logic', function(e) {
			NY_OPTIONS.control(e, function() {

				var logic = ny(e.target);
				var condition = ny('.options-logic-conditions');			


				if (logic.hasClass('selected')) {
					logic
						.removeClass('selected')
						.find('input')
						.prop('checked', false);

					condition.hide();

				} else {
					logic
						.addClass('selected')
						.find('input')
						.prop('checked', true);

					condition.show();
				}

			});
		});


		/**
		 * Display options group
		 */
		ny(document).on('change', '.conditional-logic-option-group', function() {

			var grp_id = ny(this).val();			
			var sel_option = ny('.conditional-logic-option');

			sel_option
				.find('option')
				.hide();

			sel_option
				.find('option.grp-option-' + grp_id)
				.show();

			sel_option
				.find('option[value=""]')
				.prop('selected', true)
				.show();
		});


	},



	override: function( post_id, option_id ) {
		NY_OPTIONS.feed({
			action: 'options_fields',			
			post_id: post_id,
			option_id: option_id,	
			el: '.option-override-modal'		
		}, function( response ) {
			
			if (response.results !== '') {

				ny('.option-override-modal')
					.attr('data-id', option_id)
					.show();
				ny('.option-override-fields')
					.html( response.results )
					.css({ 
						'margin-top': ((ny(window).height() - ny('.option-override-fields').height())/ 2) + 'px' 
					});;

				setTimeout(function(){
					ny('.option-override-fields').css({ 
						'margin-top': ((ny(window).height() - ny('.option-override-fields').height())/ 2) + 'px' 
					});
				}, 500);

			}

		});
	},


	/**
	 * Save override
	 */
	saveOverride: function( post_id, option_id, options ) {
		NY_OPTIONS.feed({
			action: 'override_save',
			post_id: post_id,			
			option_id: option_id,
			options: options,	
			el: '.option-override-modal'		
		}, function( response ) {
			ny('.option-override-save').text('Save Changes');
			ny('.option-override-remove').text('Remove Override');
		});
	},



	/**
	 * Sort Options
	 * @return {[type]} [description]
	 */
	options: function() {
		ny( "#options-sortable" ).sortable();
    	ny( "#options-sortable" ).disableSelection();
	},



	/**
	 * Disable Page Loading
	 * @param  {[type]}   e        [description]
	 * @param  {Function} callback [description]
	 * @return {[type]}            [description]
	 */
	control: function(e, callback) {

		e.preventDefault();
		e.stopPropagation();

		callback();
	}

};
NY_OPTIONS.build();