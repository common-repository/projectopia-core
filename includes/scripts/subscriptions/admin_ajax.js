jQuery(document).ready(function() {
	jQuery('#subscription_client').on('change', function(e) {
		e.preventDefault();
		var client_id = jQuery(this).val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_update_client_contacts',
			'client_id' : client_id,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
			} else {
				spinner.hide();
				jQuery('#subscription_client_contact').prop('disabled', false);
				jQuery('#subscription_client_contact').html(response.contacts);
			}
		});
	});
	jQuery('#delete_task_confirm').on('click', function(e) {
		e.preventDefault();
		var task_id = jQuery(this).data('id');
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_delete_subscription_page',
			'task_id' : task_id,
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#delete_task_confirm').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
			spinner.hide();
				jQuery('#delete_task_confirm').prop('disabled', false);
			} else {
				spinner.hide();
				jQuery('#delete_task_confirm').prop('disabled', false);
				window.location.href = response.redirect;				
			}				
		});
	});
	jQuery('#send_subscription').on('click', function(e) {
		e.preventDefault();
		var sub_id = jQuery('#post_ID').val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_send_subscription',
			'sub_id' : sub_id,
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#send_subscription').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#send_subscription').prop('disabled', false);
				jQuery('#status_messages').html(response.message);
			} else {
				spinner.hide();
				jQuery('#send_subscription').prop('disabled', false);
				jQuery('#status_messages').html(response.message);
				location.reload();
			}				
		});
	});
	jQuery('#cancel_sub_conf').click(function(e) {
		e.preventDefault();
		var post = jQuery(this).data('id');
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_cancel_subscription',
			'post_id' : post,
		}
		jQuery.ajax({
			url: localisation.ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#cancel_sub_conf').prop('disabled', true);
			},
		}).always(function(response) {
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#cancel_sub_conf').prop('disabled', false);
				alert(response.message);
			} else {
				jQuery('#cancel_sub_conf').prop('disabled', false);
				location.reload();				
			}
		});
	});
});