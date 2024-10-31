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
	jQuery(document).on('click', 'button.delete_file', function(e){		
		e.preventDefault();
		var attID = jQuery(this).data('id');
		jQuery('#cqpim_overlay').show();
		var hiddenField = '<input type="hidden" name="delete_file[]" value="' + attID + '" />';
		jQuery(this).parents('div.inside').prepend(hiddenField);
		jQuery('#publish').trigger('click');
	});
	var type = jQuery('#quote_type').val();
	if(!jQuery('#posts-filter').length) {
		if(!type) {
			jQuery.colorbox({
				'width' : '500px',
				'maxWidth':'95%',
				'inline': true,
				'href': '#quote_basics',							
				'overlayClose'  : false, 
				'escKey' : false,
				'opacity': '0.5',	
				'onLoad': function() {
					jQuery('#cboxClose').remove();
				},
			});	
			jQuery.colorbox.resize();
		}
	}
	jQuery('a#add-milestone').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox({
			'width' : '500px',
			'maxWidth':'95%',
			'inline': true,
			'href': '#add-milestone-div',
			'opacity': '0.5',
		});	
		jQuery.colorbox.resize();
	});
	jQuery('a#apply-template').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox({
			'width' : '500px',
			'maxWidth':'95%',
			'inline': true,
			'href': '#apply-template-div',
			'opacity': '0.5',
		});	
		jQuery.colorbox.resize();
	});
	jQuery('a#clear-all').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox({
			'width' : '500px',
			'maxWidth':'95%',
			'inline': true,
			'href': '#clear-all-div',
			'opacity': '0.5',
		});	
		jQuery.colorbox.resize();
	});
	jQuery('#edit-project-brief').on('click', function(e){
		e.preventDefault();
			jQuery.colorbox({
				'inline': true,
				'fixed': true,
				'href': '#edit-project-info-div',	
				'opacity': '0.5',
			});	
	});
	jQuery('#edit-quote-header').on('click', function(e){
		e.preventDefault();
			jQuery.colorbox({
				'inline': true,
				'fixed': true,
				'href': '#edit-quote-header-div',	
				'opacity': '0.5',
			});			
	});
	jQuery('#edit-quote-footer').on('click', function(e){
		e.preventDefault();
			jQuery.colorbox({
				'inline': true,
				'fixed': true,
				'href': '#edit-quote-footer-div',	
				'opacity': '0.5',
			});			
	});
	jQuery('#edit-quote-details').on('click', function(e){
		e.preventDefault();
			jQuery.colorbox({
				'width' : '500px',
				'maxWidth':'95%',
				'inline': true,
				'href': '#quote_basics',							
				'opacity': '0.5',	
			});	
			jQuery.colorbox.resize();	
	});
	jQuery('.convert_to_project').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox({
			'width' : '500px',
			'maxWidth':'95%',
			'inline': true,
			'href': '#quote_convert',
			'opacity': '0.5',
		});	
		jQuery.colorbox.resize();
	});
	jQuery('button.save').on('click', function(e){
		e.preventDefault();
		jQuery('#cqpim_overlay').show();
		jQuery.colorbox.close();
		setTimeout(function(){
			jQuery('#publish').trigger('click');
		}, 500);
	});
	jQuery('button.cancel-colorbox').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox.close();
	});
	jQuery('button.save-basics').click(function(e){
		e.preventDefault();
		var type = jQuery('#quote_type').val();
		var client = jQuery('.quote_client_dropdown').val(); 
		var ref = jQuery('#quote_ref').val();
		var start = jQuery('#start_date').val();
		var finish = jQuery('#finish_date').val();
		if(type && client && ref && start && finish) {
			jQuery('#cqpim_overlay').show();
			jQuery.colorbox.close();
			setTimeout(function(){
				jQuery('#publish').trigger('click');
			}, 500);
		} else {
			jQuery('#basics-error').html('<div class="cqpim-alert cqpim-alert-danger alert-display">' + localisation.quotes.project_dates + '</div><div class="clear"></div>');
			jQuery.colorbox.resize();
		}
	});
	jQuery(document).on('click', 'button.cancel_delete_stage', function(e){
		e.preventDefault();
		jQuery.colorbox.close();
	});
	jQuery(document).on('click', 'button.cancel_delete_task', function(e){
		e.preventDefault();
		jQuery.colorbox.close();
	});

	// Message input dialog box for quote.  
	jQuery('button#add_message_trigger').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox({
			'width' : '500px',
			'maxWidth':'95%',
			'inline': true,
			'href': '#add_message',
			'opacity': '0.5',
		});
	});

});