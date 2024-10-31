jQuery(document).ready(function() {
	jQuery('#tabs').show().tabs();
	jQuery('.timepicker').timepicker({ 'scrollDefault': 'now' });
	jQuery('#create_linked_team').on('click', function(e) {
		e.preventDefault();
		var user_id = jQuery(this).data('uid');
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_create_team_from_admin',
			'user_id' : user_id,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#create_linked_team').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#create_linked_team').prop('disabled', false);
				alert(localisation.teams.link_error);
			} else {
				spinner.hide();
				jQuery('#create_linked_team').prop('disabled', false);
				location.reload();
			}
		});	
	});
	jQuery('.cancel-colorbox').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox.close();
	})
	jQuery('#reset-cqpim').on('click', function(e){
		e.preventDefault();
			jQuery.colorbox({
				'maxWidth':'95%',
				'inline': true,
				'href': '#reset_cqpim',							
				'opacity': '0.5',	
			});	
			jQuery.colorbox.resize();	
	});
	jQuery('.reset-cqpim-conf').on('click', function(e) {
		e.preventDefault();
		jQuery.colorbox.close();
		var data = {
			'action' : 'pto_remove_all_data',
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				jQuery('.reset-cqpim-conf').prop('disabled', true);
			},
		}).done(function(response){
			location.replace(response.redirect);
		});	
	});
	jQuery('.remove_logo').on('click', function(e) {
		e.preventDefault();
		var type = jQuery(this).data('type');		
		var data = {
			'action' : 'pto_remove_logo',
			'type' : type,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				jQuery('#cqpim_overlay').show();
			},
		}).done(function(response){
			location.reload()
		});	
	});
	jQuery('#cqpim_invoice_template').on('change', function(e) {
		var value = jQuery(this).val();		
		if ( value == 1 ) {
			jQuery('.cqpim-clean-main-colour, .cqpim-cool-main-colour').hide();
		} else if ( value == 2 ) {
			jQuery('.cqpim-cool-main-colour').hide();
			jQuery('.cqpim-clean-main-colour').show();
		} else if ( value == 3 ) {
			jQuery('.cqpim-clean-main-colour').hide();
			jQuery('.cqpim-cool-main-colour').show();
		}
	});
	jQuery('#cqpim_invoice_template').change();
	jQuery('.pto_update_details').on('click', function(e) {
		e.preventDefault();
		var team = jQuery('#team_id').val();
		var type = jQuery(this).data('type');
		var name = jQuery('#team_name').val();
		var email = jQuery('#team_email').val();
		var phone = jQuery('#team_telephone').val();
		var job = jQuery('#team_job').val();
		var photo = jQuery('#upload_attachment_ids').val();
		var password = jQuery('#password').val();
		var password2 = jQuery('#password2').val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_update_team_profile',
			'team' : team,
			'type' : type,
			'name' : name,
			'email' : email,
			'phone' : phone,
			'job' : job,
			'photo' : photo,
			'password' : password,
			'password2' : password2,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('.pto_update_details').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				alert(response.message);
				jQuery('.pto_update_details').prop('disabled', false);
			} else {
				spinner.hide();
				alert(response.message);
				jQuery('.pto_update_details').prop('disabled', false);
				location.reload();
			}
		});	
	});
	jQuery('.pto_remove_current_photo').on('click', function(e) {
		e.preventDefault();
		var team = jQuery('#team_id').val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_remove_current_photo',
			'team' : team,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('.pto_remove_current_photo').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				alert(response.message);
				jQuery('.pto_remove_current_photo').prop('disabled', false);
			} else {
				spinner.hide();
				alert(response.message);
				jQuery('.pto_remove_current_photo').prop('disabled', false);
				location.reload();
			}
		});	
	});
	jQuery('.cqpim_wc_repeater').repeater();
	
	
	
	
	
	jQuery('#add_rec_inv').click(function(e) {
		e.preventDefault();
		var client_id = jQuery(this).val();
		var title = jQuery('#rec-inv-title').val();
		var start = jQuery('#rec-inv-start').val();
		var end = jQuery('#rec-inv-end').val();
		var frequency = jQuery('#rec-inv-frequency').val();
		var status = jQuery('#rec-inv-status').val();
		var contact = jQuery('#client_contact_select').val();
		var spinner = jQuery('#cqpim_overlay');
		var items = jQuery('input[name^="ngroup-a"]').map(function(){return jQuery(this).val();}).get();
		if(jQuery('#rec-inv-auto').is(':checked')) {
			auto = 1;
		} else {
			auto = 0
		}
		if(jQuery('#rec-inv-partial').is(':checked')) {
			partial = 1;
		} else {
			partial = 0
		}
		var data = {
			'action' : 'pto_add_new_recurring_invoice',
			'client_id' : client_id,
			'title' : title,
			'start' : start,
			'end' : end,
			'frequency' : frequency,
			'status' : status,
			'contact' : contact,
			'auto' : auto,
			'items' : items,
			'partial' : partial,
			'pto_nonce' : localisation.global_nonce
		};
		if(title && frequency) {
			jQuery.ajax({
				url: ajaxurl,
				data: data,
				type: 'POST',
				dataType: 'json',
				beforeSend: function(){
					spinner.show();
					jQuery('#add_rec_inv').prop('disabled', true);
					jQuery.colorbox.resize();
				},
			}).always(function(response){
				console.log(response);
			}).done(function(response){
				if(response.error == true) {
					spinner.hide();
					jQuery('#add_rec_inv').prop('disabled', false);
					jQuery('.rec-inv-messages').html(response.message);
					jQuery.colorbox.resize();
				} else {
					spinner.hide();
					jQuery('#add_rec_inv').prop('disabled', false);
					jQuery('.rec-inv-messages').html(response.message);
					jQuery.colorbox.resize();
					location.reload();
				}
			});
		} else {
			jQuery('.rec-inv-messages').html('<div class="cqpim-alert cqpim-alert-danger alert-display">You must enter a title and a frequency.</div>');
			jQuery.colorbox.resize();
		}
	});
	// Delete Rec Invoice
	jQuery('.delete_rec').click(function(e) {
		e.preventDefault();
		var client_id = jQuery(this).data('client');
		var key = jQuery(this).val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_delete_recurring_invoice',
			'client_id' : client_id,
			'key' : key,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('.delete_task').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
			} else {
				location.reload();
			}
		});
	});
	// Open Edit Rec Inv Colorbox
	jQuery('.edit_rec').click(function(e) {
		e.preventDefault();
		var key = jQuery(this).val();
		jQuery.colorbox({
			'maxWidth':'95%',
			'inline': true,
			'fixed': true,
			'href': '#edit-recurring-invoice-' + key,	
			'opacity': '0.5',
		});	
	});	
	// Edit Rec Inv
	jQuery('.edit-rec-inv-btn').click(function(e) {
		e.preventDefault();
		var key = jQuery(this).data('key');
		var invoice_key = jQuery(this).data('invoice-key');
		var client_id = jQuery(this).val();
		var title = jQuery('#rec-inv-title-' + key).val();
		var start = jQuery('#rec-inv-start-' + key).val();
		var end = jQuery('#rec-inv-end-' + key).val();
		var frequency = jQuery('#rec-inv-frequency-' + key).val();
		var status = jQuery('#rec-inv-status-' + key).val();
		var contact = jQuery('#client_contact_select_' + key).val();
		var spinner = jQuery('#cqpim_overlay');
		var items = jQuery('input[name^="group' + key + '-a"]').map(function(){return jQuery(this).val();}).get();
		if(jQuery('#rec-inv-auto-' + key).is(':checked')) {
			auto = 1;
		} else {
			auto = 0
		}
		if(jQuery('#rec-inv-partial-' + key).is(':checked')) {
			partial = 1;
		} else {
			partial = 0
		}
		var data = {
			'action' : 'pto_edit_recurring_invoice',
			'key' : invoice_key,
			'client_id' : client_id,
			'title' : title,
			'start' : start,
			'end' : end,
			'frequency' : frequency,
			'status' : status,
			'contact' : contact,
			'auto' : auto,
			'items' : items,
			'partial' : partial,
			'pto_nonce' : localisation.global_nonce
		};
		if(title && frequency) {
			jQuery.ajax({
				url: ajaxurl,
				data: data,
				type: 'POST',
				dataType: 'json',
				beforeSend: function(){
					spinner.show();
					jQuery('.edit-rec-inv-btn').prop('disabled', true);
					jQuery.colorbox.resize();
				},
			}).done(function(response){
				if(response.error == true) {
					spinner.hide();
					jQuery('.edit-rec-inv-btn').prop('disabled', false);
					jQuery('.edit-inv-messages').html(response.message);
					jQuery.colorbox.resize();
				} else {
					spinner.hide();
					jQuery('.edit-rec-inv-btn').prop('disabled', false);
					jQuery('.edit-inv-messages').html(response.message);
					jQuery.colorbox.resize();
					location.reload();
				}
			});
		} else {
			jQuery('.rec-inv-messages').html('<div class="cqpim-alert cqpim-alert-danger alert-display">You must enter a title and a frequency.</div>');
			jQuery.colorbox.resize();
		}
	});


	// Initial settings multipage form steps.
	var stepsForm = jQuery('#initial-settings-form');
	if ( stepsForm.length > 0 ) {
		//Warn user if they try to refresh the page.
		window.onbeforeunload = function() { return "Your work will be lost."; };
		stepsForm.validate( {
			errorPlacement: function errorPlacement(error, element) { element.before(error); }
		});
	}

	var stepsWizard = jQuery('#wizard');
	if ( stepsWizard.length > 0 ) {
		jQuery( stepsWizard ).steps({
			headerTag : 'h4',
			bodyTag : 'section',
			transitionEffect: 'fade',
			enableAllSteps: true,
			transitionEffectSpeed: 300,
			onStepChanging: function (event, currentIndex, newIndex) { 
				if ( newIndex === 1 ) {
					jQuery('.steps ul').addClass('step-2');
				} else {
					jQuery('.steps ul').removeClass('step-2');
				}
				if ( newIndex === 2 ) {
					if ( stepsForm.length ) {
						stepsForm.validate().settings.ignore = ":disabled,:hidden";
						return stepsForm.valid();
					}
					jQuery('.steps ul').addClass('step-3');
				} else {
					jQuery('.steps ul').removeClass('step-3');
				}
				if ( newIndex === 3 ) {
					jQuery('.steps ul').addClass('step-4');
				} else {
					jQuery('.steps ul').removeClass('step-4');
				}
				if ( newIndex === 4 ) {
					jQuery('.steps ul').addClass('step-5');
				} else {
					jQuery('.steps ul').removeClass('step-5');
				}
				return true; 
			},
			onFinished: function (event, currentIndex) {
				event.preventDefault();
				var spinner = jQuery('#cqpim_overlay');
				// Company details.
				var company_name      = jQuery('input[name=company_name]').val();
				var company_address   = jQuery('textarea[name=company_address]').val();
				var company_postcode  = jQuery('input[name=company_postcode]').val();
				var company_telephone = jQuery('input[name=company_telephone]').val();

				// Company emails.
				var company_sales_email    = jQuery('input[name=company_sales_email]').val();
				var company_accounts_email = jQuery('input[name=company_accounts_email]').val();
				var company_support_email  = jQuery('input[name=company_support_email]').val();

				// Finance details
				var currency_symbol          = jQuery('input[name=currency_symbol]').val();
				var currency_symbol_position = jQuery('select[name=currency_symbol_position]').val();
				var currency_code            = jQuery('select[name=currency_code]').val();

				//Sales tax details
				var sales_tax_rate = jQuery('input[name=sales_tax_rate]').val();
				var sales_tax_name = jQuery('input[name=sales_tax_name]').val();
				var sales_tax_reg  = jQuery('input[name=sales_tax_reg]').val();

				//Payment gateway details
				// Stripe details
				var client_invoice_stripe_key    = jQuery('input[name=client_invoice_stripe_key]').val();
				var client_invoice_stripe_secret = jQuery('input[name=client_invoice_stripe_secret]').val();
				var client_invoice_stripe_ideal  = 0;

				if ( jQuery('input[name=client_invoice_stripe_ideal]').prop("checked") == true ) {
					client_invoice_stripe_ideal = 1;
				}

				//Paypal details.
				var client_invoice_paypal_address = jQuery('input[name=client_invoice_paypal_address]').val();
				var cqpim_paypal_api_username     = jQuery('input[name=cqpim_paypal_api_username]').val();
				var cqpim_paypal_api_password     = jQuery('input[name=cqpim_paypal_api_password]').val();
				var cqpim_paypal_api_signature    = jQuery('input[name=cqpim_paypal_api_signature]').val();

				var data = {
					'action' : 'pto_save_initial_settings_options',
					'pto_nonce' : localisation.global_nonce,
					
					'company_name'      : company_name,
					'company_address'   : company_address,
					'company_postcode'  : company_postcode,
					'company_telephone' : company_telephone,

					'company_sales_email'    : company_sales_email,
					'company_accounts_email' : company_accounts_email,
					'company_support_email'  : company_support_email,

					'currency_symbol'         : currency_symbol,
					'currency_symbol_position': currency_symbol_position,
					'currency_code'           : currency_code,

					'sales_tax_rate' : sales_tax_rate,
					'sales_tax_name' : sales_tax_name,
					'sales_tax_reg'  : sales_tax_reg,

					'client_invoice_stripe_key'    : client_invoice_stripe_key,
					'client_invoice_stripe_secret' : client_invoice_stripe_secret,
					'client_invoice_stripe_ideal'  : client_invoice_stripe_ideal,

					'client_invoice_paypal_address' : client_invoice_paypal_address,
					'cqpim_paypal_api_username'     : cqpim_paypal_api_username,
					'cqpim_paypal_api_password'     : cqpim_paypal_api_password,
					'cqpim_paypal_api_signature'    : cqpim_paypal_api_signature
				};

				jQuery.ajax({
					url        : ajaxurl,
					data       : data,
					type       : 'POST',
					dataType   : 'json',
					beforeSend : function(){
						spinner.show();
					},
				}).done(function(response){
					if( response.error == true ) {
						spinner.hide();
						alert( response.message );
					} else {
						alert( response.message );
						window.onbeforeunload = null;
						window.location.href = response.redirect;
					}
				});

			},

			labels: {
				finish: "Save & Open Dashboard",
				next: "Next",
				previous: "Previous"
			}
		});
	}
	if ( jQuery('.pto-modal-wrapper').length ) {
		jQuery('body').css('overflow-y','hidden');
	}

    // Custom Steps Jquery Steps
    jQuery('.wizard > .steps li a').click(function(){
		if	( jQuery('.wizard > .steps li.error').length == 0 ) {
			jQuery(this).parent().addClass('checked');
		}
		jQuery(this).parent().prevAll().addClass('checked');
		jQuery(this).parent().nextAll().removeClass('checked');
    });

    // Custom Button Jquery Steps
    jQuery('.forward').click(function(){
    	jQuery("#wizard").steps('next');
    })
    jQuery('.backward').click(function(){
        jQuery("#wizard").steps('previous');
    })

    // Checkbox
    jQuery('.checkbox-circle label').click(function(){
        jQuery('.checkbox-circle label').removeClass('active');
        jQuery(this).addClass('active');
    })

    // Payment option.
    if ( jQuery("#pto-stripe").is(":checked") ) {
        jQuery('.pto-payment-gateway-paypal').hide();
    } else {
        jQuery('.pto-payment-gateway-stripe').hide();
    }

    jQuery('#pto-stripe').click(function(){
        jQuery('.pto-payment-gateway-paypal').hide();
        jQuery('.pto-payment-gateway-stripe').show();
    })

    jQuery('#pto-paypal').click(function(){
        jQuery('.pto-payment-gateway-stripe').hide();
        jQuery('.pto-payment-gateway-paypal').show();
	})

	// Cancel initial setup wizard.
	jQuery('#close-intial-popup').click(function() {
		var data = {
			'action' : 'pto_initial_setup_popup_cancel',
			'pto_nonce' : localisation.global_nonce
		}
		jQuery.ajax({
			url        : ajaxurl,
			data       : data,
			type       : 'POST',
			dataType   : 'json',
		}).done(function(response){
			console.log(response);
			window.location.href = response.redirect;
		});
	})

	/** Store settings tab id when click. */
	jQuery( 'a.ui-tabs-anchor' ).on( 'click', function(e) {
		localStorage.setItem('settings_active_tab', jQuery(e.target).attr('href'));
	} );
	var settings_active_tab = localStorage.getItem( 'settings_active_tab' );
	/** if tabs with id in url then active that tab. */
	var tab_in_url = jQuery(location).attr("href").split('#').pop();
	if ( tab_in_url.split('-')[0] === 'tabs' ) {
		settings_active_tab = '#' + tab_in_url;
	}
	if ( settings_active_tab ) {
        jQuery( 'a[href="' + settings_active_tab + '"]' ).click();
	}

	/** Toggle the checkbox of unknown email support and reject email */
	jQuery('input[name="cqpim_create_support_on_unknown_email"],input[name="cqpim_send_piping_reject"]').click(function(){
		var cqpim_send_piping_reject = jQuery('input[name="cqpim_send_piping_reject"]');
		var cqpim_create_support_on_unknown_email = jQuery('input[name="cqpim_create_support_on_unknown_email"]');
		if ( jQuery(this).is( cqpim_create_support_on_unknown_email ) ) {
			cqpim_create_support_on_unknown_email.prop('checked', true);
			cqpim_send_piping_reject.prop('checked', false);  
		} else if( jQuery(this).is( cqpim_send_piping_reject ) ) {
			cqpim_create_support_on_unknown_email.prop('checked', false);
			cqpim_send_piping_reject.prop('checked', true);  
		}
	});

	/** Checked all support tickets */
	jQuery('#suppot_ticket_all_check').click( function( e ) {
		jQuery('input:checkbox').not(this).prop('checked', this.checked);
	});

	/** Delete all checked support tickets */
	jQuery('#btn_delete_all_support_tickets').click(function( e ) {
		var support_ticket_ids = [];
		jQuery.each( jQuery( '.checkbox_support_ticket:checked' ), function() {
			support_ticket_ids.push(jQuery(this).val());
		} );
		var spinner = jQuery('#cqpim_overlay');

		var data = {
			'action' : 'pto_bulk_delete_support_tickets',
			'support_ticket_ids' : support_ticket_ids,
			'pto_nonce' : localisation.global_nonce
		};

		jQuery.ajax ( {
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function() {
				spinner.show();
				jQuery(this).prop('disabled', true);
			},
		}).always(function(response) {
			console.log(response);
		}).done(function(response) {
			if(response.error == true) {
				spinner.hide();
				jQuery(this).prop('disabled', false);
				alert(response.message);
			} else {
				alert( response.message );
				location.reload();
			}
		});
	});

});
