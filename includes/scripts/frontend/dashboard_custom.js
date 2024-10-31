jQuery(document).ready(function() {
	jQuery(window).scroll(function (event) {
		var sc = jQuery(window).scrollTop();
        var top = jQuery('#cqpim_admin_head').height() + jQuery('#cqpim-dash-sidebar').height();
        if (sc < top) {
            var top_pos = top - sc;
            jQuery('#cqpim-dash-sidebar-back').css("top", top_pos);
        }else{
            jQuery('#cqpim-dash-sidebar-back').css("top", "0px");
		}
		
		//patch
		if(jQuery('.menu-open').is(":visible")){
			jQuery('#cqpim-dash-sidebar').hide();
		}else{
			jQuery('#cqpim-dash-sidebar').show();
		}

		

        
	});
	jQuery(window).resize(function(e) {
		if (jQuery(window).width() > 918) {
		   jQuery('#cqpim-dash-sidebar').show();
		}	
	});
	if ( jQuery.isFunction(jQuery.fn.tooltip) ) {
	jQuery('.cqpim_tooltip').tooltip();
	}
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
	jQuery('.datepicker').datepicker({ 
		showButtonPanel: true,
		closeText: localisation.calendar.closeText,
		currentText: localisation.calendar.currentText,
		monthNames: localisation.calendar.monthNames,
		monthNamesShort: localisation.calendar.monthNamesShort,
		dayNames: localisation.calendar.dayNames,
		dayNamesShort: localisation.calendar.dayNamesShort,
		dayNamesMin: localisation.calendar.dayNamesMin,
		dateFormat: localisation.calendar.dateFormat,
		firstDay: localisation.calendar.firstDay,
	});
	jQuery('.masonry-grid').masonry({
		columnWidth: '.grid-sizer',
		itemSelector: '.grid-item',
		percentPosition: true
	});
	if(jQuery('body').hasClass('logged-in')) {
		jQuery('#payment-amount').show();
		jQuery('#stripe-pay').show();
		jQuery('#pp-pay').show();
	}
	jQuery('#save_amount').on('click', function(e) {
		e.preventDefault;
		var amount = jQuery('#amount_to_pay').val();
		jQuery('#overlay').show();
		window.location.href = window.location.pathname+"?"+jQuery.param({'atp':amount})
	});
	jQuery('#cqpim_pay_now').on('click', function(e) {
		e.preventDefault();
		jQuery.colorbox({
			'inline': true,
			'href': '#cqpim_payment_methods',	
			'opacity': '0.5',
			'width': '600px',
			'initialHeight': '600px'
		});	
		jQuery.colorbox.resize();
	});
	jQuery('.dataTable').dataTable({
		"language": {
			"processing":     localisation.datatables.sProcessing,
			"search":         localisation.datatables.sSearch,
			"lengthMenu":     localisation.datatables.sLengthMenu,
			"info":           localisation.datatables.sInfo,
			"infoEmpty":      localisation.datatables.sInfoEmpty,
			"infoFiltered":   localisation.datatables.sInfoFiltered,
			"infoPostFix":    localisation.datatables.sInfoPostFix,
			"loadingRecords": localisation.datatables.sLoadingRecords,
			"zeroRecords":    localisation.datatables.sZeroRecords,
			"emptyTable":     localisation.datatables.sEmptyTable,
			"paginate": {
				"first":      localisation.datatables.sFirst,
				"previous":   localisation.datatables.sPrevious,
				"next":       localisation.datatables.sNext,
				"last":       localisation.datatables.sLast
			},
			"aria": {
				"sortAscending":  localisation.datatables.sSortAscending,
				"sortDescending": localisation.datatables.sSortDescending
			}
		},
		"stateSave": true
	});
	jQuery('.dataTable-CST').dataTable({
		"order": [[ 4, 'desc' ]],
		"stateSave": true,
		"language": {
			"processing":     localisation.datatables.sProcessing,
			"search":         localisation.datatables.sSearch,
			"lengthMenu":     localisation.datatables.sLengthMenu,
			"info":           localisation.datatables.sInfo,
			"infoEmpty":      localisation.datatables.sInfoEmpty,
			"infoFiltered":   localisation.datatables.sInfoFiltered,
			"infoPostFix":    localisation.datatables.sInfoPostFix,
			"loadingRecords": localisation.datatables.sLoadingRecords,
			"zeroRecords":    localisation.datatables.sZeroRecords,
			"emptyTable":     localisation.datatables.sEmptyTable,
			"paginate": {
				"first":      localisation.datatables.sFirst,
				"previous":   localisation.datatables.sPrevious,
				"next":       localisation.datatables.sNext,
				"last":       localisation.datatables.sLast
			},
			"aria": {
				"sortAscending":  localisation.datatables.sSortAscending,
				"sortDescending": localisation.datatables.sSortDescending
			}
		}
	});
	jQuery('.dataTable-CI').dataTable({
		"order": [[ 0, 'desc' ]],
		"stateSave": true,
		"language": {
			"processing":     localisation.datatables.sProcessing,
			"search":         localisation.datatables.sSearch,
			"lengthMenu":     localisation.datatables.sLengthMenu,
			"info":           localisation.datatables.sInfo,
			"infoEmpty":      localisation.datatables.sInfoEmpty,
			"infoFiltered":   localisation.datatables.sInfoFiltered,
			"infoPostFix":    localisation.datatables.sInfoPostFix,
			"loadingRecords": localisation.datatables.sLoadingRecords,
			"zeroRecords":    localisation.datatables.sZeroRecords,
			"emptyTable":     localisation.datatables.sEmptyTable,
			"paginate": {
				"first":      localisation.datatables.sFirst,
				"previous":   localisation.datatables.sPrevious,
				"next":       localisation.datatables.sNext,
				"last":       localisation.datatables.sLast
			},
			"aria": {
				"sortAscending":  localisation.datatables.sSortAscending,
				"sortDescending": localisation.datatables.sSortDescending
			}
		}
	});
	jQuery('.dataTable-CQ').dataTable({
		"order": [[ 1, 'asc' ]],
		"stateSave": true,
		"language": {
			"processing":     localisation.datatables.sProcessing,
			"search":         localisation.datatables.sSearch,
			"lengthMenu":     localisation.datatables.sLengthMenu,
			"info":           localisation.datatables.sInfo,
			"infoEmpty":      localisation.datatables.sInfoEmpty,
			"infoFiltered":   localisation.datatables.sInfoFiltered,
			"infoPostFix":    localisation.datatables.sInfoPostFix,
			"loadingRecords": localisation.datatables.sLoadingRecords,
			"zeroRecords":    localisation.datatables.sZeroRecords,
			"emptyTable":     localisation.datatables.sEmptyTable,
			"paginate": {
				"first":      localisation.datatables.sFirst,
				"previous":   localisation.datatables.sPrevious,
				"next":       localisation.datatables.sNext,
				"last":       localisation.datatables.sLast
			},
			"aria": {
				"sortAscending":  localisation.datatables.sSortAscending,
				"sortDescending": localisation.datatables.sSortDescending
			}
		}
	});
	jQuery('.dataTable-CP').dataTable({
		"order": [[ 4, 'asc' ]],
		"stateSave": true,
		"language": {
			"processing":     localisation.datatables.sProcessing,
			"search":         localisation.datatables.sSearch,
			"lengthMenu":     localisation.datatables.sLengthMenu,
			"info":           localisation.datatables.sInfo,
			"infoEmpty":      localisation.datatables.sInfoEmpty,
			"infoFiltered":   localisation.datatables.sInfoFiltered,
			"infoPostFix":    localisation.datatables.sInfoPostFix,
			"loadingRecords": localisation.datatables.sLoadingRecords,
			"zeroRecords":    localisation.datatables.sZeroRecords,
			"emptyTable":     localisation.datatables.sEmptyTable,
			"paginate": {
				"first":      localisation.datatables.sFirst,
				"previous":   localisation.datatables.sPrevious,
				"next":       localisation.datatables.sNext,
				"last":       localisation.datatables.sLast
			},
			"aria": {
				"sortAscending":  localisation.datatables.sSortAscending,
				"sortDescending": localisation.datatables.sSortDescending
			}
		}
	});
	jQuery('#switch_to_resolved').click(function(e) {
		e.preventDefault();
		var spinner = jQuery('#overlay');
		var data = {
			'action' : 'pto_switch_resolved_tickets',
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: localisation.ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				// show spinner
				spinner.show();
				// disable form elements while awaiting data
				jQuery('#switch_to_resolved').prop('disabled', true);
			},
		}).always(function(response){
			console.log(response);
		}).done(function(response){
			spinner.hide();
			console.log(response);
			jQuery('#switch_to_resolved').prop('disabled', false);
			location.reload();
		});	
	});
	jQuery('#support-submit').click(function(e) {
		e.preventDefault();
		var form_data = {};
		form_data['ticket_title'] = jQuery('#ticket_title').val();
		form_data['ticket_priority_new'] = jQuery('#ticket_priority_new').val();
		form_data['ticket_files'] = jQuery('#upload_attachment_ids').val();
		form_data['ticket_update_new'] = jQuery('#ticket_update_new').val();
		form_data['ticket_item'] = jQuery('#ticket_item').val();	
		form_data['reject_reason'] = jQuery('#reject_reason').val();
		form_data['custom'] = {};
		i = 0;
		jQuery('.cqpim-custom').each(function() {
			if(jQuery(this).attr('type') == 'radio') {
				if (jQuery(this).is(':checked')) {
					form_data['custom'][jQuery(this).attr('name')] = jQuery(this).val();	
				}
			} else if (jQuery(this).attr('type') == 'checkbox') {
				if (!form_data['custom'][jQuery(this).attr('name')]) {
					form_data['custom'][jQuery(this).attr('name')] = {};
				}
				if (jQuery(this).is(':checked')) {
					form_data['custom'][jQuery(this).attr('name')][i] = jQuery(this).val();
				}
			} else {
				form_data['custom'][jQuery(this).attr('name')] = jQuery(this).val();
			}
			i = i + 1;
		});
		var data = {
			'action' : 'pto_client_raise_support_ticket',
			'data' : form_data,
			'pto_nonce' : localisation.global_nonce
		}
		jQuery.ajax({
			url: localisation.ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				// show spinner
				jQuery('#overlay').show();
				// disable form elements while awaiting data
				jQuery('#client_add_support').prop('disabled', true);
			},
		}).always(function(response) {
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				jQuery('#overlay').hide();
				jQuery('#client_add_support').prop('disabled', false);
				alert(response.message);
				//jQuery.colorbox.resize();
			} else {
				//jQuery('#overlay').hide();
				jQuery('#client_add_support').prop('disabled', false);
				//jQuery(messages).html(response.message);
				window.location.replace(response.message);				
			}
		});
	});
	jQuery('#client_settings').submit(function(e) {
		e.preventDefault();
		var spinner = jQuery('#overlay');
		var messages = jQuery('#settings_messages');
		var user_id = jQuery('#client_user_id').val();
		var client_object = jQuery('#client_object').val();
		var client_type = jQuery('#client_type').val();
		var client_email = jQuery('#client_email').val();
		var client_phone = jQuery('#client_phone').val();
		var client_name = jQuery('#client_name').val();
		var company_name = jQuery('#company_name').val();
		var company_address = jQuery('#company_address').val();
		var company_postcode = jQuery('#company_postcode').val();
		var client_pass = jQuery('#client_pass').val();
		var client_pass_rep = jQuery('#client_pass_rep').val();
		var photo = jQuery('#upload_attachment_ids').val();
		var no_tasks = 0;
		var no_tasks_comment = 0;
		var no_tickets = 0;
		var no_tickets_comment = 0;
		var no_bugs = 0;
		var no_bugs_comment = 0;
		if(jQuery('#no_tasks').is(":checked")) {
			no_tasks = 1;
		}
		if(jQuery('#no_tasks_comment').is(":checked")) {
			no_tasks_comment = 1;
		}
		if(jQuery('#no_tickets').is(":checked")) {
			no_tickets = 1;
		}
		if(jQuery('#no_tickets_comment').is(":checked")) {
			no_tickets_comment = 1;
		}
		if(jQuery('#no_bugs').is(":checked")) {
			no_bugs = 1;
		}
		if(jQuery('#no_bugs_comment').is(":checked")) {
			no_bugs_comment = 1;
		}
		var custom = {};
		i = 0;
		jQuery('.cqpim-custom').each(function() {
			if(jQuery(this).attr('type') == 'radio') {
				if (jQuery(this).is(':checked')) {
					custom[jQuery(this).attr('name')] = jQuery(this).val();	
				}
			} else if (jQuery(this).attr('type') == 'checkbox') {
				if (!custom[jQuery(this).attr('name')]) {
					custom[jQuery(this).attr('name')] = {};
				}
				if (jQuery(this).is(':checked')) {
					custom[jQuery(this).attr('name')][i] = jQuery(this).val();
				}
			} else {
				custom[jQuery(this).attr('name')] = jQuery(this).val();
			}
			i = i + 1;
		});
		var data = {
			'action' : 'pto_client_update_details',
			'user_id' : user_id,
			'client_object' : client_object,
			'client_type' : client_type,
			'client_email' : client_email,
			'client_phone' : client_phone,
			'client_name' : client_name,
			'company_name' : company_name,
			'company_address' : company_address,
			'company_postcode' : company_postcode,
			'client_pass' : client_pass,
			'client_pass_rep' : client_pass_rep,
			'photo' : photo,
			'no_tasks' : no_tasks,
			'no_tasks_comment' : no_tasks_comment,
			'no_tickets' : no_tickets,
			'no_tickets_comment' : no_tickets_comment,
			'no_bugs' : no_bugs,
			'no_bugs_comment' : no_bugs_comment,
			'custom' : custom,
			'pto_nonce' : localisation.global_nonce
		}
		jQuery.ajax({
			url: localisation.ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				// show spinner
				spinner.show();
				// disable form elements while awaiting data
				jQuery('#client_settings_submit').prop('disabled', true);
			},
		}).always(function(response) {
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#client_settings_submit').prop('disabled', false);
				jQuery(messages).html(response.message);
				jQuery.colorbox.resize();
			} else {
				spinner.hide();
				jQuery('#client_settings_submit').prop('disabled', false);
				jQuery(messages).html(response.message);
				//location.reload();				
			}
		});
	});
	// Contacts
	jQuery('button.cancel-colorbox').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox.close();
	});
	jQuery('#add_client_team').click(function(e) {
		e.preventDefault();
		jQuery.colorbox({
			'inline': true,
			'fixed': true,
			'href': '#add_client_team_ajax',	
			'opacity': '0.5',
		});	
	});
	jQuery('.edit-milestone').click(function(e) {
		e.preventDefault();
		var key = jQuery(this).val();
		jQuery.colorbox({
			'inline': true,
			'fixed': true,
			'href': '#contact_edit_' + key,	
			'opacity': '0.5',
		});	
	});
	jQuery('#add_client_team_submit').click(function(e) {
		e.preventDefault();
		var contact_name = jQuery('#contact_name').val();
		var contact_telephone = jQuery('#contact_telephone').val();
		var contact_email = jQuery('#contact_email').val();
		var entity_id = jQuery('#post_ID').val();
		var spinner = jQuery('#overlay');
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
			url: localisation.ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				// show spinner
				spinner.show();
				// disable form elements while awaiting data
				jQuery('#add_client_team_submit').prop('disabled', true);
				jQuery.colorbox.resize();
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				// re-enable form elements so that new enquiry can be posted
				jQuery('#add_client_team_submit').prop('disabled', false);
				jQuery('#client_team_messages').html(response.message);
				jQuery.colorbox.resize();
			} else {
				spinner.hide();
				// re-enable form elements so that new enquiry can be posted
				jQuery('#add_client_team_submit').prop('disabled', false);
				jQuery('#client_team_messages').html(response.message);
				jQuery.colorbox.resize();
				location.reload();
			}
		});
	});	
	// Remove Team Member
	jQuery('.delete_team').click(function(e) {
		e.preventDefault();
		var key = jQuery(this).val();
		var project_id = jQuery('#post_ID').val();
		var domain = document.domain;
		var spinner = jQuery('#overlay');
		var data = {
			'action' : 'pto_remove_client_contact',
			'key' : key,
			'project_id' : project_id,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: localisation.ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				// show spinner
				spinner.show();
				// disable form elements while awaiting data
				jQuery('.delete_team').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('.delete_team').prop('disabled', false);
			} else {
				spinner.hide();
				jQuery('.delete_team').prop('disabled', false);
				location.reload();
			}
		});
	});
	jQuery('.contact_edit_submit').click(function(e) {
		e.preventDefault();
		var key = jQuery(this).val();
		var project_id = jQuery('#post_ID').val();
		var name = jQuery('#contact_name_' + key).val();
		var phone = jQuery('#contact_telephone_' + key).val();
		var email = jQuery('#contact_email_' + key).val();
		var password = jQuery('#new_password_' + key).val();
		var password2 = jQuery('#confirm_password_' + key).val();
		var spinner = jQuery('#overlay');
		var messages = jQuery('#client_team_messages_' + key);
		if(jQuery('#send_new_password_' + key).is(':checked')) {
			send = 1;
		} else {
			send = 0
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
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: localisation.ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				// show spinner
				spinner.show();
				// disable form elements while awaiting data
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
	jQuery('.cqpim_alert_clear').click(function(e) {
		e.preventDefault();
		var element = jQuery(this).parent().parent();
		var client = jQuery(this).data('client');
		var alert_id = jQuery(this).data('alert');
		var spinner = jQuery('#overlay');
		var data = {
			'action' : 'pto_clear_client_alert',
			'client' : client,
			'alert' : alert_id,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: localisation.ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
			},
		}).always(function(response) {
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
			} else {
				spinner.hide();
				jQuery(element).slideUp();
				jQuery(element).remove();
			}
		});
	});	
	jQuery(document).on('input', '#username, #password', function() {
		jQuery('#login_messages').fadeOut(500);
	});
	// jQuery('#password').change(function() {
	// 	jQuery('#login_messages').fadeOut(500);
	// });
	jQuery(document).on('submit', '#cqpim-login', function(e) {
		e.preventDefault();
		var nonce = jQuery(document).find('#signonsecurity').val();
		var username = jQuery(document).find('#username').val();
		var password = jQuery(document).find('#password').val();

		/** Verify google recaptcha for frontend login form */
		var g_captacha_response = -1;
		if ( jQuery('.g-recaptcha').length  ) {
			g_captacha_response = grecaptcha.getResponse();
			if ( g_captacha_response.length == 0 ) {
				if ( ! jQuery('.g-recaptcha-error').length ) {
					jQuery('.g-recaptcha').before('<p class="g-recaptcha-error" style="color:red;margin:0;">Please verify this Google reCaptcha !</p>');
				}
				e.preventDefault();
				return false;
			}
		}

		var data = {
			'action' : 'pto_ajax_login',
			'nonce' : nonce,	
			'username' : username,
			'password' : password,
			'g_captacha_response' : g_captacha_response,
			'pto_nonce' : localisation.global_nonce
		}
		jQuery.ajax({
			url: localisation.ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				// show spinner
				jQuery('#overlay').show();
				// disable form elements while awaiting data
				jQuery('#log-in').prop('disabled', true);
			},
		}).always(function(response) {
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				jQuery('#overlay').hide();
				jQuery('#log-in').prop('disabled', false);
				jQuery('#login_messages').removeClass('success');
				jQuery('#login_messages').addClass('error');
				jQuery('#login_messages').html(response.message);
				jQuery('#login_messages').fadeIn(500);
				//jQuery.colorbox.resize();
			} else {
				jQuery('#overlay').hide();
				jQuery('#log-in').prop('disabled', false);
				jQuery('#login_messages').removeClass('error');
				jQuery('#login_messages').addClass('success');
				jQuery('#login_messages').html(response.message);
				jQuery('#login_messages').fadeIn(500);
				//jQuery(messages).html(response.message);
				window.location.replace(response.redirect);				
			}
		});
	});
	jQuery('#cqpim-reset-pass').submit(function(e) {
		e.preventDefault();
		var nonce = jQuery('#signonsecurity').val();
		var username = jQuery('#username').val();
		var data = {
			'action' : 'pto_ajax_reset',
			'nonce' : nonce,	
			'username' : username,
			'pto_nonce' : localisation.global_nonce
		}
		jQuery.ajax({
			url: localisation.ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				// show spinner
				jQuery('#overlay').show();
				// disable form elements while awaiting data
				jQuery('#log-in').prop('disabled', true);
			},
		}).always(function(response) {
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				jQuery('#overlay').hide();
				jQuery('#log-in').prop('disabled', false);
				jQuery('#login_messages').removeClass('success');
				jQuery('#login_messages').addClass('error');
				jQuery('#login_messages').html(response.message);
				jQuery('#login_messages').fadeIn(500);
				//jQuery.colorbox.resize();
			} else {
				jQuery('#overlay').hide();
				jQuery('#log-in').prop('disabled', false);
				jQuery('#login_messages').removeClass('error');
				jQuery('#login_messages').addClass('success');
				jQuery('#login_messages').html(response.message);
				jQuery('#login_messages').fadeIn(500);
				jQuery('#username').val('');
				//jQuery(messages).html(response.message);
				//window.location.replace(response.redirect);				
			}
		});
	});
	jQuery('#reset_pass_conf').submit(function(e) {
		e.preventDefault();
		var hash = jQuery('#hash').val();
		var pass = jQuery('#password').val();
		var pass2 = jQuery('#password2').val();
		var data = {
			'action' : 'pto_ajax_reset_conf',
			'hash' : hash,	
			'pass' : pass,
			'pass2' : pass2,
			'pto_nonce' : localisation.global_nonce
		}
		jQuery.ajax({
			url: localisation.ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				// show spinner
				jQuery('#overlay').show();
				// disable form elements while awaiting data
				jQuery('#log-in').prop('disabled', true);
			},
		}).always(function(response) {
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				jQuery('#overlay').hide();
				jQuery('#log-in').prop('disabled', false);
				jQuery('#login_messages').removeClass('success');
				jQuery('#login_messages').addClass('error');
				jQuery('#login_messages').html(response.message);
				jQuery('#login_messages').fadeIn(500);
				//jQuery.colorbox.resize();
			} else {
				jQuery('#overlay').hide();
				jQuery('#log-in').prop('disabled', false);
				jQuery('#login_messages').removeClass('error');
				jQuery('#login_messages').addClass('success');
				jQuery('#login_messages').html(response.message);
				jQuery('#login_messages').fadeIn(500);
				jQuery('#password').val('');
				jQuery('#password2').val('');
				//jQuery(messages).html(response.message);
				//window.location.replace(response.redirect);				
			}
		});
	});
	jQuery('#cqpim-register').submit(function(e) {
		e.preventDefault();
		var nonce = jQuery('#signonsecurity').val();
		var name = jQuery('#name').val();
		var username = jQuery('#username').val();
		var company = jQuery('#company').val();
		var password = jQuery('#password').val();
		var rpassword = jQuery('#rpassword').val();
		/** Verify google recaptcha for frontend register form */
		var g_captacha_response = -1;
		if ( jQuery('.g-recaptcha').length  ) {
			g_captacha_response = grecaptcha.getResponse();
			if ( g_captacha_response.length == 0 ) {
				if ( ! jQuery('.g-recaptcha-error').length ) {
					jQuery('.g-recaptcha').before('<p class="g-recaptcha-error" style="color:red;margin:0;">Please verify this Google reCaptcha !</p>');
				}
				e.preventDefault();
				return false;
			}
		}
		var data = {
			'action' : 'pto_ajax_register',
			'nonce' : nonce,	
			'username' : username,
			'name' : name,
			'company' : company,
			'password' : password,
			'rpassword' : rpassword,
			'g_captacha_response' : g_captacha_response,
			'pto_nonce' : localisation.global_nonce
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
				jQuery('#overlay').hide();
				jQuery('#login_messages').removeClass('success');
				jQuery('#login_messages').addClass('error');
				jQuery('#login_messages').html(response.message);
				jQuery('#login_messages').fadeIn(500);
			} else {
				jQuery('#overlay').hide();
				jQuery('#log-in').prop('disabled', false);
				jQuery('#login_messages').removeClass('error');
				jQuery('#login_messages').addClass('success');
				jQuery('#login_messages').html(response.message);
				jQuery('#login_messages').fadeIn(500);			
			}
		});
	});
	jQuery('.pto_remove_current_client_photo').on('click', function(e) {
		e.preventDefault();
		var team = jQuery('#team_id').val();
		var spinner = jQuery('#overlay');
		var data = {
			'action' : 'pto_remove_current_client_photo',
			'team' : team,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: localisation.ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('.pto_remove_current_client_photo').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('.pto_remove_current_client_photo').prop('disabled', false);
			} else {
				spinner.hide();
				jQuery('#pto_avatar_current_cont').hide();
				jQuery('#pto_avatar_current_cont').hide();
				jQuery('.pto_remove_current_client_photo').hide();
				location.reload();
			}
		});	
	});
	jQuery('.subscribe_to').on('click', function(e){
		e.preventDefault();
		var id = jQuery(this).data('id');
		jQuery.colorbox({			
			'width' : '500px',
			'maxWidth':'95%',
			'inline': true,
			'href': '#subscribe-plan-div-' + id,
			'opacity': '0.5',
		});	
		jQuery.colorbox.resize();
	});
	jQuery('.subscribe_confirm').on('click', function(e) {
		e.preventDefault();
		var id = jQuery(this).data('id');
		var spinner = jQuery('#overlay');
		var data = {
			'action' : 'pto_create_sub_from_cd',
			'id' : id
		};
		jQuery.ajax({
			url: localisation.ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('.subscribe_confirm').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('.subscribe_confirm').prop('disabled', false);
				alert(response.message);
			} else {
				spinner.hide();
				jQuery('.subscribe_confirm').prop('disabled', false);
				window.location.replace(response.redirect);
			}
		});	
	});
	jQuery('.cqpim_notifications').on('click', function(e) {
		e.preventDefault;
		jQuery('#cqpim_notifications').toggle();
	});
	jQuery('.nf_remove_button').on('click', function(e) {		
		e.preventDefault();
		var element = jQuery(this);
		var key = jQuery(this).data('key');
		var spinner = jQuery('#overlay');
		var data = {
			'action' : 'pto_notifications_client_remove_nf',
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
			},
		}).done(function(response){
			spinner.hide();
			jQuery(element).closest('li').slideUp("slow");	
		});	
	});
	jQuery('.notification_item').on('click', function(e) {		
		e.preventDefault();
		var element = jQuery(this);
		var item = jQuery(this).data('item');
		var key = jQuery(this).data('key');
		var type = jQuery(this).data('type');
		var spinner = jQuery('#overlay');
		var data = {
			'action' : 'pto_notifications_client_item',
			'key' : key,
			'item' : item,
			'type' : type,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: localisation.ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
			},
		}).done(function(response){
			spinner.hide();
			if(response.error == true) {
				alert(response.message);
			} else {
				var redirect = response.redirect.replace("&amp;", "&");
				window.location.replace(redirect);
			}
		});	
	});	
	jQuery('#clear_all_read_nf').on('click', function(e) {		
		e.preventDefault();
		var spinner = jQuery('#overlay');
		var data = {
			'action' : 'pto_clear_all_read_client_nf',
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: localisation.ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
			},
		}).done(function(response){
			spinner.hide();
			jQuery('#notifications_ul li').each(function() {
				if(!jQuery(this).hasClass('unread')) {
					jQuery(this).slideUp("slow");
				}				
			});
		});	
	});
	jQuery('#mark_all_read_nf').on('click', function(e) {		
		e.preventDefault();
		var spinner = jQuery('#overlay');
		var data = {
			'action' : 'pto_mark_all_read_client_nf',
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: localisation.ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
			},
		}).done(function(response){
			spinner.hide();
			jQuery('#notifications_ul li').each(function() {
				jQuery(this).removeClass("unread");
				jQuery('#nf_counter').remove();
			});
		});	
	});
	jQuery('#clear_all_nf').on('click', function(e) {		
		e.preventDefault();
		var spinner = jQuery('#overlay');
		var data = {
			'action' : 'pto_clear_all_client_nf',
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: localisation.ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
			},
		}).done(function(response){
			spinner.hide();
			jQuery('#notifications_ul').remove();
			jQuery('#nf_counter').remove();
			jQuery('#notification_list').html(response.html);
		});	
	});
	jQuery('#client_fe_files').on('submit', function(e) {		
		e.preventDefault();
		var client = jQuery('#client_id').val();
		var files = jQuery('#upload_attachment_ids').val();
		var spinner = jQuery('#overlay');
		var data = {
			'action' : 'pto_add_client_files',
			'client' : client,
			'files' : files,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: localisation.ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
			},
		}).done(function(response){
			if(response.error == 1) {
				spinner.hide();
				alert(localisation.uploads.client_up_fail);
			} else {
				spinner.hide();
				window.location.reload();
			}
		});	
	});
	jQuery('#project_fe_files').on('submit', function(e) {		
		e.preventDefault();
		var task = jQuery('#task_id').val();
		var files = jQuery('#upload_attachment_ids').val();
		var spinner = jQuery('#overlay');
		var data = {
			'action' : 'pto_add_task_files',
			'task' : task,
			'files' : files,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: localisation.ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
			},
		}).done(function(response){
			if(response.error == 1) {
				spinner.hide();
				alert(localisation.uploads.client_up_fail);
			} else {
				spinner.hide();
				window.location.reload();
			}
		});	
	});
});