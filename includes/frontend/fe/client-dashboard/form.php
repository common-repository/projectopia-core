<div class="cqpim_block">
	<div class="cqpim_block_title">
		<div class="caption">
			<i class="fa fa-envelope font-green-sharp" aria-hidden="true"></i> <?php esc_html_e('Request a Quote', 'projectopia-core'); ?>
		</div>
	</div>
	<div class="cqpim-dash-item-inside">
		<?php 
		$user = wp_get_current_user();
		$client_logs = get_post_meta($assigned, 'client_logs', true);
		if ( empty($client_logs) ) {
			$client_logs = array();
		}
		$now = time();
		$client_logs[ $now ] = array(
			'user' => $user->ID,
			'page' => __('Client Dashboard Quotes Page', 'projectopia-core'),
		);
		update_post_meta($assigned, 'client_logs', $client_logs);
		?>
		<div class="cqpim-dash-item-inside">
			<?php
			$form = get_option( 'cqpim_backend_form' );
			$form_data = get_post_meta( $form, 'builder_data', true );
			if ( ! empty( $form_data ) ) {
				$form_data = json_decode( $form_data );
				$fields = $form_data;
			}
			echo '<form id="cqpim_backend_form">';
			if ( ! empty($fields) ) {
				echo '<div id="cqpim_backend_quote">';
				foreach ( $fields as $field ) {
					$n_id = isset( $field->name ) ? strtolower( $field->name ) : '';
					$n_id = str_replace( ' ', '_', $n_id );
					$n_id = str_replace( '-', '_', $n_id );
					$n_id = preg_replace( '/[^\w-]/', '', $n_id );
					if ( ! empty( $field->required ) && $field->required == 1 ) {
						$required = 'required';
						$ast = '<span style="color:#F00">*</span>';
					} else {
						$required = '';
						$ast = '';
					}
					echo '<div style="padding-bottom:12px" class="cqpim_form_item">';
					if ( $field->type != 'header' ) {
						echo '<label style="display:block; padding-bottom:5px" for="' . esc_attr( $n_id ) . '">' . esc_html( $field->label ) . ' ' . wp_kses_post( $ast ) . '</label>';
					}
					if ( $field->type == 'header' ) {
						echo '<' . esc_attr( $field->subtype ) . ' class="' . esc_attr( $field->className ) . '">' . esc_html( $field->label ) . '</' . esc_attr( $field->subtype ) . '>';
					} elseif ( $field->type == 'text' ) {
						echo '<input type="text" class="' . esc_attr( $field->className ) . '" id="' . esc_attr( $n_id ) . '" ' . esc_attr( $required ) . ' />';
					} elseif ( $field->type == 'website' ) {
						echo '<input type="url" class="' . esc_attr( $field->className ) . '" id="' . esc_attr( $n_id ) . '" ' . esc_attr( $required ) . ' />';
					} elseif ( $field->type == 'number' ) {
						echo '<input type="number" class="' . esc_attr( $field->className ) . '" id="' . esc_attr( $n_id ) . '" ' . esc_attr( $required ) . ' />';
					} elseif ( $field->type == 'textarea' ) {
						echo '<textarea class="' . esc_attr( $field->className ) . '" id="' . esc_attr( $n_id ) . '" ' . esc_attr( $required ) . '></textarea>';
					} elseif ( $field->type == 'date' ) {
						echo '<input class="' . esc_attr( $field->className ) . '" type="date" id="' . esc_attr( $n_id ) . '" ' . esc_attr( $required ) . ' />';
					} elseif ( $field->type == 'email' ) {
						echo '<input type="email" class="' . esc_attr( $field->className ) . '" id="' . esc_attr( $n_id ) . '" ' . esc_attr( $required ) . ' />';
					} elseif ( $field->type == 'file' ) {
						$multiple = isset($field->multiple) ? 'multiple' : '';
						echo '<input type="file" class="cqpim-file-upload-form" name="async-upload" id="' . esc_attr( $n_id ) . '" ' . esc_attr( $multiple ) . '/>';
						echo '<div id="upload_messages_' . esc_attr( $n_id ) . '"></div>';
						echo '<input type="hidden" name="image_id" id="upload_' . esc_attr( $n_id ) . '">';
						echo '<div class="clear"></div>';
					} elseif ( $field->type == 'checkbox' ) {
						if ( ! empty($field->toggle) && $field->toggle == true ) {
							echo '<input type="checkbox" toggle="true" class="' . esc_attr( $field->className ) . '" value="' . esc_attr( $option->value ) . '" name="' . esc_attr( $n_id ) . '" /> ' . esc_html( $option->label ) . '<br />';
						} else {
							echo '<input type="checkbox" class="' . esc_attr( $field->className ) . '" value="' . esc_attr( $option->value ) . '" name="' . esc_attr( $n_id ) . '" /> ' . esc_html( $option->label ) . '<br />';
						}
					} elseif ( $field->type == 'checkbox-group' ) {
						$options = $field->values;
						foreach ( $options as $option ) {
							echo '<input type="checkbox" class="' . esc_attr( $field->className ) . '" value="' . esc_attr( $option->value ) . '" name="' . esc_attr( $n_id ) . '" /> ' . esc_html( $option->label ) . '<br />';
						}
					} elseif ( $field->type == 'radio-group' ) {
						$options = $field->values;
						foreach ( $options as $option ) {
							echo '<input type="radio" class="' . esc_attr( $field->className ) . '" value="' . esc_attr( $option->value ) . '" name="' . esc_attr( $n_id ) . '" ' . esc_attr( $required ) . ' /> ' . esc_html( $option->label ) . '<br />';
						}
					} elseif ( $field->type == 'select' ) {
						$options = $field->values;
						echo '<select class="' . esc_attr( $field->className ) . '" id="' . esc_attr( $n_id ) . '" ' . esc_attr( $required ) . '>';
							foreach ( $options as $option ) {  
								echo '<option value="' . esc_attr( $option->value ) . '">' . esc_html( $option->label ) . '</option>';
							}
						echo '</select>';
					}
					if ( ! empty($field->other) && $field->other == 1 ) {
						echo '<br />';
						echo esc_html__('Other:', 'projectopia-core') . '<input style="width:100%" type="text" id="' . esc_attr( $n_id ) . '_other" />';
					}
					if ( ! empty($field->description) ) {
						echo '<p>' . wp_kses_post( $field->description ) . '</p>';
					}
					echo '</div>';
				}
				echo '<br />';
				echo '<input type="submit" class="cqpim_button bg-blue font-white rounded_2" id="cqpim_submit_backend" value="' . esc_attr__('Submit Quote Request', 'projectopia-core') . '" /><br /><div id="form_spinner" style="clear:both; display:none; background:url(' . esc_url( PTO_PLUGIN_URL ) . 'includes/css/img/ajax-loader.gif) center center no-repeat; width:16px; height:16px; padding:10px 0 0 5px; margin-top:15px"></div>';
				echo '<div style="margin-top:20px" id="cqpim_submit_backend_messages"></div>';
				echo '</div>';
			} else {
				echo '<p>' . esc_html__('You have not added any fields to the selected form', 'projectopia-core') . '</p>';
			}
			echo '</form>'; 
			echo '<input type="hidden" id="client" value="' . esc_attr( $assigned ) . '" />'; ?>
			?>
		</div>
		<?php 
		
add_action( 'wp_footer', 'pto_backend_form_scripts', 50 );
function pto_backend_form_scripts() {
	$form = get_option( 'cqpim_backend_form' );
	$form_data = get_post_meta( $form, 'builder_data', true );
	if ( empty( $form_data ) ) {
		$form_data = '';
	}
	$form_data = json_decode( $form_data );
	$fields = $form_data;
	if ( empty( $fields ) ) {
		$fields = array();
	} ?>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('#cqpim_backend_form').on('submit', function(e) {
					e.preventDefault();
					var spinner = jQuery('#form_spinner');
					var client = jQuery('#client').val();
					<?php
					if ( empty($fields) ) {
						$fields = array();
					}
					foreach ( $fields as $field ) {
						$n_id = isset( $field->name ) ? strtolower( $field->name ) : '';
						$n_id = str_replace( ' ', '_', $n_id );
						$n_id = str_replace( '-', '_', $n_id );
						$n_id = preg_replace( '/[^\w-]/', '', $n_id );    
						if ( $field->type != 'header' ) {
							if ( $field->type == 'text' || 
								$field->type == 'number' || 
								$field->type == 'email' || 
								$field->type == 'textarea' ||
								$field->type == 'website' || 
								$field->type == 'select' || 
								$field->type == 'date' || 
								$field->type == 'number' ) {
								echo 'var ' . esc_attr( $n_id ) . ' = jQuery("#' . esc_attr( $n_id ) . '").val();';
								echo 'if(!' . esc_attr( $n_id ) . ') { ' . esc_attr( $n_id ) . ' = ""; };';
							} elseif ( $field->type == 'checkbox-group' || $field->type == 'checkbox' ) {
								echo 'var ' . esc_attr( $n_id ) . ' = jQuery("input[name=' . esc_attr( $n_id ) . ']:checked").map(function() { return jQuery(this).val(); }).get();';
								echo 'if(!' . esc_attr( $n_id ) . ') { ' . esc_attr( $n_id ) . ' = ""; };';
							} elseif ( $field->type == 'radio-group' ) {
								echo 'var ' . esc_attr( $n_id ) . ' = jQuery("input[name=' . esc_attr( $n_id ) . ']:checked").val();';
								echo 'if(!' . esc_attr( $n_id ) . ') { ' . esc_attr( $n_id ) . ' = ""; };';
							} elseif ( $field->type == 'file' ) {
								echo 'var ' . esc_attr( $n_id ) . ' = jQuery("#upload_' . esc_attr( $n_id ) . '").val();';
								echo 'if(!' . esc_attr( $n_id ) . ') { ' . esc_attr( $n_id ) . ' = ""; };';
							}
							if ( ! empty($field->other) && $field->other == 1 ) {
								echo 'var ' . esc_attr( $n_id ) . '_other = jQuery("#' . esc_attr( $n_id ) . '_other").val();';
							}
						}
					}
					?>
					var data = {
						'action' : 'pto_backend_quote_submission',
						'pto_nonce' : '<?php echo esc_js( wp_create_nonce( PTO_GLOBAL_NONCE ) ); ?>',
						'client' : client,
						<?php
						foreach ( $fields as $field ) {
							if ( $field->type != 'header' ) {
								$n_id = isset( $field->name ) ? strtolower( $field->name ) : '';
								$n_id = str_replace( ' ', '_', $n_id );
								$n_id = str_replace( '-', '_', $n_id );
								$n_id = preg_replace( '/[^\w-]/', '', $n_id );
								if ( $field->type == 'file' ) {
									echo "'cqpimuploader_" . esc_attr( $n_id ) . "' : " . esc_attr( $n_id ) . ",";
								} else {
									echo "'" . esc_attr( $n_id ) . "' : " . esc_attr( $n_id ) . ",";
								}
								if ( ! empty( $field->other ) && $field->other == 1 ) {
									echo 'var ' . esc_attr( $n_id ) . '_other = jQuery("#' . esc_attr( $n_id ) . '_other").val();';
								}   
							}
						}
						?>
					};
					jQuery.ajax({
						url: '<?php echo esc_url( admin_url() . 'admin-ajax.php' ); ?>',
						data: data,
						type: 'POST',
						dataType: 'json',
						beforeSend: function(){
							spinner.show();
							jQuery('#cqpim_submit_backend').prop('disabled', true);
						},
					}).done(function(response){
						if(response.error == true) {
							spinner.hide();
							jQuery('#cqpim_submit_backend').prop('disabled', false);
							jQuery('#cqpim_submit_backend_messages').html(response.message);
						} else {
							spinner.hide();
							jQuery('#cqpim_submit_backend').prop('disabled', false);
							jQuery('#cqpim_submit_backend_messages').html(response.message);
						}
					});
				});
			});
		</script>	
<?php } ?>		
	</div>
</div>	