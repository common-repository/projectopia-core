jQuery(document).ready(function() {
	jQuery(document).on('click', 'button.delete_file', function(e){		
		e.preventDefault();
		var attID = jQuery(this).data('id');
		jQuery('#cqpim_overlay').show();
		var hiddenField = '<input type="hidden" name="delete_file[]" value="' + attID + '" />';
		jQuery(this).parents('div.inside').prepend(hiddenField);
		jQuery('#publish').trigger('click');
	});
	jQuery('.delete_client').on('click', function(e) {
		e.preventDefault();
		var id = jQuery(this).data('id');
		jQuery.colorbox({
			'maxWidth':'95%',
			'inline': true,
			'fixed': true,
			'href': '#delete_client_warning_' + id,	
			'opacity': '0.5',
		});			
	});
	jQuery('.calendar_filter').on('change', function(e) {
		e.preventDefault();
		var filters = jQuery('.calendar_filter:checkbox:checked').map(function() {
			return this.value;
		}).get();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_filter_calendar',
			'filters' : filters,
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
			location.reload();	
		});
	});	
	jQuery('.uldc').on('click', function(e) {
		e.preventDefault();
		var id = jQuery(this).data('id');
		var spinner = jQuery('#cqpim_overlay');
		var messages = jQuery('#client_messages_' + id);
		var data = {
			'action' : 'pto_unlink_delete_client',
			'id' : id,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery.colorbox.resize();
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery(messages).html(response.message);
				jQuery.colorbox.resize();
			} else {
				jQuery(messages).html(response.message);
				jQuery.colorbox.resize();
				location.reload();
			}
		});	
	});
	jQuery('.dcu').on('click', function(e) {
		e.preventDefault();
		var id = jQuery(this).data('id');
		var spinner = jQuery('#cqpim_overlay');
		var messages = jQuery('#client_messages_' + id);
		var data = {
			'action' : 'pto_delete_client_user_confirm',
			'id' : id,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery.colorbox.resize();
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery(messages).html(response.message);
				jQuery.colorbox.resize();
			} else {
				jQuery(messages).html(response.message);
				jQuery.colorbox.resize();
				location.reload();
			}
		});		
	});	
	var type = jQuery('#post_type').val();
	if(type == 'cqpim_client') {
		jQuery('.repeater').repeater({
			isFirstItemUndeletable: true,
			show: function () {
				jQuery(this).show()
				jQuery.colorbox.resize();
			},
			hide: function (deleteElement) {
				jQuery(this).slideUp(deleteElement);
				jQuery.colorbox.resize();
			},
		});
	}
	jQuery('.save').on('click', function(e){
		e.preventDefault();
			jQuery('#publish').trigger('click');
	});
	jQuery('button.cancel-colorbox').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox.close();
	});
	jQuery('.reset-password').click(function(e) {
		e.preventDefault();
		jQuery.colorbox({
			'maxWidth':'95%',
			'width': '550px',
			'inline': true,
			'fixed': true,
			'href': '#password_reset',	
			'opacity': '0.5',
		});	
		jQuery.colorbox.resize();
	});
	jQuery('.edit-milestone').click(function(e) {
		e.preventDefault();
		var key = jQuery(this).val();
		jQuery.colorbox({
			'maxWidth':'95%',
			'inline': true,
			'fixed': true,
			'href': '#contact_edit_' + key,	
			'opacity': '0.5',
		});	
	});
	jQuery('#add_client_team').click(function(e) {
		e.preventDefault();
		jQuery.colorbox({
			'maxWidth':'95%',
			'inline': true,
			'fixed': true,
			'href': '#add_client_team_ajax',	
			'opacity': '0.5',
		});	
	});
	jQuery('#add_client_alert').click(function(e) {
		e.preventDefault();
		jQuery.colorbox({
			'maxWidth':'95%',
			'inline': true,
			'fixed': true,
			'href': '#add_client_alert_ajax',	
			'opacity': '0.5',
		});	
	});
	jQuery('.edit_alert').click(function(e) {
		e.preventDefault();
		var key = jQuery(this).val();
		jQuery.colorbox({
			'maxWidth':'95%',
			'inline': true,
			'fixed': true,
			'href': '#edit_client_alert_' + key + '_ajax',	
			'opacity': '0.5',
		});	
	});
	jQuery('#create-rec-inv').click(function(e) {
		e.preventDefault();
		jQuery.colorbox({
			'maxWidth':'95%',
			'inline': true,
			'fixed': true,
			'href': '#add-recurring-invoice',	
			'opacity': '0.5',
		});	
	});
	jQuery('#add_client_team_submit').click(function(e) {
		e.preventDefault();
		var contact_name = jQuery('#contact_name').val();
		var contact_telephone = jQuery('#contact_telephone').val();
		var contact_email = jQuery('#contact_email').val();
		var entity_id = jQuery('#post_ID').val();
		var spinner = jQuery('#cqpim_overlay');
		if(jQuery('#send_contact_details').is(':checked')) {
			send = 1;
		} else {
			send = 0
		}
		var data = {
			'action' : 'pto_client_add_contact',
			'contact_name' : contact_name,
			'contact_telephone' : contact_telephone,
			'contact_email' : contact_email,
			'entity_id' : entity_id,
			'send' : send,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#add_client_team_submit').prop('disabled', true);
				jQuery.colorbox.resize();
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#add_client_team_submit').prop('disabled', false);
				jQuery('#client_team_messages').html(response.message);
				jQuery.colorbox.resize();
			} else {
				jQuery('#add_client_team_submit').prop('disabled', false);
				jQuery('#client_team_messages').html(response.message);
				jQuery.colorbox.resize();
				location.reload();
			}
		});
	});
	jQuery('#add_client_alert_submit').click(function(e) {
		e.preventDefault();
		var alert_level   = jQuery('#alert_level').val();
		var alert_message = jQuery('#alert_message').val();
		var post_id       = jQuery('#post_ID').val();
		var is_sms_allow  = 0;
		var global        = 0;

		if ( jQuery('#alert_global').is(':checked') ) {
			global = 1;
		}

		/** This option is for SMS to client alert */
		if ( jQuery('#send_sms_alert').is(':checked') ) {
			is_sms_allow = 1;
		}

		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_client_add_alert',
			'alert_level' : alert_level,
			'alert_message' : alert_message,
			'post_id' : post_id,
			'global' : global,
			'is_sms_allow' : is_sms_allow,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#add_client_alert_submit').prop('disabled', true);
				jQuery.colorbox.resize();
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#add_client_alert_submit').prop('disabled', false);
				jQuery('#client_alert_messages').html(response.message);
				jQuery.colorbox.resize();
			} else {
				jQuery('#add_client_alert_submit').prop('disabled', false);
				jQuery('#client_alert_messages').html(response.message);
				jQuery.colorbox.resize();
				location.reload();
			}
		});
	});
	jQuery('.edit_alert_submit').click(function(e) {
		e.preventDefault();
		var key = jQuery(this).data('key');
		var alert_level = jQuery('#alert_level_' + key).val();
		var alert_message = jQuery('#alert_message_' + key).val();
		var post_id = jQuery('#post_ID').val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_client_edit_alert',
			'alert_level' : alert_level,
			'alert_message' : alert_message,
			'post_id' : post_id,
			'key' : key,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('.edit_alert_submit').prop('disabled', true);
				jQuery.colorbox.resize();
			},
		}).always(function(response){
			console.log(data);
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('.edit_alert_submit').prop('disabled', false);
				jQuery('#client_alert_messages_' + key).html(response.message);
				jQuery.colorbox.resize();
			} else {
				jQuery('.edit_alert_submit').prop('disabled', false);
				jQuery('#client_alert_messages_' + key).html(response.message);
				jQuery.colorbox.resize();
				location.reload();
			}
		});
	});
	jQuery('.delete_alert').click(function(e) {
		e.preventDefault();
		var key = jQuery(this).val();
		var global = jQuery(this).data('global');
		var post_id = jQuery('#post_ID').val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_client_delete_alert',
			'key' : key,
			'global' : global,
			'post_id' : post_id,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('.delete_alert').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('.delete_alert').prop('disabled', false);
			} else {
				jQuery('.delete_alert').prop('disabled', false);
				location.reload();
			}
		});
	});
	jQuery('#reset_pass_ajax').click(function(e) {
		e.preventDefault();
		var new_password = jQuery('#new_password').val();
		var confirm_password = jQuery('#confirm_password').val();
		var entity_id = jQuery('#post_ID').val();
		var user_id = jQuery(this).val();
		var type = jQuery('#pass_type').val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		if(jQuery('#send_new_password').is(':checked')) {
			send = 1;
		} else {
			send = 0
		}
		if(!new_password || !confirm_password) {
			jQuery('#password_messages').html('<div class="cqpim-alert cqpim-alert-danger alert-display">Password Fields cannot be blank. Please fill in both fields.</div>');		
			jQuery.colorbox.resize();
		} else {
			if(new_password != confirm_password) {
				jQuery('#password_messages').html('<div class="cqpim-alert cqpim-alert-danger alert-display">The passwords do not match, please correct this before continuing.</div>');
				jQuery.colorbox.resize();
			} else {
				var data = {
					'action' : 'pto_reset_password',
					'new_password' : new_password,
					'confirm_password' : confirm_password,
					'send' : send,
					'user_id' : user_id,
					'entity_id' : entity_id,
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
						jQuery('#reset_pass_ajax').prop('disabled', true);
						jQuery.colorbox.resize();
					},
				}).always(function(response){
					console.log(response);
				}).done(function(response){
					if(response.error == true) {
						spinner.hide();
						jQuery('#reset_pass_ajax').prop('disabled', false);
						jQuery('#password_messages').html(response.message);
					} else {
						spinner.hide();
						jQuery('#reset_pass_ajax').prop('disabled', false);
						jQuery('#password_messages').html(response.message);
						jQuery.colorbox.resize();
						location.reload();
					}
				});
			}
		}
	});
	jQuery('#add_rec_inv').click(function(e) {
		e.preventDefault();
		var client_id = jQuery(this).val();
		var title = jQuery('#rec-inv-title').val();
		var start = jQuery('#rec-inv-start').val();
		var end = jQuery('#rec-inv-end').val();
		var frequency = jQuery('#rec-inv-frequency').val();
		var status = jQuery('#rec-inv-status').val();
		var contact = jQuery('#client_contact_select').val();
		var spinner = jQuery('#cqpim_overlay');
		var items = jQuery('input[name^="ngroup-a"]').map(function(){return jQuery(this).val();}).get();
		if(jQuery('#rec-inv-auto').is(':checked')) {
			auto = 1;
		} else {
			auto = 0
		}
		if(jQuery('#rec-inv-partial').is(':checked')) {
			partial = 1;
		} else {
			partial = 0
		}
		var data = {
			'action' : 'pto_add_new_recurring_invoice',
			'client_id' : client_id,
			'title' : title,
			'start' : start,
			'end' : end,
			'frequency' : frequency,
			'status' : status,
			'contact' : contact,
			'auto' : auto,
			'items' : items,
			'partial' : partial,
			'pto_nonce' : localisation.global_nonce
		};
		if(title && frequency) {
			jQuery.ajax({
				url: ajaxurl,
				data: data,
				type: 'POST',
				dataType: 'json',
				beforeSend: function(){
					spinner.show();
					jQuery('#add_rec_inv').prop('disabled', true);
					jQuery.colorbox.resize();
				},
			}).always(function(response){
				console.log(response);
			}).done(function(response){
				if(response.error == true) {
					spinner.hide();
					jQuery('#add_rec_inv').prop('disabled', false);
					jQuery('.rec-inv-messages').html(response.message);
					jQuery.colorbox.resize();
				} else {
					spinner.hide();
					jQuery('#add_rec_inv').prop('disabled', false);
					jQuery('.rec-inv-messages').html(response.message);
					jQuery.colorbox.resize();
					location.reload();
				}
			});
		} else {
			jQuery('.rec-inv-messages').html('<div class="cqpim-alert cqpim-alert-danger alert-display">You must enter a title and a frequency.</div>');
			jQuery.colorbox.resize();
		}
	});
	// Delete Rec Invoice
	jQuery('.delete_task').click(function(e) {
		e.preventDefault();
		var client_id = jQuery('#post_ID').val();
		var key = jQuery(this).val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_delete_recurring_invoice',
			'client_id' : client_id,
			'key' : key,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('.delete_task').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
			} else {
				location.reload();
			}
		});
	});
	// Open Edit Rec Inv Colorbox
	jQuery('.edit-task').click(function(e) {
		e.preventDefault();
		var key = jQuery(this).val();
		jQuery.colorbox({
			'maxWidth':'95%',
			'inline': true,
			'fixed': true,
			'href': '#edit-recurring-invoice-' + key,	
			'opacity': '0.5',
		});	
	});	
	// Edit Rec Inv
	jQuery('.edit-rec-inv-btn').click(function(e) {
		e.preventDefault();
		var key = jQuery(this).data('key');
		var client_id = jQuery(this).val();
		var title = jQuery('#rec-inv-title-' + key).val();
		var start = jQuery('#rec-inv-start-' + key).val();
		var end = jQuery('#rec-inv-end-' + key).val();
		var frequency = jQuery('#rec-inv-frequency-' + key).val();
		var status = jQuery('#rec-inv-status-' + key).val();
		var contact = jQuery('#client_contact_select_' + key).val();
		var spinner = jQuery('#cqpim_overlay');
		var items = jQuery('input[name^="group' + key + '-a"]').map(function(){return jQuery(this).val();}).get();
		if(jQuery('#rec-inv-auto-' + key).is(':checked')) {
			auto = 1;
		} else {
			auto = 0
		}
		if(jQuery('#rec-inv-partial-' + key).is(':checked')) {
			partial = 1;
		} else {
			partial = 0
		}
		var data = {
			'action' : 'pto_edit_recurring_invoice',
			'key' : key,
			'client_id' : client_id,
			'title' : title,
			'start' : start,
			'end' : end,
			'frequency' : frequency,
			'status' : status,
			'contact' : contact,
			'auto' : auto,
			'items' : items,
			'partial' : partial,
			'pto_nonce' : localisation.global_nonce
		};
		if(title && frequency) {
			jQuery.ajax({
				url: ajaxurl,
				data: data,
				type: 'POST',
				dataType: 'json',
				beforeSend: function(){
					spinner.show();
					jQuery('.edit-rec-inv-btn').prop('disabled', true);
					jQuery.colorbox.resize();
				},
			}).done(function(response){
				if(response.error == true) {
					spinner.hide();
					jQuery('.edit-rec-inv-btn').prop('disabled', false);
					jQuery('.edit-inv-messages').html(response.message);
					jQuery.colorbox.resize();
				} else {
					spinner.hide();
					jQuery('.edit-rec-inv-btn').prop('disabled', false);
					jQuery('.edit-inv-messages').html(response.message);
					jQuery.colorbox.resize();
					location.reload();
				}
			});
		} else {
			jQuery('.rec-inv-messages').html('<div class="cqpim-alert cqpim-alert-danger alert-display">You must enter a title and a frequency.</div>');
			jQuery.colorbox.resize();
		}
	});
	// Remove Team Member
	jQuery('.delete_team').click(function(e) {
		e.preventDefault();
		var key = jQuery(this).val();
		var project_id = jQuery('#post_ID').val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_remove_client_contact',
			'key' : key,
			'project_id' : project_id,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('.delete_team').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('.delete_team').prop('disabled', false);
			} else {
				jQuery('.delete_team').prop('disabled', false);
				location.reload();
			}
		});
	});
	jQuery('.contact_edit_submit').click(function(e) {
		e.preventDefault();
		var key = jQuery(this).val();
		var project_id = jQuery('#post_ID').val();
		var admin = true;
		var name = jQuery('#contact_name_' + key).val();
		var phone = jQuery('#contact_telephone_' + key).val();
		var email = jQuery('#contact_email_' + key).val();
		var password = jQuery('#new_password_' + key).val();
		var password2 = jQuery('#confirm_password_' + key).val();
		var spinner = jQuery('#cqpim_overlay');
		var messages = jQuery('#client_team_messages_' + key);
		if(jQuery('#send_new_password_' + key).is(':checked')) {
			send = 1;
		} else {
			send = 0
		}
		var no_tasks = 0;
		var no_tasks_comment = 0;
		var no_tickets = 0;
		var no_tickets_comment = 0;
		var no_bugs = 0;
		var no_bugs_comment = 0;
		if(jQuery('#no_tasks_' + key).is(":checked")) {
			no_tasks = 1;
		}
		if(jQuery('#no_tasks_comment_' + key).is(":checked")) {
			no_tasks_comment = 1;
		}
		if(jQuery('#no_tickets_' + key).is(":checked")) {
			no_tickets = 1;
		}
		if(jQuery('#no_tickets_comment_' + key).is(":checked")) {
			no_tickets_comment = 1;
		}
		if(jQuery('#no_bugs_' + key).is(":checked")) {
			no_bugs = 1;
		}
		if(jQuery('#no_bugs_comment_' + key).is(":checked")) {
			no_bugs_comment = 1;
		}
		var data = {
			'action' : 'pto_edit_client_contact',
			'key' : key,
			'project_id' : project_id,
			'name' : name,
			'email' : email,
			'phone' : phone,
			'password' : password,
			'password2' : password2,
			'send' : send,
			'admin' : admin,
			'no_tasks' : no_tasks,
			'no_tasks_comment' : no_tasks_comment,
			'no_tickets' : no_tickets,
			'no_tickets_comment' : no_tickets_comment,
			'no_bugs' : no_bugs,
			'no_bugs_comment' : no_bugs_comment,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('.delete_team').prop('disabled', true);
				jQuery.colorbox.resize();
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('.contact_edit_submit').prop('disabled', false);
				jQuery(messages).html(response.message);
				jQuery.colorbox.resize();
			} else {
				spinner.hide();
				jQuery('.contact_edit_submit').prop('disabled', false);
				jQuery(messages).html(response.message);
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
	jQuery('.faq_order').on('change', function(e) {
		e.preventDefault();
		var post = jQuery(this).data('id');
		var order = jQuery(this).val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'cqpim_update_faq_order',
			'post' : post,
			'order' : order,
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
		});
	});
	jQuery('#client_files').on('change', '.fe_file', function(e) {
		e.preventDefault();
		var post = jQuery(this).data('client');
		var file = jQuery(this).data('file');
		var fe = 0;
		if(jQuery(this).is(":checked")) {
			fe = 1;
		}
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_manage_client_fe_files',
			'post' : post,
			'file' : file,
			'fe' : fe,
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
		});
	});

	/** Remove autofill dummy input */
	jQuery(window).on('load',function () {
		var autofill_input = jQuery('input[name="dummy_input"]');
		if( autofill_input.length ) {
			autofill_input.remove();
		}
	});
});