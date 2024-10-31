<?php
// Frontend Quote Form
function pto_frontend_quote_form() {
	add_action( 'wp_footer', 'pto_frontend_form_scripts', 50 );
	wp_enqueue_script('pto_form_upload');
	wp_localize_script('pto_form_upload', 'localisation', pto_return_localisation());
	global $post;
	update_option('cqpim_form_page', $post->ID, true);
	$form = get_option('cqpim_frontend_form');
	$form_data = get_post_meta($form, 'builder_data', true);
	if ( is_array($form_data) ) {
		$form_data = '';
	}
	$form_data = json_decode($form_data);
	$fields = $form_data;
	$code = '<div id="cqpim_frontend_form_cont">';
	$code .= '<form id="cqpim_frontend_form">';
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
	if ( ! empty($fields) ) {
		$i = 0;
		foreach ( $fields as $field ) {
			$id = isset( $field->name ) ? strtolower( $field->name ) : '';
			$id = str_replace(' ', '_', $id);
			$id = str_replace('-', '_', $id);
			$id = preg_replace('/[^\w-]/', '', $id);
			if ( ! empty($field->required) && $field->required == 1 ) {
				$required = 'required';
				$ast = ' <span style="color:#F00">*</span>';
			} else {
				$required = '';
				$ast = '';
			}
			$p_class_name = ! empty($field->className) ? $field->className : "";
			$code .= '<div style="padding-bottom:12px" class="cqpim_form_item">';
			if ( $field->type != 'header' ) {
				$code .= '<label style="display:block; padding-bottom:5px" for="' . esc_attr( $id ) . '">' . esc_html( $field->label ) . ' ' . wp_kses_post( $ast ) . '</label>';
			}
			if ( $field->type == 'header' ) {
				$code .= '<' . esc_attr( $field->subtype ) . ' class="' . esc_attr( $p_class_name ) . '">' . esc_html( $field->label ) . '</' . esc_attr( $field->subtype ) . '>';
			} elseif ( $field->type == 'text' ) {
				$code .= '<input style="width:100%" class="' . esc_attr( $p_class_name ) . '" type="text" id="' . esc_attr( $id ) . '" ' . esc_attr( $required ) . ' />';
			} elseif ( $field->type == 'website' ) {
				$code .= '<input style="width:100%" class="' . esc_attr( $p_class_name ) . '" type="url" id="' . esc_attr( $id ) . '" ' . esc_attr( $required ) . ' />';
			} elseif ( $field->type == 'number' ) {
				$code .= '<input style="width:100%" class="' . esc_attr( $p_class_name ) . '" type="number" id="' . esc_attr( $id ) . '" ' . esc_attr( $required ) . ' />';
			} elseif ( $field->type == 'textarea' ) {
				$code .= '<textarea class="' . esc_attr( $p_class_name ) . '" style="width:100%; height:140px" id="' . esc_attr( $id ) . '" ' . esc_attr( $required ) . '></textarea>';
			} elseif ( $field->type == 'date' ) {
				$code .= '<input class="' . esc_attr( $p_class_name ) . '" style="width:100%" type="date" id="' . esc_attr( $id ) . '" ' . esc_attr( $required ) . ' />';
			} elseif ( $field->type == 'email' ) {
				$code .= '<input class="' . esc_attr( $p_class_name ) . '" style="width:100%" type="email" id="' . esc_attr( $id ) . '" ' . esc_attr( $required ) . ' />';
			} elseif ( $field->type == 'file' ) {  
				$multiple = isset($field->multiple) ? 'multiple' : '';
				$code .= '<input type="file" class="cqpim-file-upload-form" name="async-upload" id="' . esc_attr( $id ) . '" ' . esc_attr( $multiple ) . '/>';
				$code .= '<div id="upload_messages_' . esc_attr( $id ) . '"></div>';
				$code .= '<input type="hidden" name="image_id" id="upload_' . esc_attr( $id ) . '">';
				$code .= '<div class="clear"></div>';
			} elseif ( $field->type == 'checkbox-group' ) {
				$options = $field->values;
				foreach ( $options as $option ) {
					$code .= '<input class="' . esc_attr( $p_class_name ) . '" type="checkbox" value="' . esc_attr( $option->value ) . '" name="' . esc_attr( $id ) . '" /> ' . esc_html( $option->label ) . '<br />';
				}
			} elseif ( $field->type == 'radio-group' ) {
				$options = $field->values;
				foreach ( $options as $option ) {
					$code .= '<input class="' . esc_attr( $p_class_name ) . '" type="radio" value="' . esc_attr( $option->value ) . '" name="' . esc_attr( $id ) . '" ' . esc_attr( $required ) . ' /> ' . esc_html( $option->label ) . '<br />';
				}
			} elseif ( $field->type == 'select' ) {
				$options = $field->values;
				$code .= '<select class="' . esc_attr( $p_class_name ) . '" id="' . esc_attr( $id ) . '" ' . esc_attr( $required ) . '>';
					foreach ( $options as $option ) {
						$code .= '<option value="' . esc_attr( $option->value ) . '">' . esc_html( $option->label ) . '</option>';
					}
				$code .= '</select>';
			}
			if ( ! empty($field->other) && $field->other == 1 ) {
				$code .= '<br />';
				$code .= __('Other:', 'projectopia-core') . '<input style="width:100%" type="text" id="' . esc_attr( $id ) . '_other" />';
			}
			if ( ! empty($field->description) ) {
				$code .= '<p>' . wp_kses_post( $field->description ) . '</p>';
			}
			$code .= '</div>';
			$i++;
		}
	} else {
		$code .= '';
	}
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
	$code .= '<input type="submit" id="cqpim_submit_frontend" value="' . esc_attr__('Submit Quote Request', 'projectopia-core') . '" /><br /><div id="form_spinner" style="clear:both; display:none; background:url(' . PTO_PLUGIN_URL . '/img/ajax-loader.gif) center center no-repeat; width:16px; height:16px; padding:10px 0 0 5px; margin-top:15px"></div>';
	$code .= wp_nonce_field('image-submission', '_wpnonce', true, false);
	$code .= '<div style="margin-top:20px" id="cqpim_submit_frontend_messages"></div>';
	$code .= '</form>';
	$code .= '</div>';
	return $code;
}
add_shortcode('cqpim_frontend_form', 'pto_frontend_quote_form');
function pto_frontend_form_scripts() {
	$form = get_option('cqpim_frontend_form');
	$form_data = get_post_meta($form, 'builder_data', true);
	if ( empty($form_data) ) {
		$form_data = '';
	}
	$form_data = json_decode($form_data);
	$fields = $form_data;
	if ( empty($fields) ) {
		$fields = array();
	}
	?>
	<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('#cqpim_frontend_form').on('submit', function(e) {
				e.preventDefault();
				/** Verify google recaptcha for frontend quote */
				<?php if ( ! empty( get_option('pto_frontend_form_google_recaptcha') ) ) { ?>
					var g_captacha_response = grecaptcha.getResponse();
					if( g_captacha_response.length == 0 ) {
						if( ! jQuery('.g-recaptcha-error').length ) {
							jQuery('.g-recaptcha').before('<p class="g-recaptcha-error" style="color:red;margin:0;">Please verify you are human !</p>');
							setTimeout(function() { 
								jQuery('.g-recaptcha-error').remove(); 
							}, 3000); 
						}
						e.preventDefault();
						return false;
					}
				<?php } ?>
				var spinner = jQuery('#form_spinner');
				var name = jQuery('#full_name').val();
				var company = jQuery('#company_name').val();
				var address = jQuery('#address').val();
				var postcode = jQuery('#postcode').val();
				var telephone = jQuery('#telephone').val();
				var email = jQuery('#email').val();
				<?php
				foreach ( $fields as $field ) {
					$id = isset( $field->name ) ? strtolower( $field->name ) : '';
					$id = str_replace(' ', '_', $id);
					$id = str_replace('-', '_', $id);
					$id = preg_replace('/[^\w-]/', '', $id);
					if ( $field->type != 'header' ) {
						if ( $field->type == 'text' || 
							$field->type == 'number' || 
							$field->type == 'email' || 
							$field->type == 'textarea' ||
							$field->type == 'website' || 
							$field->type == 'select' || 
							$field->type == 'date' || 
							$field->type == 'number' ) {
							echo 'var ' . esc_attr( $id ) . ' = jQuery("#' . esc_attr( $id ) . '").val();';
							echo 'if(!' . esc_attr( $id ) . ') { ' . esc_attr( $id ) . ' = ""; };';
						} elseif ( $field->type == 'checkbox-group' ) {
							echo 'var ' . esc_attr( $id ) . ' = jQuery("input[name=' . esc_attr( $id ) . ']:checked").map(function() { return jQuery(this).val(); }).get();';
							echo 'if(!' . esc_attr( $id ) . ') { ' . esc_attr( $id ) . ' = ""; };';
						} elseif ( $field->type == 'radio-group' ) {
							echo 'var ' . esc_attr( $id ) . ' = jQuery("input[name=' . esc_attr( $id ) . ']:checked").val();';
							echo 'if(!' . esc_attr( $id) . ') { ' . esc_attr( $id ) . ' = ""; };';
						} elseif ( $field->type == 'file' ) {
							echo 'var ' . esc_attr( $id ) . ' = jQuery("#upload_' . esc_attr( $id ) . '").val();';
							echo 'if(!' . esc_attr( $id ) . ') { ' . esc_attr( $id ) . ' = ""; };';
						}
						if ( ! empty($field->other) && $field->other == 1 ) {
							echo 'var ' . esc_attr( $id ) . '_other = jQuery("#' . esc_attr( $id ) . '_other").val();';
						}
					}
				}
				?>
				var data = {
					'action' : 'pto_frontend_quote_submission',
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
					<?php
					foreach ( $fields as $field ) {
						$id = isset( $field->name ) ? strtolower( $field->name ) : '';
						$id = str_replace(' ', '_', $id);
						$id = str_replace('-', '_', $id);
						$id = preg_replace('/[^\w-]/', '', $id);
							if ( $field->type != 'header' ) {
								if ( $field->type == 'file' ) {
									echo "'cqpimuploader_" . esc_attr( $id ) . "' : " . esc_attr( $id ) . ",";
								} else {
									echo "'" . esc_attr( $id ) . "' : " . esc_attr( $id ) . ",";
								}
							if ( ! empty($field->other) && $field->other == 1 ) {
								echo "'" . esc_attr( $id ) . "_other' : " . esc_attr( $id ) . "_other,";
							}
						}
					}
					?>
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
					<?php if ( ! empty( get_option('pto_frontend_form_google_recaptcha') ) ) { ?>
						grecaptcha.reset();
					<?php } ?>
					if(response.error == true) {
						spinner.hide();
						// re-enable form elements so that new enquiry can be posted
						jQuery('#cqpim_submit_frontend').prop('disabled', false);
						jQuery('#cqpim_submit_frontend_messages').html(response.message);
					} else {
						/** Clear form after submission */
						jQuery('#cqpim_frontend_form').trigger("reset");
						jQuery('.cqpim-file-upload-form').show();
						jQuery('#upload_messages_upload_files_related_to_your_project').remove();
						jQuery('#upload_messages_file_upload').remove();

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