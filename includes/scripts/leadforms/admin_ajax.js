jQuery(document).ready(function() {
	var scrollTop = localStorage.getItem('scrollTop');
    if (scrollTop !== null) {
        jQuery(window).scrollTop(Number(scrollTop));
        localStorage.removeItem('scrollTop');
    }
	jQuery('#publish').click(function(event) {
		localStorage.setItem('scrollTop', jQuery(window).scrollTop());
		return true;
	});
	jQuery('#builder_type').on('change', function(e) {
		e.preventDefault();
		var spinner = jQuery('#cqpim_overlay');
		spinner.show();
		var form = jQuery(this).val();
		if(form == 'gf') {
			jQuery('#gravity_form_cont').show();
			jQuery('#leadform_builder_builder').hide();
		} else {
			jQuery('#gravity_form_cont').hide();
		}
		spinner.hide();
	});
	jQuery('button.save').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox.close();
		setTimeout(function(){
			jQuery('#publish').trigger('click');
		}, 500);
	});
});