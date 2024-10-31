jQuery(document).ready(function() {
	jQuery('.menu-open').on('click', function(e) {
		e.preventDefault;
		jQuery(this).hide();
		jQuery('#cqpim-dash-sidebar').show();
		jQuery('.menu-close').show();
	});
	jQuery('.menu-close').on('click', function(e) {
		e.preventDefault;
		jQuery(this).hide();
		jQuery('#cqpim-dash-sidebar').hide();
		jQuery('.menu-open').show();
	});
	if (jQuery(window).width() > 899) {
		var height = jQuery(document).innerHeight();
		jQuery('#cqpim-dash-menu').css('height', height);
	}
	jQuery(window).resize(function() {
		if (jQuery(window).width() > 899) {
			var height = jQuery(document).innerHeight();
			jQuery('#cqpim-dash-menu').css('height', height);
		} else {
			jQuery('#cqpim-dash-menu').css('height', 'auto');		
		}
	});
	// Client Accept Quote
	jQuery('#accept_quote').click(function(e) {
		e.preventDefault();
		jQuery('#messages').html('');
		var quote_id = jQuery('#quote_id').val();
		var name = jQuery('#conf_name').val();
		var pm_name = jQuery('#pm_name').val();
		var spinner = jQuery('#overlay');
		var messages = jQuery('#messages');
		var domain = document.domain;
		var data = {
			'action' : 'pto_client_accept_quote',
			'quote_id' : quote_id,
			'name' : name,
			'pm_name' : pm_name,
			'pto_nonce' : localisation.global_nonce
		};
		if(!name) {
			alert('You must enter your name');
		} else {
			jQuery.ajax({
				url: localisation.ajaxurl,
				data: data,
				type: 'POST',
				dataType: 'json',
				beforeSend: function(){
					// show spinner
					spinner.show();
					// disable form elements while awaiting data
					jQuery('#accept_quote').prop('disabled', true);
				},
			}).done(function(response){
				if(response.error == true) {
					spinner.hide();
					// re-enable form elements so that new enquiry can be posted
					jQuery('#accept_quote').prop('disabled', false);
					jQuery('#messages').html('<p>' + response.errors + '</p>');
				} else {
					spinner.hide();
					// re-enable form elements so that new enquiry can be posted
					jQuery('#accept_quote').prop('disabled', false);
					location.reload();
				}
			});
		}
	});

	// Create dialog box to send new message for quote.
	jQuery('a#add_message_trigger').on('click', function(e){
		e.preventDefault();
		var anc    = jQuery(this).attr('id');
		var id     = anc.replace('client_', '');
		var thediv = jQuery('#' + id);
		jQuery(thediv).parent('div').attr('id', id + '_container');
		jQuery.colorbox({
			'inline'  : true,
			'href'    : '#add_message',
			'opacity' : '0.5',
		});
		jQuery.colorbox.resize();
	});

	// Send the quote message for client dashboard.
	jQuery('#add_message_ajax').click(function(e) {
		e.preventDefault();
		var visibility = jQuery('#add_message_visibility').val();
		var message    = jQuery('#add_message_text').val();
		var quote_id   = jQuery('#post_ID').val();
		var who        = jQuery('#message_who').val();
		var spinner    = jQuery('#overlay');

		var data = {
			'action'     : 'pto_add_message_to_quote',
			'visibility' : visibility,
			'message'    : message,
			'quote_id'   : quote_id,
			'who'        : who,
			'pto_nonce'  : localisation.global_nonce
		};

		jQuery.ajax({
			url        : localisation.ajaxurl,
			data       : data,
			type       : 'POST',
			dataType   : 'json',
			beforeSend : function(){
				spinner.show();
				jQuery.colorbox.resize();
				jQuery('#add_message_trigger').prop('disabled', true);
			},
		}).always(function(response) {
			spinner.hide();
			jQuery('#add_message_ajax').prop('disabled', false);
			if ( response.error == true ) {
				jQuery('#message_messages').html(response.message);
				jQuery.colorbox.resize();
			} else {
				jQuery('#message_messages').html(response.message);
				jQuery.colorbox.resize();
				location.reload();
			}
		});
	});

	// Delete quote message for client dashboard.
	jQuery('button.delete_message').click(function(e) {
		e.preventDefault();
		var quote_id = jQuery('#post_ID').val();
		var key = jQuery(this).data('id');
		var spinner = jQuery('#overlay');

		var data = {
			'action' : 'pto_delete_quote_message',
			'quote_id' : quote_id,
			'key' : key,
			'pto_nonce' : localisation.global_nonce
		};

		jQuery.ajax({
			url: localisation.ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#button.delete_message').prop('disabled', true);
			},
		}).done(function() {
				location.reload();
		});
	});

});