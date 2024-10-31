jQuery(document).ready(function() {
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
	jQuery("#uploaded_files").load(location.href + " #uploaded_files");
	jQuery('.masonry-grid').masonry({
		columnWidth: '.grid-sizer',
		itemSelector: '.grid-item',
		percentPosition: true
	});
	jQuery('#update_task').click(function(e) {
		e.preventDefault();
		var file_task_id = jQuery('#file_task_id').val();	
		var add_task_message = jQuery('#add_task_message').val();	
		var owner = jQuery('#task_owner').val();	
		var files = jQuery('#upload_attachment_ids').val();
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
			'action' : 'pto_client_update_task',
			'file_task_id' : file_task_id,
			'add_task_message' : add_task_message,
			'task_owner' : owner,
			'files' : files,
			'custom' : custom,
			'pto_nonce' : localisation.global_nonce
		}
		console.log(data);
		jQuery.ajax({
			url: localisation.ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				jQuery('#overlay').show();
				jQuery('#update_task').prop('disabled', true);
			},
		}).always(function(response) {
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				jQuery('#overlay').hide();
				jQuery('#update_task').prop('disabled', false);
				alert(response.message);
			} else {
				jQuery('#update_task').prop('disabled', false);
				location.reload();				
			}
		});
	});
});