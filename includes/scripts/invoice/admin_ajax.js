jQuery(document).ready(function() {
	jQuery('#invoice_client').on('change', function(e) {
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
				jQuery('#client_contact').prop('disabled', false);
				jQuery('#client_contact').html(response.contacts);
			}
		});
	});
	jQuery('#edit_date_conf').on('click', function(e) {
		e.preventDefault();
		var post = jQuery(this).data('id');
		var date = jQuery('#due_date').val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_update_invoice_due',
			'post' : post,
			'date' : date,
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
				alert(response.errors);
			} else {
				spinner.hide();
				location.reload();
			}
		});
	});
	// var project = jQuery('#invoice_project').val();
	// if(!project) {
	// 	update_projects_dropdown();
	// }
	jQuery('#invoice_client').change(function(e) {
		e.preventDefault();
		update_projects_dropdown();
	});
	jQuery('#send_invoice').click(function(e) {
		e.preventDefault();
		jQuery('#messages').html('');
		var invoice_id = jQuery(this).data('id');
		var type = jQuery(this).data('type');
		var spinner = jQuery('#cqpim_overlay');
		var messages = jQuery('#messages');
		var domain = document.domain;
		var data = {
			'action' : 'pto_process_invoice_emails',
			'invoice_id' : invoice_id,
			'type' : type,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#send_invoice').prop('disabled', true);
			},
		}).always(function(response) {
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#send_invoice').prop('disabled', false);
				jQuery('#messages').html(response.errors);
			} else {
				spinner.hide();
				jQuery('#send_invoice').prop('disabled', false);
				jQuery('#messages').html(response.message);
				jQuery('#publish').trigger('click');
			}
		});
	});
	jQuery('#mark_paid_trigger').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox({
			'inline': true,
			'href': '#invoice_payment',
			'opacity': '0.5',
			'width': '500px'
		});	
		jQuery.colorbox.resize();
	});	
	jQuery('#mark_paid').click(function(e) {
		e.preventDefault();
		jQuery('#messages').html('');
		var invoice_id = jQuery(this).data('id');
		var amount = jQuery('#payment_amount').val();
		var date = jQuery('#payment_date').val();
		var notes = jQuery('#payment_notes').val();
		var method = jQuery('#payment_method').val();
		var spinner = jQuery('#cqpim_overlay');
		var messages = jQuery('#messages');
		var domain = document.domain;
		var data = {
			'action' : 'pto_mark_invoice_paid',
			'invoice_id' : invoice_id,
			'amount' : amount,
			'notes' : notes,
			'method' : method,
			'date' : date,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#mark_paid').prop('disabled', true);
			},
		}).always(function(response) {
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#mark_paid').prop('disabled', false);
				jQuery('#messages').html(response.errors);
			} else {
				//spinner.hide();
				jQuery('#mark_paid').prop('disabled', false);
				jQuery('#messages').html(response.message);
				jQuery('#publish').trigger('click');
			}
		});
	});
	jQuery('.send_reminder').click(function(e) {
		e.preventDefault();
		jQuery('#messages').html('');
		var invoice_id = jQuery(this).data('id');
		var type = jQuery(this).data('type');
		var spinner = jQuery('#cqpim_overlay');
		var messages = jQuery('#messages');
		var domain = document.domain;
		var data = {
			'action' : 'pto_send_invoice_reminders',
			'invoice_id' : invoice_id,
			'type' : type,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#send_reminder').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#send_reminder').prop('disabled', false);
				jQuery('#messages').html(response.errors);
			} else {
				spinner.hide();
				jQuery('#send_reminder').prop('disabled', false);
				jQuery('#messages').html(response.message);
				location.reload();
			}
		});
	});
	jQuery('button.delete_stage_conf').on('click', function(e){
		e.preventDefault();
		var payment_id = jQuery(this).val();
		var invoice_id = jQuery('#post_ID').val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_delete_payment',
			'payment_id' : payment_id,
			'invoice_id' : invoice_id,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('button.delete_stage_conf').prop('disabled', true);
			},
		}).always(function(response) {
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('button.delete_stage_conf').prop('disabled', false);
				alert(response.errors);
			} else {
				spinner.hide();
				jQuery('button.delete_stage_conf').prop('disabled', false);
				location.reload();
			}
		});		
	});
	jQuery('.edit-milestone').on('click', function(e){
		e.preventDefault();
		var key = jQuery(this).val();
		jQuery.colorbox({
			'inline': true,
			'href': '#invoice_payment_' + key,
			'opacity': '0.5',
			'width' : '500px',
		});	
		jQuery.colorbox.resize();
	});
	jQuery('button#edit_paid').on('click', function(e){
		e.preventDefault();
		var payment_id = jQuery(this).attr('data-key');
		var invoice_id = jQuery('#post_ID').val();
		var amount = jQuery('#payment_amount_' + payment_id).val();
		var method = jQuery('#payment_method_' + payment_id).val();
		var notes = jQuery('#payment_notes_' + payment_id).val();
		var date = jQuery('#payment_date_' + payment_id).val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_edit_payment',
			'payment_id' : payment_id,
			'invoice_id' : invoice_id,
			'amount' : amount,
			'method' : method,
			'notes' : notes,
			'date' : date,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('button#edit_paid').prop('disabled', true);
			},
		}).always(function(response) {
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('button#edit_paid').prop('disabled', false);
				alert(response.errors);
			} else {
				spinner.hide();
				jQuery('button#edit_paid').prop('disabled', false);
				location.reload();
			}
		});		
	});
	jQuery('#create_escrow_trigger').on('click', function(e) {
		e.preventDefault();
		var invoice_id = jQuery('#post_ID').val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_create_escrow_transaction',
			'invoice_id' : invoice_id,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#create_escrow_trigger').prop('disabled', true);
			},
		}).always(function(response){
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#create_escrow_trigger').prop('disabled', false);
				jQuery('#escrow_return_messages').html(response.message);
			} else {
				spinner.hide();
				jQuery('#create_escrow_trigger').prop('disabled', false);
				jQuery('#escrow_return_messages').html(response.message);
				location.reload();
			}
		});
	});
});
function update_projects_dropdown() {
		var client_id = jQuery('#invoice_client').val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_populate_invoice_projects',
			'ID' : client_id,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#invoice_client').prop('disabled', true);
			},
		}).always(function(response) {
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#invoice_client').prop('disabled', false);
				jQuery('#invoice_project').html(response.options);
			} else {
				spinner.hide();
				jQuery('#invoice_client').prop('disabled', false);
				jQuery('#invoice_project').html(response.options);
			}
		});
};