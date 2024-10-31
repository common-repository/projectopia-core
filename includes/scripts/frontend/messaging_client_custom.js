jQuery(document).ready(function() {
	setTimeout(
	  function() {
		jQuery('.malert').fadeOut("slow");
	  }, 2000);  
	jQuery('.messages-table').dataTable({
		"order": [[ 4, 'desc' ]],
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
	jQuery('#send-message').on('click', function(e) {
		e.preventDefault();
		jQuery('#cqpim-new-message').toggle();
	});
	jQuery('#cqpim-convo-reply').on('click', function(e) {
		e.preventDefault();
		jQuery('#cqpim-reply-message').toggle();
	});
	jQuery('#cancel').on('click', function(e) {
		e.preventDefault();
		jQuery('#cqpim-new-message').toggle();
	});
	jQuery('#cancel-reply').on('click', function(e) {
		e.preventDefault();
		jQuery('#cqpim-reply-message').toggle();		
	});
	jQuery('#send').on('click', function(e) {
		e.preventDefault();
		var element = jQuery(this);
		var recipients = jQuery('#to').val();
		var subject = jQuery('#subject').val();
		var message = jQuery('#message').val();
		var attachments = jQuery('#upload_attachment_ids').val();
		var responsebox = jQuery('#message-ajax-response');
		var data = {
			'action' : 'pto_create_conversation',
			'recipients' : recipients,
			'subject' : subject,
			'message' : message,
			'attachments' : attachments,
			'client' : 1,
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
				jQuery(element).prop('disabled', true);
				jQuery(responsebox).html('');
			},
		}).always(function(response){
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				jQuery('#overlay').hide();
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
			'client' : 1,
			'pto_nonce' : localisation.global_nonce
		}
		console.log(data);
		jQuery.ajax({
			url: localisation.ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				// show spinner
				jQuery('#overlay').show();
				jQuery(element).prop('disabled', true);
				jQuery(responsebox).html('');
			},
		}).always(function(response){
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				jQuery('#overlay').hide();
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
						'client' : 1,
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
							jQuery(element).prop('disabled', true);
						},
					}).always(function(response){
						console.log(response);
					}).done(function(response){
						if(response.error == true) {
							jQuery('#overlay').hide();
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
						'client' : 1,
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
							jQuery(element).prop('disabled', true);
						},
					}).always(function(response){
						console.log(response);
					}).done(function(response){
						if(response.error == true) {
							jQuery('#overlay').hide();
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