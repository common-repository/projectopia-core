jQuery(document).ready(function() {
	jQuery('#delete_task').on('click', function(e) {
		e.preventDefault();
		var task_id = jQuery(this).data('id');
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_delete_expense_page',
			'task_id' : task_id,
			'pto_nonce' : localisation.global_nonce,
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#delete_task').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
			spinner.hide();
				jQuery('#delete_task').prop('disabled', false);
			} else {
				spinner.hide();
				jQuery('#delete_task').prop('disabled', false);
				window.location.href = response.redirect;			
			}				
		});
	});
	jQuery('.request_auth').on('click', function(e) {
		e.preventDefault();
		var expense_id = jQuery(this).data('id');
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_request_expense_auth',
			'expense_id' : expense_id,
			'pto_nonce' : localisation.global_nonce,
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('.request_auth').prop('disabled', true);
			},
		}).always(function(response){
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('.request_auth').prop('disabled', false);
				jQuery('#expense-messages').html(response.message);
			} else {
				spinner.hide();
				jQuery('.request_auth').prop('disabled', false);
				jQuery('#expense-messages').html(response.message);
				location.reload();
			}
		});
	});
	jQuery('.auth_launch').on('click', function(e) {
		e.preventDefault();
		jQuery.colorbox({
			'maxWidth':'95%',
			'inline': true,
			'fixed': true,
			'href': '#auth_conf_box',	
			'opacity': '0.5',
		});			
	});
	jQuery('button.cancel-colorbox').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox.close();
	});
	jQuery('.process_auth').on('click', function(e) {
		e.preventDefault();
		var expense_id = jQuery(this).val();
		var decision = jQuery('#expense_auth_decision').val();
		var notes = jQuery('#expense_auth_notes').val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_process_expense_auth',
			'expense_id' : expense_id,
			'decision' : decision,
			'notes' : notes,
			'pto_nonce' : localisation.global_nonce,
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('.request_auth').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('.request_auth').prop('disabled', false);
				jQuery('#expense_auth_messages').html(response.message);
				jQuery.colorbox.resize();
			} else {
				spinner.hide();
				jQuery('.request_auth').prop('disabled', false);
				jQuery('#expense_auth_messages').html(response.message);
				jQuery.colorbox.resize();
				location.reload();
			}
		});
	});
	jQuery('#income_control_date').on('change', function(e) {
		e.preventDefault();
		var date = jQuery('#income_control_date').val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_edit_income_graph',
			'date' : date,
			'type' : 'date',
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
			spinner.hide();
			location.reload();	
		});
	});	
});