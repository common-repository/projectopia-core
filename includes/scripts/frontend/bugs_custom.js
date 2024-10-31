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
	jQuery('.masonry-grid').masonry({
		columnWidth: '.grid-sizer',
		itemSelector: '.grid-item',
		percentPosition: true
	});	
	jQuery('#update_bug_btn').on('click', function(e) {
		e.preventDefault();
		var bug_id = jQuery(this).data('id');
		var title = jQuery('#bug_title').val();
		var desc = jQuery('#bug_desc').val();
		var assignee = jQuery('#bug_assignee').val();
		var status = jQuery('#bug_status').val();
		var files = jQuery('#upload_attachment_ids').val();
		var update = jQuery('#bug_update').val();
		var spinner = jQuery('#overlay');
		var messages = jQuery('#password_messages');
		var data = {
			'action' : 'pto_update_bug',
			'bug_id' : bug_id,
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
				spinner.show();
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery(messages).html(response.message);
			} else {
				jQuery(messages).html(response.message);
				location.reload();
			}
		});	
	});
});