jQuery(document).ready(function() {
	jQuery('#post_type').on('change', function() {
		var type = jQuery(this).val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_switch_custom_field_type',
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
				jQuery('#post_type').prop('disabled', true);
			},
		}).done(function(response){
			if(response.success === false) {
				spinner.hide();
				jQuery('#post_type').prop('disabled', false);
			} else {
				spinner.hide();
				jQuery('#post_type').prop('disabled', false);
				location.reload();
			}
		});
	});
});
function cqpim_save_custom_fields() {
	var type = jQuery('#post_type').val();
	var builder = jQuery('#builder_data').val();
	var spinner = jQuery('.ajax_spinner');
	var data = {
		'action' : 'pto_save_custom_fields',
		'builder' : builder,
		'type' : type,
		'pto_nonce' : localisation.global_nonce
	}
	jQuery.ajax({
		url: ajaxurl,
		data: data,
		type: 'POST',
		dataType: 'json',
		beforeSend: function(){
			spinner.show();
		},
	}).done(function(response){
		if(response.success === false) {
			spinner.hide();
			alert(localisation.cf_alerts.fail);
		} else {
			spinner.hide();
			alert(localisation.cf_alerts.done);
		}
	});
}