jQuery(document).ready(function() {
	jQuery('.save').click(function(e) {
		e.preventDefault();
		//jQuery('#cqpim_overlay').show();
		jQuery('#publish').trigger('click');
	});
});