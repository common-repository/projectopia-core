jQuery(document).ready(function() {
	jQuery('.menu-open').on('click', function(e) {
		e.preventDefault;
		jQuery(this).hide();
		jQuery('#cqpim-dash-sidebar').show();
		jQuery('.menu-close').show();
	});
	jQuery('.menu-close').on('click', function(e) {
		e.preventDefault;
		jQuery(this).hide();
		jQuery('#cqpim-dash-sidebar').hide();
		jQuery('.menu-open').show();
	});
	jQuery('.masonry-grid').masonry({
		columnWidth: '.grid-sizer',
		itemSelector: '.grid-item',
		percentPosition: true
	});
	jQuery('.dataTable').dataTable({
		"order": [[ 0, 'desc' ]],
		"language": {
			"processing":     localisation.datatables.sProcessing,
			"search":         localisation.datatables.sSearch,
			"lengthMenu":     localisation.datatables.sLengthMenu,
			"info":           localisation.datatables.sInfo,
			"infoEmpty":      localisation.datatables.sInfoEmpty,
			"infoFiltered":   localisation.datatables.sInfoFiltered,
			"infoPostFix":    localisation.datatables.sInfoPostFix,
			"loadingRecords": localisation.datatables.sLoadingRecords,
			"zeroRecords":    localisation.datatables.sZeroRecords,
			"emptyTable":     localisation.datatables.sEmptyTable,
			"paginate": {
				"first":      localisation.datatables.sFirst,
				"previous":   localisation.datatables.sPrevious,
				"next":       localisation.datatables.sNext,
				"last":       localisation.datatables.sLast
			},
			"aria": {
				"sortAscending":  localisation.datatables.sSortAscending,
				"sortDescending": localisation.datatables.sSortDescending
			}
		}
	});
	jQuery('.datepicker').datepicker({ 
		showButtonPanel: true,
		closeText: localisation.calendar.closeText,
		currentText: localisation.calendar.currentText,
		monthNames: localisation.calendar.monthNames,
		monthNamesShort: localisation.calendar.monthNamesShort,
		dayNames: localisation.calendar.dayNames,
		dayNamesShort: localisation.calendar.dayNamesShort,
		dayNamesMin: localisation.calendar.dayNamesMin,
		dateFormat: localisation.calendar.dateFormat,
		firstDay: localisation.calendar.firstDay,	
	});
	jQuery('#cancel_sub').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox({			
			'width' : '500px',
			'maxWidth':'95%',
			'inline': true,
			'href': '#invoice_payment',
			'opacity': '0.5',
		});	
		jQuery.colorbox.resize();
	});		
	jQuery('#cancel_sub_conf').click(function(e) {
		e.preventDefault();
		var post = jQuery(this).data('id');
		var data = {
			'action' : 'pto_cancel_subscription',
			'post_id' : post,
		}
		jQuery.ajax({
			url: localisation.ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				jQuery('#overlay').show();
				jQuery('#cancel_sub_conf').prop('disabled', true);
			},
		}).always(function(response) {
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				jQuery('#overlay').hide();
				jQuery('#cancel_sub_conf').prop('disabled', false);
				alert(response.message);
			} else {
				jQuery('#cancel_sub_conf').prop('disabled', false);
				window.location.replace(response.redirect);				
			}
		});
	});
	jQuery('#pp-ajax-start').click(function(e) {
		e.preventDefault();
		var post = jQuery(this).data('id');
		var data = {
			'action' : 'pto_process_paypal_redirect',
			'post_id' : post,
		}
		jQuery.ajax({
			url: localisation.ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				jQuery('#overlay').show();
				jQuery('#pp-ajax-start').prop('disabled', true);
			},
		}).always(function(response) {
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				jQuery('#overlay').hide();
				jQuery('#pp-ajax-start').prop('disabled', false);
				alert(response.message);
			} else {
				jQuery('#pp-ajax-start').prop('disabled', false);
				window.location.replace(response.redirect);				
			}
		});
	});
});