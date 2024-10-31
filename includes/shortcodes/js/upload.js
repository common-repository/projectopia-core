jQuery(document).ready(function() {
	
	jQuery('.cqpim-file-upload').on('change', function(e) {
		
		e.preventDefault();
		
		var element = jQuery(this);
		
		var id = jQuery(this).attr('id');

		var formData = new FormData();

		formData.append('action', 'upload-attachment');
		
		formData.append('async-upload', jQuery(this)[0].files[0]);
		
		formData.append('name', jQuery(this)[0].files[0].name);
		
		formData.append('_wpnonce', upload_config.nonce);

		jQuery.ajax({
			
			url: upload_config.upload_url,
			
			data: formData,
			
			processData: false,
			
			contentType: false,
			
			dataType: 'json',
			
			type: 'POST',
			
			beforeSend: function() {
				
				jQuery(element).hide();
				
				jQuery('#upload_messages_' + id).show().html('<span style="margin-top:5px;background:#f0ad4e;padding:5px 10px;color:#fff; border-radius:5px; -moz-border-radius:5px">' + upload_config.strings.uploading + '</span>'); 
				
			},
			
			success: function(resp) {
				
				console.log(resp);
				
				if ( resp.success ) {
					
					jQuery('#upload_messages_' + id).show().html('<span style="margin-top:5px;background:#8ec165;padding:5px 10px;color:#fff; border-radius:5px; -moz-border-radius:5px">' + upload_config.strings.success + ' - ' + resp.data.filename + ' - <a href="#" style="color:#fff" class="btn-change-image" data-id="' + id + '">' + upload_config.strings.change + '</a></span>');
					
					jQuery('#upload_' + id).val(resp.data.id);

				} else {
					
					jQuery('#upload_messages_' + id).show().html('<span style="margin-top:5px;background:#d9534f;padding:5px 10px;color:#fff; border-radius:5px; -moz-border-radius:5px">' + upload_config.strings.error + '</span>');
					
					jQuery(element).show();
					
					jQuery('#upload_' + id).val('');
					
				}
				
				
				
			}
			
		});
		
	});	
	
	jQuery('.btn-change-image').on( 'click', function(e) {
		
		e.preventDefault();
		
		var id = jQuery(this).data('id');
		
		var element = jQuery('#' + id);
		
		jQuery('#upload_messages_' + id).empty().hide();
		
		element.val('').show();
		
		jQuery('#upload_' + id).val('');
		
	});
	
	jQuery('.cqpim-file-upload').on('click', function() {
		
		var id = jQuery(this).data('id');
		
		jQuery(this).val('');
		
		jQuery('#upload_' + id).val('');
		
	});

});