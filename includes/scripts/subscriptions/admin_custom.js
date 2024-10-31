jQuery(document).ready(function() {
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
	jQuery('.save').click(function(e) {
		e.preventDefault();
		jQuery('#publish').trigger('click');
	});
	jQuery('.delete_plan').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox({
			'width' : '500px',
			'maxWidth':'95%',
			'inline': true,
			'href': '#delete-plan-div',
			'opacity': '0.5',
		});	
		jQuery.colorbox.resize();
	});
	jQuery('#cancel_subscription').on('click', function(e){
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
	jQuery('.cancel-colorbox').click(function(e) {
		e.preventDefault();
		jQuery.colorbox.close();
	});
	jQuery('#sub_item_type').on('change', function(e) {
		if(jQuery(this).val() == 'plan') {
			jQuery('#sub_plan').show();
			jQuery('#sub_plan').parent().parent().show();
			jQuery('#sub_plan').attr('disabled', false);
			jQuery('#line_items_cont').hide();
			jQuery('#plan_items_cont').show();
		} else {
			jQuery('#sub_plan').hide();
			jQuery('#sub_plan').parent().parent().hide();
			jQuery('#sub_plan').attr('disabled', true);
			jQuery('#line_items_cont').show();
			jQuery('#plan_items_cont').hide();
		}
	});
	jQuery('#sub_item_type').trigger('change');
	jQuery('.repeater').repeater();
	jQuery(document).on('change','.invoice_price', function() {
		var row_index = jQuery(this).attr('name');
		row = row_index.replace(/[^0-9\.]/g, '');
		calculate_totals(this, row);
	});
	jQuery('.line_tax').on('change', function() {
		var row_index = jQuery(this).attr('name');
		row = row_index.replace(/[^0-9\.]/g, '');
		var element = jQuery('[name="group-a[' + row + '][price]"]');
		calculate_totals(element, row);
	});
	jQuery('.line_stax').on('change', function() {
		var row_index = jQuery(this).attr('name');
		row = row_index.replace(/[^0-9\.]/g, '');
		var element = jQuery('[name="group-a[' + row + '][price]"]');
		calculate_totals(element, row);
	});
	jQuery(document).on('change','.invoice_qty', function() {
		var row_index = jQuery(this).attr('name');
		row = row_index.replace(/[^0-9\.]/g, '');
		var element = jQuery('[name="group-a[' + row + '][price]"]');
		calculate_totals(element, row);
	});
	jQuery('.line_delete').on('click', function() {
		calculate_totals(this);
	});
});

function calculate_totals(element, row) {
		var qty = +jQuery('[name="group-a[' + row + '][qty]"]').val();
		var price = +jQuery(element).val();
		var ltot = price * qty;
		ltot = ltot.toFixed(2);
		jQuery('[name="group-a[' + row + '][line_total]"]').val(ltot);
		var sub = 0;
		jQuery('.invoice_line_total').each(function() {
			sub += Number(jQuery(this).val());
		});
		sub = sub.toFixed(2);
		jQuery('#invoice_subtotal').val(sub);
		var tax = 0;
		var stax = 0;
		var tax_rate = +jQuery('#tax_rate').val();
		var stax_rate = +jQuery('#stax_rate').val();
		jQuery('.invoice_line_total').each(function() {
			amount = Number(jQuery(this).val());
			row = jQuery(this).data('row');
			if(jQuery('[name="group-a[' + row + '][line_tax][]"]').is(':checked')) {
			} else {
				tax_figure = amount / 100 * tax_rate;
				tax = tax + tax_figure;
			}	
			if(jQuery('[name="group-a[' + row + '][line_stax][]"]').is(':checked')) {
			} else {
				stax_figure = amount / 100 * stax_rate;
				stax = stax + stax_figure;
			}			
		});
		tax = tax.toFixed(2);
		stax = stax.toFixed(2);
		jQuery('#invoice_vat').val(tax);
		jQuery('#invoice_svat').val(stax);
		if(jQuery('#invoice_svat').length) {
			var total = +sub + +tax + +stax;
		} else {
			var total = +sub + +tax;
		}
		total = total.toFixed(2);
		jQuery('#invoice_total').val(total);
}