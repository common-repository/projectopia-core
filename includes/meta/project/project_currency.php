<?php
/**
 * Project currency setting meta box register callback function.
 * 
 * @param Object $post
 */
function pto_project_currency_metabox_callback( $post ) {
	wp_nonce_field( 'project_currency_metabox', 'project_currency_metabox_nonce' );
	$quote_details = get_post_meta($post->ID, 'project_details', true);
	$client_id = isset($quote_details['client_id']) ? $quote_details['client_id'] : '';
	$currency_override = get_option('allow_client_currency_override');
	$currency = get_option('currency_symbol');
	$currency_code = get_option('currency_code');
	$currency_position = get_option('currency_symbol_position');
	$currency_space = get_option('currency_symbol_space'); 
	$client_currency = get_post_meta($client_id, 'currency_symbol', true);
	$client_currency_code = get_post_meta($client_id, 'currency_code', true);
	$client_currency_space = get_post_meta($client_id, 'currency_space', true);     
	$client_currency_position = get_post_meta($client_id, 'currency_position', true);   
	$quote_currency = get_post_meta($post->ID, 'currency_symbol', true);
	$quote_currency_code = get_post_meta($post->ID, 'currency_code', true);
	$quote_currency_space = get_post_meta($post->ID, 'currency_space', true);       
	$quote_currency_position = get_post_meta($post->ID, 'currency_position', true); 
	?>

	<p style="font-size: 1rem;"><?php esc_html_e('If you would like to override the currency on this project you can do so here. By default the project will use the system currency settings, however if the client that the project is assigned to has custom settings these will be used instead.', 'projectopia-core'); ?></p>
	<div class="cqpim-alert cqpim-alert-info alert-display">
		<h5>
			<img src="<?php echo esc_url( PTO_PLUGIN_URL . '/assets/admin/img/usacoin.svg' ); ?>" 
				class="bg-blue-sharp full-rounded icon img-fluid mr-2" />
			<span><?php esc_html_e('System Currency Settings', 'projectopia-core'); ?></span>
		</h5>

		<p>
			<?php esc_html_e('Currency Symbol:', 'projectopia-core'); ?> <?php echo esc_html( $currency ); ?><br />
			<?php esc_html_e('Currency Code:', 'projectopia-core'); ?> <?php echo esc_html( $currency_code ); ?><br />
			<?php esc_html_e('Currency Position:', 'projectopia-core'); ?> <?php if ( $currency_position == 'l' ) { esc_html_e('Before Amount', 'projectopia-core'); } else { esc_html_e('After Amount', 'projectopia-core'); } ?><br />
			<?php esc_html_e('Currency Space:', 'projectopia-core'); ?> <?php if ( $currency_space == '1' ) { esc_html_e('Yes', 'projectopia-core'); } else { esc_html_e('No', 'projectopia-core'); } ?>
		</p>
	</div>
	<?php if ( ! empty($client_id) ) { 
		if ( ! empty($client_currency_space) ) {
			if ( $client_currency_space == 'l' ) { 
				$sstring = __('Yes', 'projectopia-core'); 
			} else { 
				$sstring = __('No', 'projectopia-core'); 
			}
		} else {
			if ( $currency_space == 'l' ) { 
				$sstring = __('Yes (System Setting)', 'projectopia-core'); 
			} else { 
				$sstring = __('No (System Setting)', 'projectopia-core'); 
			}           
		}
		if ( ! empty($client_currency_position) ) {
			if ( $client_currency_position == 'l' ) { 
				$string = __('Before Amount', 'projectopia-core'); 
			} else { 
				$string = __('After Amount', 'projectopia-core'); 
			}
		} else {
			if ( $currency_position == 'l' ) { 
				$string = __('Before Amount (System Setting)', 'projectopia-core'); 
			} else { 
				$string = __('After Amount (System Setting)', 'projectopia-core'); 
			}           
		}
	?>
		<div class="cqpim-alert cqpim-alert-info alert-display">
			<h5>
				<img src="<?php echo esc_url( PTO_PLUGIN_URL . '/assets/admin/img/usacoin.svg' ); ?>" 
					class="bg-blue-sharp full-rounded icon img-fluid mr-2" />
				<span><?php esc_html_e('Client Currency Settings', 'projectopia-core'); ?></span>
			</h5>
			<p>
				<?php esc_html_e('Currency Symbol:', 'projectopia-core'); ?> <?php if ( ! empty($client_currency) ) { echo esc_html( $client_currency ); } else { echo esc_html( $currency ) . ' ' . esc_html__('(System Setting)', 'projectopia-core'); } ?><br />
				<?php esc_html_e('Currency Code:', 'projectopia-core'); ?> <?php if ( ! empty($client_currency_code) ) { echo esc_html( $client_currency_code ); } else { echo esc_html( $currency_code ) . ' ' . esc_html__('(System Setting)', 'projectopia-core'); } ?><br />
				<?php esc_html_e('Currency Position:', 'projectopia-core'); ?> <?php echo esc_html( $string ); ?><br />
				<?php esc_html_e('Currency Space:', 'projectopia-core'); ?> <?php echo esc_html( $sstring ); ?>
			</p>
		</div>
	<?php } ?>

	<h5 class="my-2"><?php esc_html_e('Active Currency Settings', 'projectopia-core'); ?></h5>

	<table style="width:100%">
		<tr>
			<td>
				<div class="form-group">
					<label fpr="currency_symbol"><?php esc_html_e('Project Client Currency Symbol:', 'projectopia-core'); ?></label>
					<div class="input-group">
						<input class="form-control input" type="text" name="currency_symbol" value="<?php echo esc_attr( $quote_currency ); ?>" />
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div class="form-group">
					<label for="currency_code"><?php esc_html_e('Project Currency Code:', 'projectopia-core'); ?><i class="fa fa-question-circle cqpim_tooltip" aria-hidden="true" title="" style="margin-left: 8px;" data-original-title="<?php esc_attr_e('Some of the available currencies are not supported by PayPal.', 'projectopia-core'); ?>"></i></label>
					<div class="input-group">
						<select class="form-control input customSelect" name="currency_code" id="currency_code">
							<option value="0"><?php esc_html_e('Choose a currency', 'projectopia-core'); ?></option>
							<?php $codes = pto_return_currency_select();
							foreach ( $codes as $key => $code ) {
								echo '<option value="' . esc_attr( $key ) . '" ' . selected( $key, $quote_currency_code, false ) . '>' . esc_html( $code ) . '</option>';
							}
							?>
						</select>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div class="form-group">
					<label for="currency_position"><?php esc_html_e('Project Currency Symbol Position: ', 'projectopia-core'); ?></label>
					<div class="input-group">
						<select name="currency_position" class="form-control input customSelect">
							<option value=""><?php esc_html_e('Choose...', 'projectopia-core'); ?></option>
							<option value="l" <?php if ( $quote_currency_position == 'l' ) { echo 'selected'; } ?>><?php esc_html_e('Before Amount', 'projectopia-core'); ?></option>
							<option value="r" <?php if ( $quote_currency_position == 'r' ) { echo 'selected'; } ?>><?php esc_html_e('After Amount', 'projectopia-core'); ?></option>
						</select>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div class="form-group mt-3">
					<label for="currency_space">
						<input type="checkbox" id="currency_space" name="currency_space" value="1" <?php if ( $quote_currency_space == '1' ) { echo 'checked'; } ?> />
						<?php esc_html_e('Add a space between the currency symbol and amount.', 'projectopia-core'); ?>
					</label>
				</div>
			</td>
		</tr>
	</table>		
	<?php
}