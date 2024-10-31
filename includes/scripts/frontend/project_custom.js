jQuery(document).ready(function() {
	jQuery('#project_bugs_table').dataTable({
		"order": [[ 0, 'desc' ]],
		"language": {
			"processing":     localisation.datatables.sProcessing,
			"search":          localisation.datatables.sSearch,
			"lengthMenu":      localisation.datatables.sLengthMenu,
			"info":            localisation.datatables.sInfo,
			"infoEmpty":       localisation.datatables.sInfoEmpty,
			"infoFiltered":    localisation.datatables.sInfoFiltered,
			"infoPostFix":     localisation.datatables.sInfoPostFix,
			"loadingRecords":  localisation.datatables.sLoadingRecords,
			"zeroRecords":     localisation.datatables.sZeroRecords,
			"emptyTable":      localisation.datatables.sEmptyTable,
			"paginate": {
				"first":       localisation.datatables.sFirst,
				"previous":    localisation.datatables.sPrevious,
				"next":        localisation.datatables.sNext,
				"last":        localisation.datatables.sLast
			},
			"aria": {
				"sortAscending":   localisation.datatables.sSortAscending,
				"sortDescending":  localisation.datatables.sSortDescending
			}
		}	
	});
	jQuery('.masonry-grid').masonry({
		columnWidth: '.grid-sizer',
		itemSelector: '.grid-item',
		percentPosition: true
	});
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
	jQuery('.refresh').on('click', function(e) {
		e.preventDefault();
		location.reload();
	});
	jQuery("#uploaded_files").load(location.href + " #uploaded_files");
	// Make timeline column same height as status
	htm = jQuery('#htm').height();
	htr = jQuery('#hta .title').outerHeight();
	hta = htm - htr;
	jQuery('#hta .content').height(hta - 15);
	// Colorbox
	jQuery('.colorbox').colorbox({'inline':true, 'opacity': '0.5'});
	// Add Message 
	jQuery('a#add_message_trigger').on('click', function(e){
		e.preventDefault();
		var anc = jQuery(this).attr('id');
		var id = anc.replace('client_', '');
		var thediv = jQuery('#' + id);
		jQuery(thediv).parent('div').attr('id', id + '_container');
		jQuery.colorbox({
			'inline': true,
			'href': '#add_message',
			'opacity' : '0.5',
		});	
		jQuery.colorbox.resize();
	});
	// Client Accept contract
	jQuery('#accept_contract').click(function(e) {
		e.preventDefault();
		jQuery('#messages').html('');
		var project_id = jQuery('#project_id').val();
		var name = jQuery('#conf_name').val();
		var pm_name = jQuery('#pm_name').val();
		var spinner = jQuery('#overlay');
		var messages = jQuery('#messages');
		var domain = document.domain;
		var data = {
			'action' : 'pto_client_accept_contract',
			'project_id' : project_id,
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
					spinner.show();
					jQuery('#accept_contract').prop('disabled', true);
				},
			}).always(function(response){
				console.log(response);
			}).done(function(response){
				if(response.error == true) {
					spinner.hide();
					jQuery('#accept_contract').prop('disabled', false);
					jQuery('#messages').html(response.errors);
				} else {
					spinner.hide();
					jQuery('#accept_contract').prop('disabled', false);
					location.reload();
				}
			});
		}
	});
	// Send message AJAX
	jQuery('#add_message_ajax').click(function(e) {
		e.preventDefault();
		var visibility = jQuery('#add_message_visibility').val();
		var message = jQuery('#add_message_text').val();
		var project_id = jQuery('#post_ID').val();
		var who = jQuery('#message_who').val();
		var domain = document.domain;
		var spinner = jQuery('#overlay');
		var data = {
			'action' : 'pto_add_message_to_project',
			'visibility' : visibility,
			'message' : message,
			'project_id' : project_id,
			'who' : who,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: localisation.ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery.colorbox.resize();
				jQuery('#add_message_trigger').prop('disabled', true);
			},
		}).always(function(response){
			if(response.error == true) {
			spinner.hide();
				jQuery('#add_message_ajax').prop('disabled', false);
				jQuery('#message_messages').html(response.errors);
				jQuery.colorbox.resize();
			} else {
				spinner.hide();
				jQuery('#add_message_ajax').prop('disabled', false);
				jQuery('#message_messages').html(response.errors);
				jQuery.colorbox.resize();
				location.reload();
			}
		});
	});
	// Delete Message
	jQuery('button.delete_message').click(function(e) {
		e.preventDefault();
		var project_id = jQuery('#post_ID').val();
		var key = jQuery(this).data('id');
		var domain = document.domain;
		var spinner = jQuery('#overlay');
		var data = {
			'action' : 'pto_delete_project_message',
			'project_id' : project_id,
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
		}).done(function(){
				location.reload();
		});
	});
	jQuery('#add_bug_btn').on('click', function(e) {
		e.preventDefault();
		var project = jQuery(this).data('id');
		var title = jQuery('#bug_title').val();
		var desc = jQuery('#bug_desc').val();
		var assignee = jQuery('#bug_assignee').val();
		var status = jQuery('#bug_status').val();
		var files = jQuery('#upload_attachment_ids').val();
		var update = jQuery('#bug_update').val();
		var spinner = jQuery('#overlay');
		var new_bug = 1;
		var messages = jQuery('#password_messages');
		var data = {
			'action' : 'pto_update_bug',
			'project' : project,
			'new' : new_bug,
			'title' : title,
			'desc' : desc,
			'assignee' : assignee,
			'status' : status,
			'update' : update,
			'files' : files,
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
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery(messages).html(response.message);
			} else {
				jQuery(messages).html(response.message);
				window.location.replace(response.redirect);
			}
		});	
	});
});