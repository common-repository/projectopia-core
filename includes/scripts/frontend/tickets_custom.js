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
	jQuery('.masonry-grid').masonry({
		columnWidth: '.grid-sizer',
		itemSelector: '.grid-item',
		percentPosition: true
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
	jQuery('#update_support').click(function(e) {
		e.preventDefault();
		var form_data = {};
		var post = jQuery('#post_id').val();
		form_data['ticket_status_new'] = jQuery('#ticket_status_new').val();
		form_data['ticket_files'] = jQuery('#upload_attachment_ids').val();
		form_data['ticket_priority_new'] = jQuery('#ticket_priority_new').val();
		form_data['ticket_update_new'] = jQuery('#ticket_update_new').val();
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
			'action' : 'pto_update_support_ticket',
			'post_id' : post,
			'data' : form_data,	
			'type' : 'client',
			'pto_nonce' : localisation.global_nonce
		}
		jQuery.ajax({
			url: localisation.ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				jQuery('#overlay').show();
				jQuery('#update_support').prop('disabled', true);
			},
		}).always(function(response) {
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				jQuery('#overlay').hide();
				jQuery('#update_support').prop('disabled', false);
				alert(response.message);
			} else {
				jQuery('#update_support').prop('disabled', false);
				location.reload();				
			}
		});
	});
});