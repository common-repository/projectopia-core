jQuery(document).ready(function() {
	jQuery('#delete_task_confirm').on('click', function(e) {
		e.preventDefault();
		var task_id = jQuery(this).data('id');
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_delete_plan_page',
			'task_id' : task_id,
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#delete_task_confirm').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
			spinner.hide();
				jQuery('#delete_task_confirm').prop('disabled', false);
			} else {
				spinner.hide();
				jQuery('#delete_task_confirm').prop('disabled', false);
				window.location.href = response.redirect;				
			}				
		});
	});
});