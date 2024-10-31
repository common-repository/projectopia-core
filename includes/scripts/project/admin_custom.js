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
	jQuery('#project_contributors .inside').each(function() {
		jQuery(this).children('.team_member').matchHeight();
	});
	jQuery('.author-other.no_access td.column-title span.post-state').remove();
	jQuery('.author-other.no_access td.column-title a').remove();
	jQuery('.author-other.no_access td.column-title a').remove();
	jQuery('.author-other.no_access td.column-title .row-actions').remove();
	jQuery('.author-other.no_access td.column-title strong').html(localisation.projects.assign_error);
	jQuery('.author-other.no_access').remove();
	jQuery('button.cancel-colorbox').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox.close();
	});
	var type = jQuery('#project_ref_for_basics').val();
	if(!jQuery('#posts-filter').length && jQuery('#project_details').length) {
		if(!type) {
			jQuery.colorbox({
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
	jQuery('button.save-basics').click(function(e){
		e.preventDefault();
		var ref = jQuery('#quote_ref').val();
		var start = jQuery('#start_date').val();
		var finish = jQuery('#finish_date').val();
		if(ref && start && finish) {
			jQuery.colorbox.close();
			setTimeout(function(){
				jQuery('#publish').trigger('click');
			}, 500);
		} else {
			jQuery('#basics-error').html('<div class="cqpim-alert cqpim-alert-danger alert-display">' + localisation.projects.project_dates + '</div><div class="clear"></div>');
			jQuery.colorbox.resize();
		}
	});

	/** Cancel project edit popup */
	jQuery(document).on('click', 'a.cancel-creation', function(e){
		e.preventDefault();
		jQuery.colorbox.close();
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
	jQuery('#unsigned_off').on('click', function(e){
		e.preventDefault();
			jQuery.colorbox({
				'width' : '500px',
				'maxWidth':'95%',
				'inline': true,
				'href': '#quote_unsign',							
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
	jQuery('button#add_team_member').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox({
			'width' : '650px',
			'maxWidth':'95%',
			'inline': true,
			'href': '#add_team_member_div',
			'opacity': '0.5',
		});
	});
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
	

	jQuery('button#add_file_trigger').on('click', function(e){
		jQuery("#password_reset").remove();
		e.preventDefault();
		jQuery.colorbox({
			'width' : '500px',
			'maxWidth':'95%',
			'inline': true,
			'href': '#add_file',
			'opacity': '0.5',
		});
	});

	jQuery('button.save').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox.close();
		setTimeout(function(){
			jQuery('#publish').trigger('click');
		}, 500);
	});
	jQuery(document).on('click', 'button.delete_stage_conf', function(e){
		e.preventDefault();
		var id = jQuery(this).val();
			jQuery.colorbox({
			'width' : '500px',
				'maxWidth':'95%',
				'inline': true,
				'fixed': true,
				'href': '#delete-milestone-div-' + id,	
				'opacity': '0.5',
			});			
	});
	jQuery(document).on('click', 'button.cancel_delete_stage', function(e){
		e.preventDefault();
		jQuery.colorbox.close();
	});
	jQuery(document).on('click', 'button.cancel_delete_task', function(e){
		e.preventDefault();
		jQuery.colorbox.close();
	});
	jQuery(document).on('click', 'button.delete_file', function(e){		
		e.preventDefault();
		var attID = jQuery(this).data('id');
		jQuery('#cqpim_overlay').show();
		var hiddenField = '<input type="hidden" name="delete_file[]" value="' + attID + '" />';
		jQuery(this).parents('div.inside').prepend(hiddenField);
		jQuery('#publish').trigger('click');
	});
	jQuery(document).on('click', '.assign_all',  function(e){
		e.preventDefault();
		var ms = jQuery(this).data('ms');
		jQuery.colorbox({			
			'width' : '500px',
			'maxWidth':'95%',
			'inline': true,
			'href': '#assign-all',
			'opacity': '0.5',
		});	
		jQuery.colorbox.resize();
		jQuery('#assign_all_ms').val(ms);
	});

	jQuery('#add_file_ajax').on('click', function(e) {
		e.preventDefault();
		
		var project = jQuery('#project_id').val();
		var files = jQuery('#upload_attachment_ids').val();
		var task_id = jQuery('#task_id').val();
		var spinner = jQuery('#cqpim_overlay');
		var messages = jQuery('#file_messages');

		if(task_id==0 || files==undefined){
			jQuery(messages).html("Please select required fields"); 
			return false;
		}
		var data = {
			'action' : 'pto_add_task_files',
			'project' : project,
			'task':task_id,
			'files' : files,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: localisation.ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				// show spinner
				spinner.show();
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery(messages).html(response.message);
			} else {
				jQuery(messages).html(response.message);
				window.location.reload();
			}
		});	
	});
 
});