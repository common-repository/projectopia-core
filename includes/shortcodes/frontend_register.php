<?php
// Frontend Quote Form
function pto_frontend_register_form() {
	wp_enqueue_script( 'jquery' );
	add_action( 'wp_footer', 'pto_register_form_scripts', 99 );
	$code = '<div id="cqpim_frontend_form_cont">';
	$code .= '<form id="cqpim_frontend_form_register">';
	$code .= '<div style="padding-bottom:12px" class="cqpim_form_item">';
	$code .= '<label style="display:block; padding-bottom:5px" for="full_name">' . esc_html__('Full Name', 'projectopia-core') . ' <span style="color:#F00">*</span></label>';
	$code .= '<input style="width:100%" type="text" id="full_name" required />';
	$code .= '</div>';
	$code .= '<div style="padding-bottom:12px" class="cqpim_form_item">';
	$code .= '<label style="display:block; padding-bottom:5px" for="company_name">' . esc_html__('Company Name', 'projectopia-core') . ' <span style="color:#F00">*</span></label>';
	$code .= '<input style="width:100%" type="text" id="company_name" required />';
	$code .= '</div>';
	$code .= '<div style="padding-bottom:12px" class="cqpim_form_item">';
	$code .= '<label style="display:block; padding-bottom:5px" for="address">' . esc_html__('Address', 'projectopia-core') . ' <span style="color:#F00">*</span></label>';
	$code .= '<textarea style="width:100%; height:140px" id="address" required></textarea>';
	$code .= '</div>';
	$code .= '<div style="padding-bottom:12px" class="cqpim_form_item">';
	$code .= '<label style="display:block; padding-bottom:5px" for="postcode">' . esc_html__('Postcode', 'projectopia-core') . ' <span style="color:#F00">*</span></label>';
	$code .= '<input style="width:100%" type="text" id="postcode" required />';
	$code .= '</div>';
	$code .= '<div style="padding-bottom:12px" class="cqpim_form_item">';
	$code .= '<label style="display:block; padding-bottom:5px" for="telephone">' . esc_html__('Telephone', 'projectopia-core') . ' <span style="color:#F00">*</span></label>';
	$code .= '<input style="width:100%" type="text" id="telephone" required />';
	$code .= '</div>';
	$code .= '<div style="padding-bottom:12px" class="cqpim_form_item">';
	$code .= '<label style="display:block; padding-bottom:5px" for="email">' . esc_html__('Email Address', 'projectopia-core') . ' <span style="color:#F00">*</span></label>';
	$code .= '<input style="width:100%" type="email" id="email" required />';
	$code .= '</div>';
	$tc = get_option('gdpr_tc_page_check');
	$pp = get_option('gdpr_pp_page_check');
	$tcp = get_option('gdpr_tc_page');
	$ppp = get_option('gdpr_pp_page');
	if ( ! empty($tc) ) {
		$code .= '<div style="padding-bottom:12px" class="cqpim_form_item">';
		$link = '<a href="' . get_the_permalink($tcp) . '" target="_blank">' . esc_html__('I have read and accept the Terms & Conditions', 'projectopia-core') . '</a> <span style="color:#F00">*</span>';
		$code .= '<input type="checkbox" id="tc_conf" name="tc_conf" required /> ' . $link;
		$code .= '</div>';
	}
	if ( ! empty($pp) ) {
		$code .= '<div style="padding-bottom:12px" class="cqpim_form_item">';
		$link = '<a href="' . get_the_permalink($ppp) . '" target="_blank">' . esc_html__('I have read and accept the Privacy Policy', 'projectopia-core') . '</a> <span style="color:#F00">*</span>';
		$code .= '<input type="checkbox" id="tc_conf" name="tc_conf" required /> ' . $link;
		$code .= '</div>';
	}
	if ( empty( get_option('pto_frontend_form_google_recaptcha') ) ) {
		$code .= '<div style="padding-bottom:12px" class="cqpim_form_item">' . esc_attr__( 'I am a Human (SPAM check)', 'projectopia-core' ) . '<span style="color:#F00">*</span> <input type="checkbox" id="human_conf" required /></div>';
	} else {
		$code .= '<div class="g-recaptcha" style="margin-bottom:10px;" data-sitekey="' . esc_attr( get_option('google_recaptcha_site_key') ) . '"></div>';
	}
	$code .= '<input type="submit" id="cqpim_submit_frontend_register" value="' . esc_attr__('Register', 'projectopia-core') . '" /><br /><div id="form_spinner" style="clear:both; display:none; background:url(' . PTO_PLUGIN_URL . '/img/ajax-loader.gif) center center no-repeat; width:16px; height:16px; padding:10px 0 0 5px; margin-top:15px"></div>';
	$code .= '<div style="margin-top:20px" id="cqpim_submit_frontend_messages"></div>';
	$code .= '</form>';
	$code .= '</div>';
	return $code;
}
add_shortcode('cqpim_registration_form', 'pto_frontend_register_form');
function pto_register_form_scripts() {
	?>
	<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('#cqpim_frontend_form_register').on('submit', function(e) {
				e.preventDefault();
				var spinner = jQuery('#form_spinner');
				var name = jQuery('#full_name').val();
				var company = jQuery('#company_name').val();
				var address = jQuery('#address').val();
				var postcode = jQuery('#postcode').val();
				var telephone = jQuery('#telephone').val();
				var email = jQuery('#email').val();
				/** Verify google recaptcha for frontend register form */
				<?php if ( ! empty( get_option('pto_frontend_form_google_recaptcha') ) ) { ?>
					var g_captacha_response = grecaptcha.getResponse();
					if ( g_captacha_response.length == 0 ) {
						if ( ! jQuery('.g-recaptcha-error').length ) {
							jQuery('.g-recaptcha').before('<p class="g-recaptcha-error" style="color:red;margin:0;">Please verify this Google reCaptcha !</p>');
						}
						e.preventDefault();
						return false;
					}
				<?php } ?>

				var data = {
					'action' : 'pto_frontend_register_submission',
					'pto_nonce' : '<?php echo esc_js( wp_create_nonce( PTO_GLOBAL_NONCE ) ); ?>',
					'name' : name,
					'company' : company,
					'address' : address,
					'postcode' : postcode,
					'telephone' : telephone,
					'email' : email,
					<?php if ( ! empty( get_option('pto_frontend_form_google_recaptcha') ) ) { ?>
					'g_captacha_response' : g_captacha_response,
					<?php } ?>
				};
				jQuery.ajax({
					url: '<?php echo esc_url( admin_url() ) . 'admin-ajax.php'; ?>',
					data: data,
					type: 'POST',
					dataType: 'json',
					beforeSend: function(){
						// show spinner
						spinner.show();
						// disable form elements while awaiting data
						jQuery('#cqpim_submit_frontend').prop('disabled', true);
					},
				}).always(function(response) {
					console.log(response);
				}).done(function(response){
					if(response.error == true) {
						spinner.hide();
						// re-enable form elements so that new enquiry can be posted
						jQuery('#cqpim_submit_frontend').prop('disabled', false);
						jQuery('#cqpim_submit_frontend_messages').html(response.message);
					} else {
						spinner.hide();
						// re-enable form elements so that new enquiry can be posted
						jQuery('#cqpim_submit_frontend').prop('disabled', false);
						jQuery('#cqpim_submit_frontend_messages').html(response.message);
					}
				});
			});
		});
	</script>	
	<?php
}