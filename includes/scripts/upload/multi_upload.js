jQuery(document).ready(function() {
	jQuery('.cqpim-file-upload').on('change', function(e) {
		e.preventDefault();
		var element = jQuery(this);
		var id = jQuery(this).attr('id');
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
				jQuery('#upload_attachments').show().append('<span class="cqpim-alert cqpim-alert-info cqpim-uploading cqpim-messages-upload">' + localisation.uploads.strings.uploading + '</span>'); 
			},
			success: function(resp) {
				console.log(resp);
				if ( resp.success ) {
					jQuery('.cqpim-uploading').remove();
					jQuery('.cqpim-upload-error').remove();
					jQuery('#upload_attachments').show().append('<span id="f' + resp.data.id + '" class="cqpim-alert cqpim-alert-success cqpim-messages-upload">' + localisation.uploads.strings.success + ' - ' + resp.data.filename + ' <i style="cursor:pointer" class="fa fa-trash btn-change-image" aria-hidden="true" data-id="' + resp.data.id + '"></i></span>');
					ids = jQuery('#upload_attachment_ids').val();
					if(!ids) {
						jQuery('#upload_attachment_ids').val(resp.data.id);
					} else {
						jQuery('#upload_attachment_ids').val(ids + ',' + resp.data.id);
					}
					if (jQuery.fn.masonry) {
						jQuery('.masonry-grid').masonry({
							columnWidth: '.grid-sizer',
							itemSelector: '.grid-item',
							percentPosition: true
						});
					}
				} else {
					jQuery('.cqpim-uploading').remove();
					jQuery('.cqpim-upload-error').remove();
					jQuery('#upload_attachments').show().append('<span class="cqpim-alert cqpim-alert-danger cqpim-messages-upload cqpim-upload-error">' + localisation.uploads.strings.error + '</span>');
					jQuery(element).show();
					if (jQuery.fn.masonry) {
						jQuery('.masonry-grid').masonry({
							columnWidth: '.grid-sizer',
							itemSelector: '.grid-item',
							percentPosition: true
						});
					}
				}
			}
		});
	});	
	jQuery('.rcqpim-file-upload').on('change', function(e) {
		e.preventDefault();
		var element = jQuery(this);
		var id = jQuery(this).attr('id');
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
				//jQuery(element).hide();
				jQuery('#rupload_attachments').show().append('<span class="cqpim-alert cqpim-alert-info cqpim-uploading cqpim-messages-upload">' + localisation.uploads.strings.uploading + '</span>'); 
			},
			success: function(resp) {
				console.log(resp);
				if ( resp.success ) {
					jQuery('.cqpim-uploading').remove();
					jQuery('.cqpim-upload-error').remove();
					jQuery('#rupload_attachments').show().append('<span id="f' + resp.data.id + '" class="cqpim-alert cqpim-alert-success cqpim-messages-upload">' + localisation.uploads.strings.success + ' - ' + resp.data.filename + ' <i style="cursor:pointer" class="fa fa-trash rbtn-change-image" aria-hidden="true" data-id="' + resp.data.id + '"></i></span>');
					ids = jQuery('#rupload_attachment_ids').val();
					if(!ids) {
						jQuery('#rupload_attachment_ids').val(resp.data.id);
					} else {
						jQuery('#rupload_attachment_ids').val(ids + ',' + resp.data.id);
					}
					if (jQuery.fn.masonry) {
						jQuery('.masonry-grid').masonry({
							columnWidth: '.grid-sizer',
							itemSelector: '.grid-item',
							percentPosition: true
						});
					}
				} else {
					jQuery('.cqpim-uploading').remove();
					jQuery('.cqpim-upload-error').remove();
					jQuery('#rupload_attachments').show().append('<span class="cqpim-alert cqpim-alert-danger cqpim-messages-upload cqpim-upload-error">' + localisation.uploads.strings.error + '</span>');
					jQuery(element).show();
					if (jQuery.fn.masonry) {
						jQuery('.masonry-grid').masonry({
							columnWidth: '.grid-sizer',
							itemSelector: '.grid-item',
							percentPosition: true
						});
					}
				}
			}
		});
	});	
	jQuery('#upload_attachments').on( 'click', '.btn-change-image', function(e) {
		e.preventDefault();
		var id = jQuery(this).data('id');
		var element = jQuery('#f' + id).remove();
		var list = jQuery('#upload_attachment_ids').val();
		var new_list = cqpim_removeValue(list,id,',');
		jQuery('#upload_attachment_ids').val(new_list);
		if (jQuery.fn.masonry) {
			jQuery('.masonry-grid').masonry({
				columnWidth: '.grid-sizer',
				itemSelector: '.grid-item',
				percentPosition: true
			});
		}
	});
	jQuery('#upload_attachments').on( 'click','.rbtn-change-image', function(e) {
		e.preventDefault();
		var id = jQuery(this).data('id');
		var element = jQuery('#f' + id).remove();
		var rlist = jQuery('#rupload_attachment_ids').val();
		var rnew_list = cqpim_removeValue(rlist,id,',');
		jQuery('#rupload_attachment_ids').val(rnew_list);
		if (jQuery.fn.masonry) {
			jQuery('.masonry-grid').masonry({
				columnWidth: '.grid-sizer',
				itemSelector: '.grid-item',
				percentPosition: true
			});
		}
	});
	jQuery('.cqpim-file-upload').on('click', function() {
		var id = jQuery(this).data('id');
		jQuery(this).val('');
	});
	jQuery('.rcqpim-file-upload').on('click', function() {
		var id = jQuery(this).data('id');
		jQuery(this).val('');
	});
});
function cqpim_removeValue(list, value, separator) {
  separator = separator || ",";
  var values = list.split(separator);
  for(var i = 0 ; i < values.length ; i++) {
    if(values[i] == value) {
      values.splice(i, 1);
      return values.join(separator);
    }
  }
  return list;
}