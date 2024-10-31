jQuery(document).ready(function() {
	setTimeout(
	  function() {
		jQuery('.fadeout').fadeOut("slow");
	  }, 2000);
	jQuery('#send-message').on('click', function(e) {
		e.preventDefault();
		jQuery('#cqpim-new-message').toggle();		
		jQuery('#cqpim-reply-message').hide();
	});
	jQuery('#cqpim-convo-reply').on('click', function(e) {
		e.preventDefault();
		jQuery('#cqpim-reply-message').toggle();
		jQuery('#cqpim-new-message').hide();
	});
	jQuery('#cancel').on('click', function(e) {
		e.preventDefault();
		jQuery('#cqpim-new-message').toggle();
	});
	jQuery('#cancel-reply').on('click', function(e) {
		e.preventDefault();
		jQuery('#cqpim-reply-message').toggle();		
	});
	jQuery('#cqpim-edit-subject').on('click', function(e) {
		e.preventDefault();
		jQuery('#cqpim-messaging-edit-subject').toggle();
	});
	jQuery('#cqpim-cancel-edit-subject').on('click', function(e) {
		e.preventDefault();
		jQuery('#cqpim-messaging-edit-subject').toggle();		
	});
	jQuery('#send').on('click', function(e) {
		e.preventDefault();
		var element = jQuery(this);
		var recipients = jQuery('#to').val();
		var subject = jQuery('#subject').val();
		var message = jQuery('#cmessage').val();
		var attachments = jQuery('#upload_attachment_ids').val();
		var responsebox = jQuery('#message-ajax-response');
		var data = {
			'action' : 'pto_create_conversation',
			'recipients' : recipients,
			'subject' : subject,
			'message' : message,
			'attachments' : attachments,
			'pto_nonce' : localisation.global_nonce
		}
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				jQuery('#cqpim_overlay').show();
				jQuery(element).prop('disabled', true);
				jQuery(responsebox).html('');
			},
		}).always(function(response){
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				jQuery('#cqpim_overlay').hide();
				jQuery(element).prop('disabled', false);
				alert(response.message);
			} else {
				window.location.replace(response.redirect);
			}
		});
	});
	jQuery('#send-reply').on('click', function(e) {
		e.preventDefault();
		var element = jQuery(this);
		var conversation = jQuery(element).data('conversation');
		var message = jQuery('#rmessage').val();
		var attachments = jQuery('#rupload_attachment_ids').val();
		var responsebox = jQuery('#rmessage-ajax-response');
		var data = {
			'action' : 'pto_create_conversation_reply',
			'conversation' : conversation,
			'message' : message,
			'attachments' : attachments,
			'pto_nonce' : localisation.global_nonce
		}
		console.log(data);
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				jQuery('#cqpim_overlay').show();
				jQuery(element).prop('disabled', true);
				jQuery(responsebox).html('');
			},
		}).always(function(response){
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				jQuery('#cqpim_overlay').hide();
				jQuery(element).prop('disabled', false);
				alert(response.message);
			} else {
				window.location.reload();
			}
		});
	});
	jQuery('#cqpim-convo-delete').on('click', function(e) {
		e.preventDefault();
		jQuery('#delete-confirm').dialog({
			resizable: false,
			height: "auto",
			width: 400,
			modal: true,
			buttons: [{
				text: localisation.messaging.dialogs.deleteconv,
				click: function() {
					jQuery( this ).dialog( "close" );
					var element = jQuery('#cqpim-convo-delete');
					var conversation = jQuery('#jq-conv-id').val();
					var data = {
						'action' : 'pto_delete_conversation',
						'conversation' : conversation,
						'pto_nonce' : localisation.global_nonce
					}
					jQuery.ajax({
						url: ajaxurl,
						data: data,
						type: 'POST',
						dataType: 'json',
						beforeSend: function(){
							jQuery('#cqpim_overlay').show();
							jQuery(element).prop('disabled', true);
						},
					}).always(function(response){
						console.log(response);
					}).done(function(response){
						if(response.error == true) {
							jQuery('#cqpim_overlay').hide();
							jQuery(element).prop('disabled', false);
							alert(response.message);
						} else {
							window.location.replace(response.redirect);
						}
					});
				}			
			},{
				text: localisation.messaging.dialogs.cancel,
				click: function() {
					jQuery( this ).dialog( "close" );
				}			
			}]
		});
	});
	jQuery('#cqpim-save-subject').on('click', function(e) {
		e.preventDefault();
		var element = jQuery(this);
		var conversation = jQuery('#jq-conv-id').val();
		var title = jQuery('#cqpim-title-editable-field').val();
		var data = {
			'action' : 'pto_edit_conversation_title',
			'conversation' : conversation,
			'title' : title,
			'pto_nonce' : localisation.global_nonce
		}
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				jQuery('#cqpim_overlay').show();
				jQuery(element).prop('disabled', true);
			},
		}).always(function(response){
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				jQuery('#cqpim_overlay').hide();
				jQuery(element).prop('disabled', false);
				alert(response.message);
			} else {
				window.location.reload();
			}
		});
	});
	jQuery('#cqpim-convo-leave').on('click', function(e) {
		e.preventDefault();
		jQuery('#leave-confirm').dialog({
			resizable: false,
			height: "auto",
			width: 400,
			modal: true,
			buttons: [{
				text: localisation.messaging.dialogs.leaveconv,
				click: function() {
					jQuery( this ).dialog( "close" );
					var element = jQuery('#cqpim-convo-leave');
					var conversation = jQuery('#jq-conv-id').val();
					var user = jQuery('#jq-user-id').val();
					var data = {
						'action' : 'pto_remove_conversation_user',
						'conversation' : conversation,
						'user' : user,
						'type' : 'leave',
						'pto_nonce' : localisation.global_nonce
					}
					jQuery.ajax({
						url: ajaxurl,
						data: data,
						type: 'POST',
						dataType: 'json',
						beforeSend: function(){
							jQuery('#cqpim_overlay').show();
							jQuery(element).prop('disabled', true);
						},
					}).always(function(response){
						console.log(response);
					}).done(function(response){
						if(response.error == true) {
							jQuery('#cqpim_overlay').hide();
							jQuery(element).prop('disabled', false);
							alert(response.message);
						} else {
							window.location.replace(response.redirect);
						}
					});
				}			
			},{
				text: localisation.messaging.dialogs.cancel,
				click: function() {
					jQuery( this ).dialog( "close" );
				}			
			}]
		});
	});
	jQuery('#cqpim-convo-remove').on('click', function(e) {
		e.preventDefault();
		jQuery('#remove-confirm').dialog({
			resizable: false,
			height: "auto",
			width: 400,
			modal: true,
			buttons: [{
				text: localisation.messaging.dialogs.removeconv,
				click: function() {
					jQuery( this ).dialog( "close" );
					var element = jQuery('#cqpim-convo-remove');
					var conversation = jQuery('#jq-conv-id').val();
					var user = jQuery('#cqpim-remove-user').val();
					var data = {
						'action' : 'pto_remove_conversation_user',
						'conversation' : conversation,
						'user' : user,
						'type' : 'remove',
						'pto_nonce' : localisation.global_nonce
					}
					jQuery.ajax({
						url: ajaxurl,
						data: data,
						type: 'POST',
						dataType: 'json',
						beforeSend: function(){
							jQuery('#cqpim_overlay').show();
							jQuery(element).prop('disabled', true);
						},
					}).always(function(response){
						console.log(response);
					}).done(function(response){
						if(response.error == true) {
							jQuery('#cqpim_overlay').hide();
							jQuery(element).prop('disabled', false);
							alert(response.message);
						} else {
							window.location.replace(response.redirect);
						}
					});
				}			
			},{
				text: localisation.messaging.dialogs.cancel,
				click: function() {
					jQuery( this ).dialog( "close" );
				}			
			}]
		});
	});
	jQuery('#cqpim-convo-add').on('click', function(e) {
		e.preventDefault();
		jQuery('#add-confirm').dialog({
			resizable: false,
			height: "auto",
			width: 500,
			modal: true,
			buttons: [{
				text: localisation.messaging.dialogs.addconv,
				click: function() {
					jQuery( this ).dialog( "close" );
					var element = jQuery('#cqpim-convo-add');
					var conversation = jQuery('#jq-conv-id').val();
					var recipients = jQuery('#ato').val();
					var data = {
						'action' : 'pto_add_conversation_user',
						'conversation' : conversation,
						'recipients' : recipients,
						'pto_nonce' : localisation.global_nonce
					}
					jQuery.ajax({
						url: ajaxurl,
						data: data,
						type: 'POST',
						dataType: 'json',
						beforeSend: function(){
							jQuery('#cqpim_overlay').show();
							jQuery(element).prop('disabled', true);
						},
					}).always(function(response){
						console.log(response);
					}).done(function(response){
						if(response.error == true) {
							jQuery('#cqpim_overlay').hide();
							jQuery(element).prop('disabled', false);
							alert(response.message);
						} else {
							window.location.replace(response.redirect);
						}
					});
				}			
			},{
				text: localisation.messaging.dialogs.cancel,
				click: function() {
					jQuery( this ).dialog( "close" );
				}			
			}]
		});
	});
});