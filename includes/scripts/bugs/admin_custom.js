jQuery(document).ready(function() {
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
	jQuery('#project_bugs_table').dataTable({
		"order": [[ 0, 'desc' ]],
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
	jQuery('.bdataTable').dataTable({
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
	jQuery('#save_bug').on('click', function(e) {
		e.preventDefault();
		setTimeout(function(){
			jQuery('#publish').trigger('click');
		}, 500);
	});
	jQuery('#create_bug_trigger').on('click', function(e) {
		e.preventDefault();
		jQuery.colorbox({
			'maxWidth':'95%',
			'inline': true,
			'fixed': true,
			'href': '#password_reset',	
			'opacity': '0.5',
		});			
	});
	// Create Bug
	jQuery('#raise_bug_ajax').on('click', function(e) {
		e.preventDefault();
		var project = jQuery('#post_ID').val();
		var title = jQuery('#bug_title').val();
		var desc = jQuery('#bug_desc').val();
		var assignee = jQuery('#bug_assignee').val();
		var status = jQuery('#bug_status').val();
		var new_bug = 1;
		var files = jQuery('#upload_attachment_ids').val();
		var spinner = jQuery('#cqpim_overlay');
		var messages = jQuery('#password_messages');
		var data = {
			'action' : 'pto_update_bug',
			'project' : project,
			'title' : title,
			'desc' : desc,
			'assignee' : assignee,
			'status' : status,
			'new' : new_bug,
			'files' : files,
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
	jQuery(document).on('click', 'button.delete_file', function(e){		
		e.preventDefault();
		var attID = jQuery(this).data('id');
		jQuery('#cqpim_overlay').show();
		var hiddenField = '<input type="hidden" name="delete_file[]" value="' + attID + '" />';
		jQuery(this).parents('div.inside').prepend(hiddenField);
		jQuery('#publish').trigger('click');
	});
	jQuery('#delete_bug').on('click', function(e) {
		e.preventDefault();
		var task_id = jQuery(this).data('id');
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_delete_bug_page',
			'task_id' : task_id,
			'pto_nonce' : localisation.global_nonce
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
});