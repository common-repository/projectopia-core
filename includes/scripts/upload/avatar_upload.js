jQuery(document).ready(function() {
	jQuery('.cqpim-file-upload-avatar').on('change', function(e) {
		e.preventDefault();
		var element = jQuery(this);
		var formData = new FormData();
		formData.append('action', 'upload-attachment');
		formData.append('async-upload', jQuery(this)[0].files[0]);
		formData.append('name', jQuery(this)[0].files[0].name);
		formData.append('_wpnonce', localisation.uploads.nonce);
		jQuery.ajax({
			url: localisation.uploads.upload_url,
			data: formData,
			processData: false,
			contentType: false,
			dataType: 'json',
			type: 'POST',
			beforeSend: function() {
				jQuery(element).hide();
				jQuery('#upload_attachments').show().html('<span style="margin-top:5px;background:#f0ad4e;padding:5px 10px;color:#fff; border-radius:5px; -moz-border-radius:5px">' + localisation.uploads.strings.uploading + '</span>'); 
			},
			success: function(resp) {
				console.log(resp);
				if ( resp.success ) {
					jQuery('#upload_attachments').show().html('<span style="margin-top:5px;background:#8ec165;padding:5px 10px;color:#fff; border-radius:5px; -moz-border-radius:5px">' + localisation.uploads.strings.success + ' - ' + resp.data.filename + ' - <a href="#" style="color:#fff" class="btn-change-image">' + localisation.uploads.strings.change + '</a></span>');
					jQuery('#upload_attachment_ids').val(resp.data.id);
					var extension = resp.data.filename.substr( (resp.data.filename.lastIndexOf('.') +1) );
					var filename_trimmed = resp.data.filename.replace(/\.[^/.]+$/, "")
					var thumb = resp.data.url.replace(resp.data.filename, filename_trimmed + '-150x150.' + extension); 
					jQuery('#pto_avatar_preview').html('<img src="' + thumb + '" />');
					jQuery('#pto_avatar_preview_cont').show();
				} else {
					jQuery('#upload_attachments').show().html('<span style="margin-top:5px;background:#d9534f;padding:5px 10px;color:#fff; border-radius:5px; -moz-border-radius:5px">' + localisation.uploads.strings.error + '</span>');
					jQuery(element).show();
					jQuery('#upload_attachment_ids').val('');
				}
			}
		});
	});	
	jQuery('.cqpim_upload_wrapper').on( 'click', '.btn-change-image',  function(e) {
		e.preventDefault();
		var element = jQuery('.cqpim-file-upload-avatar');
		jQuery('#upload_attachments').empty().hide();
		jQuery('#pto_avatar_preview').empty();
		jQuery('#pto_avatar_preview_cont').hide();
		element.val('').show();
		jQuery('#upload_attachment_ids').val('');
	});
	jQuery('.cqpim-file-upload-avatar').on('click', function() {
		jQuery(this).val('');
		jQuery('#upload_attachment_ids').val('');
	});
});