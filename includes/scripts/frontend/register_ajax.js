jQuery(document).ready(function() {
	jQuery('#add-envato-code-trigger').on('click', function(e){
		e.preventDefault();
			jQuery.colorbox({
				'inline': true,
				'href': '#add-envato-code-div',							
				'opacity': '0.5',	
			});	
			jQuery.colorbox.resize();	
	});
	jQuery('button.cancel-colorbox').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox.close();
	});
	jQuery('#cqpim_submit_support').click(function(e) {
		e.preventDefault();
		var envato_code = jQuery('#envato-code').val();
		var envato_email = jQuery('#envato-email').val();
		var envato_email_repeat = jQuery('#envato-email-repeat').val();
		var data = {
			'action' : 'pto_client_register_support',
			'envato_code' : envato_code,
			'envato_email' : envato_email,
			'envato_email_repeat' : envato_email_repeat
		}
		jQuery.ajax({
			url: localisation.ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				jQuery('#overlay').show();
				jQuery('#cqpim_submit_support').prop('disabled', true);
			},
		}).always(function(response) {
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				jQuery('#cqpim_submit_support').prop('disabled', false);
				jQuery('#overlay').hide();
				jQuery('#login_messages').addClass('error');
				jQuery('#login_messages').html(response.message);
				jQuery('#login_messages').fadeIn(500);
			} else {
				jQuery('#cqpim_submit_support').prop('disabled', false);
				jQuery('#overlay').hide();				
				jQuery('#login_messages').removeClass('error');
				jQuery('#login_messages').addClass('success');
				jQuery('#login_messages').html(response.message);
				jQuery('#login_messages').fadeIn(500);
				jQuery('#envato-code').val('');
				jQuery('#envato-email').val('');
				jQuery('#envato-email-repeat').val('');
			}
		});
	});
	jQuery('#ticket_item').change(function(e) {
		e.preventDefault();
		var item = jQuery(this).val();
		var data = {
			'action' : 'pto_envato_verify_purchase',
			'item' : item,
		}
		jQuery.ajax({
			url: localisation.ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				jQuery('#overlay').show();
			},
		}).always(function(response) {
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				jQuery('#cqpim_submit_support').prop('disabled', false);
				jQuery('#overlay').hide();
				jQuery('#login_messages').removeClass('success');
				jQuery('#login_messages').addClass('error');
				jQuery('#login_messages').html(response.message);
				jQuery('#login_messages').fadeIn(500);
				jQuery('#reject_reason').val(response.reason);
			} else {
				jQuery('#cqpim_submit_support').prop('disabled', false);
				jQuery('#overlay').hide();				
				jQuery('#login_messages').removeClass('error');
				jQuery('#login_messages').addClass('success');
				jQuery('#login_messages').html(response.message);
				jQuery('#login_messages').fadeIn(500);
				jQuery('#reject_reason').val('');
			}
		});
	});
	jQuery('#add-envato-code').click(function(e) {
		e.preventDefault();
		var code = jQuery('#purchase_code').val();
		var data = {
			'action' : 'pto_envato_verify_additional',
			'code' : code,
		}
		jQuery.ajax({
			url: localisation.ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				jQuery('#overlay').show();
			},
		}).always(function(response) {
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				jQuery('#cqpim_submit_support').prop('disabled', false);
				jQuery('#overlay').hide();
				jQuery('#login_messages').removeClass('success');
				jQuery('#login_messages').addClass('error');
				jQuery('#login_messages').html(response.message);
				jQuery('#login_messages').fadeIn(500);
				jQuery.colorbox.resize();
			} else {
				jQuery('#cqpim_submit_support').prop('disabled', false);
				jQuery('#overlay').hide();				
				jQuery('#login_messages').removeClass('error');
				jQuery('#login_messages').addClass('success');
				jQuery('#login_messages').html(response.message);
				jQuery('#login_messages').fadeIn(500);
				jQuery.colorbox.resize();
				location.reload();
			}
		});
	});
});