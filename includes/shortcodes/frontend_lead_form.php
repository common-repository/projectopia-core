<?php
// Frontend Quote Form
function pto_frontend_lead_form( $atts ) {
	$form_id = isset($atts['id']) ? $atts['id'] : '';
	if ( empty($form_id) ) {
		return __('You have not added a Form ID to the shortcode!', 'projectopia-core');
	}
	$form_type = get_post_meta($form_id, 'form_type', true);
	if ( ! empty($form_type) && $form_type == 'gf' ) {
		$gravity_form = get_post_meta($form_id, 'gravity_form', true);
		if ( empty($gravity_form) ) {
			return __('This form has been configured to use a Gravity Form, but you have not selected a Gravity Form to use.', 'projectopia-core');
		} else {
			return do_shortcode('[gravityform id="' . $gravity_form . '"]');
		}
		return;
	} elseif ( ! empty($form_type) && $form_type == 'cqpim' ) {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		pto_enqueue_all_frontend();
		wp_enqueue_script('pto_form_upload');
		wp_enqueue_script('pto_select2_scripts');
		wp_enqueue_style('pto_select2_styles');
		wp_enqueue_style('pto_fe_lead_form');
		wp_localize_script('pto_form_upload', 'localisation', pto_return_localisation());
		global $post;
		update_option('cqpim_form_page', $post->ID, true);
		$form_data = get_post_meta($form_id, 'builder_data', true);
		if ( is_array($form_data) ) {
			$form_data = '';
		}
		$form_data = json_decode($form_data);
		$fields = $form_data;
		$code = '<div id="cqpim_frontend_form_cont">';
		$code .= '<form id="cqpim_frontend_leadform">';
		if ( ! empty($fields) ) {
			$i = 0;
			foreach ( $fields as $field ) {
				$class = isset( $field->className ) ? $field->className : '';
				$id = isset( $field->name ) ? strtolower( $field->name ) : '';
				$id = str_replace( ' ', '_', $id );
				$id = str_replace( '-', '_', $id );
				$id = preg_replace( '/[^\w-]/', '', $id );
				if ( ! empty( $field->required) && $field->required == 1 ) {
					$required = 'required';
					$ast = ' <span style="color:#F00">*</span>';
				} else {
					$required = '';
					$ast = '';
				}
				$code .= '<input id="leadform_id" type="hidden" value="' . esc_attr( $form_id ) . '" />';
				$code .= '<div style="padding-bottom: 12px;" class="cqpim_form_item">';
				if ( $field->type != 'header' ) {
					$code .= '<label class="pto-field-label" style="display:block; padding-bottom:5px" for="' . esc_attr( $id ) . '">' . esc_html( $field->label ) . ' ' . wp_kses_post( $ast ) . '</label>';

				}
				if ( $field->type == 'header' ) {
					$code .= '<' . esc_attr( $field->subtype ) . ' class="' . esc_attr( $class ) . '">' . esc_html( $field->label ) . '</' . esc_attr( $field->subtype ) . '>';
				} elseif ( $field->type == 'text' ) {
					$code .= '<input style="width:100%" class="' . esc_attr( $class ) . '" type="text" id="' . esc_attr( $id ) . '" ' . esc_attr( $required ) . ' />';
				} elseif ( $field->type == 'website' ) {
					$code .= '<input style="width:100%" class="' . esc_attr( $class ) . '" type="url" id="' . esc_attr( $id ) . '" ' . esc_attr( $required ) . ' />';
				} elseif ( $field->type == 'number' ) {
					$code .= '<input style="width:100%" class="' . esc_attr( $class ) . '" type="number" id="' . esc_attr( $id ) . '" ' . esc_attr( $required ) . ' />';
				} elseif ( $field->type == 'textarea' ) {
					$code .= '<textarea class="' . esc_attr( $class ) . '" style="width:100%; height:140px" id="' . esc_attr( $id ) . '" ' . esc_attr( $required ) . '></textarea>';
				} elseif ( $field->type == 'date' ) {
					$code .= '<input class="' . esc_attr( $class ) . '" style="width:100%" type="date" id="' . esc_attr( $id ) . '" ' . esc_attr( $required ) . ' />';
				} elseif ( $field->type == 'email' ) {
					$code .= '<input class="' . esc_attr( $class ) . '" style="width:100%" type="email" id="' . esc_attr( $id ) . '" ' . esc_attr( $required ) . ' />';
				} elseif ( $field->type == 'file' ) {  
					$multiple = isset($field->multiple) ? 'multiple' : '';
					$code .= '<input type="file" class="cqpim-file-upload-form" name="async-upload" id="' . esc_attr( $id ) . '" ' . esc_attr( $multiple ) . '/>';
					$code .= '<div id="upload_messages_' . esc_attr( $id ) . '"></div>';
					$code .= '<input type="hidden" name="image_id" id="upload_' . esc_attr( $id ) . '">';
					$code .= '<div class="clear"></div>';
				} elseif ( $field->type == 'checkbox-group' ) {
					$options = $field->values;
					foreach ( $options as $option ) {
						$code .= '<input class="' . esc_attr( $class ) . '" type="checkbox" value="' . esc_attr( $option->value ) . '" name="' . esc_attr( $id ) . '" /> ' . esc_html( $option->label ) . '<br />';
					}
				} elseif ( $field->type == 'radio-group' ) {
					$options = $field->values;
					foreach ( $options as $option ) {
						$code .= '<input class="' . esc_attr( $class ) . '" type="radio" value="' . esc_attr( $option->value ) . '" name="' . esc_attr( $id ) . '" ' . esc_attr( $required ) . ' /> ' . esc_html( $option->label ) . '<br />';
					}
				} elseif ( $field->type == 'select' ) {						
					$multiple = isset( $field->multiple ) && $field->multiple == 1 ? 'multiple' : '';
					if($multiple) {
						$class .= ' pto-select2-field';
					}
					$options = $field->values;
					$code .= '<select '.esc_attr($multiple).' class="' . esc_attr( $class ) . '" id="' . esc_attr( $id ) . '" ' . esc_attr( $required ) . '>';
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
					$code .= '<p class="pto-field-desc">' . wp_kses_post( $field->description ) . '</p>';
				}
				$code .= '</div>';
				$i++;
			}
		} else {
			$code .= '';
		}
		if ( empty( get_option('pto_frontend_form_google_recaptcha') ) ) {
			$code .= '<div style="padding-bottom:12px" class="cqpim_form_item">' . esc_attr__( 'I am a Human (SPAM check)', 'projectopia-core' ) . '<span style="color:#F00">*</span> <input type="checkbox" id="human_conf" required /></div>';
		} else {
			$code .= '<div class="g-recaptcha" style="margin-bottom:10px;" data-sitekey="' . esc_attr( get_option('google_recaptcha_site_key') ) . '"></div>';
		}
		$code .= '<input type="submit" id="cqpim_submit_frontend" value="' . esc_attr__('Submit', 'projectopia-core') . '" /><br /><div id="form_spinner" style="clear:both; display:none; background:url(' . PTO_PLUGIN_URL . '/img/ajax-loader.gif) center center no-repeat; width:16px; height:16px; padding:10px 0 0 5px; margin-top:15px"></div>';
		$code .= wp_nonce_field('image-submission', '_wpnonce', true, false);
		$code .= '<div style="margin-top:20px" id="cqpim_submit_frontend_messages"></div>';
		$code .= '</form>';
		$code .= '</div>';
		$code .= pto_frontend_leadform_scripts($form_id);
		return $code;
	} else {
		return __('Form ID does not exist!', 'projectopia-core');
	}
}
add_shortcode('projectopia_lead_form', 'pto_frontend_lead_form');
//add_action( 'wp_footer', 'pto_frontend_leadform_scripts', 100 );
function pto_frontend_leadform_scripts( $form_id ) {
	$form_data = get_post_meta($form_id, 'builder_data', true);
	if ( empty($form_data) ) {
		$form_data = '';
	}
	$form_data = json_decode($form_data);
	$fields = $form_data;
	if ( empty($fields) ) {
		$fields = array();
	}
	ob_start();
	?>
	<script type="text/javascript">
		function leadScript(){
			jQuery(document).ready(function() {
				jQuery('#cqpim_frontend_leadform').on('submit', function(e) {
					e.preventDefault();
					/** Verify google recaptcha for frontend lead */
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
					var leadform_id = jQuery('#leadform_id').val();
					<?php
					foreach ( $fields as $field ) {
						$id = isset( $field->name ) ? strtolower( $field->name ) : '';
						$id = str_replace( ' ', '_', $id );
						$id = str_replace( '-', '_', $id );
						$id = preg_replace( '/[^\w-]/', '', $id );
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
								echo 'if(!' . esc_attr( $id ) . ') { ' . esc_attr( $id ) . ' = ""; };';
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
						'action' : 'pto_frontend_lead_submission',
						'leadform_id' : leadform_id,
						'pto_nonce' : '<?php echo esc_js( wp_create_nonce( PTO_GLOBAL_NONCE ) ); ?>',
						<?php if ( ! empty( get_option('pto_frontend_form_google_recaptcha') ) ) { ?>
						'g_captacha_response' : g_captacha_response,
						<?php } ?>
						<?php
						foreach ( $fields as $field ) {
							$id = isset( $field->name ) ? strtolower( $field->name ) : '';
							$id = str_replace( ' ', '_', $id );
							$id = str_replace( '-', '_', $id );
							$id = preg_replace( '/[^\w-]/', '', $id );
							if ( $field->type != 'header' ) {
								if ( $field->type == 'file' ) {
									echo "'cqpimuploader_" . esc_attr( $id ) . "' : " . esc_attr( $id ) . ",";
								} else {
									echo "'" . esc_attr( $id ) . "' : " . esc_attr( $id ) . ",";
								}
								if ( ! empty( $field->other ) && $field->other == 1 ) {
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
							spinner.show();
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
							jQuery('#cqpim_submit_frontend').prop('disabled', false);
							jQuery('#cqpim_submit_frontend_messages').html(response.message);
						} else {
							spinner.hide();
							jQuery('#cqpim_submit_frontend').prop('disabled', false);
							jQuery('#cqpim_submit_frontend_messages').html(response.message);
						}
					});
				});
			});
		}
		leadScript();
	</script>	
	<?php
	return ob_get_clean();
}