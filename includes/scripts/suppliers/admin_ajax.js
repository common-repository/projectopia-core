jQuery(document).ready(function() {
	jQuery('#income_control_date').on('change', function(e) {
		e.preventDefault();
		var date = jQuery('#income_control_date').val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_edit_income_graph',
			'date' : date,
			'type' : 'date',
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
			},
		}).done(function(response){
			spinner.hide();
			location.reload();	
		});
	});	
});