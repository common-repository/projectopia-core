jQuery(document).ready(function() {
	jQuery('.cqpim-file-upload-form').on('change', function(e) {
		e.preventDefault();
		var element = jQuery(this);
		var id = jQuery(this).attr('id');
		
		len = jQuery(this)[0].files.length;
		jQuery(element).hide();
		jQuery('#upload_messages_' + id).show().html('<span style="margin-top:5px;background:#f0ad4e;padding:5px 10px;color:#fff; border-radius:5px; -moz-border-radius:5px" class="upload-msg">' + localisation.uploads.strings.uploading + '</span>'); 
		for(i =0; i<len; i++) {
			var formData = new FormData();
		
			formData.append('action', 'upload-attachment');
			formData.append('async-upload', jQuery(this)[0].files[i]);
			formData.append('name', jQuery(this)[0].files[i].name);
			formData.append('_wpnonce', localisation.uploads.nonce);
			jQuery.ajax({
				url: localisation.uploads.upload_url,
				data: formData,
				processData: false,
				contentType: false,
				dataType: 'json',
				type: 'POST',
				
				success: function(resp) {
					console.log(resp);
					if ( resp.success ) {
						resp_id = resp.data.id;
						jQuery('.upload-msg').remove();
						jQuery('#upload_messages_' + id).show().append('<span style="margin-top:5px;background:#8ec165;padding:5px 10px;color:#fff; border-radius:5px; -moz-border-radius:5px">' + localisation.uploads.strings.success + ' - ' + resp.data.filename + ' - <a href="#" style="color:#fff" class="btn-change-image" data-id="' + id + '" data-img = "'+resp_id+'">' + localisation.uploads.strings.change + '</a></span><br>');
						
						vl = jQuery('#upload_' + id).val();
						
						if(vl !='')
							vl += ','+resp_id;
						else
							vl = resp_id;
						jQuery('#upload_' + id).val(vl);
					} else {
						jQuery('#upload_messages_' + id).show().append('<span style="margin-top:5px;background:#d9534f;padding:5px 10px;color:#fff; border-radius:5px; -moz-border-radius:5px">' + localisation.uploads.strings.error + '</span>');
						jQuery(element).show();
						jQuery('#upload_' + id).val('');
					}
				}
			});
		}
		
		
	});	
	jQuery('.cqpim_form_item').on( 'click', '.btn-change-image',  function(e) {
		e.preventDefault();
		var id = jQuery(this).data('id');
		var element = jQuery('#' + id);

		var img_id  = jQuery(this).data('img');
		
		jQuery(this).closest('span').prev('br').remove();
		jQuery(this).closest('span').remove();
		//jQuery('#upload_messages_' + id).empty().hide();
		prev_val = jQuery('#upload_' + id).val();
		
		prev_val_arr = prev_val.split(",");
		
		var index = prev_val_arr.indexOf(img_id);
		var img_arr = new Array();
		for(i=0;i<prev_val_arr.length;i++) {
			if(prev_val_arr[i] != img_id )
				img_arr.push(prev_val_arr[i]);
		}
		
		new_val = img_arr.join();
		if(new_val == '')
			element.val('').show();
		jQuery('#upload_' + id).val(new_val);
	});
	jQuery('.cqpim-file-upload-form').on('click', function() {
		var id = jQuery(this).data('id');
		jQuery(this).val('');
		jQuery('#upload_' + id).val('');
	});
	if (typeof select2 !== "undefined") { 
		jQuery('.pto-select2-field').select2({ width: 'element' });
	}
	
});