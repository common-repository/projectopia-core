<?php
function pto_quote_currency_metabox_callback( $post ) {
 	wp_nonce_field( 'quote_currency_metabox', 'quote_currency_metabox_nonce' );

	$quote_details = get_post_meta( $post->ID, 'quote_details', true);
	$client_id = isset( $quote_details['client_id']) ? $quote_details['client_id'] : '';
	$currency_override = get_option( 'allow_client_currency_override' );
	$currency = get_option( 'currency_symbol' );
	$currency_code = get_option( 'currency_code' );
	$currency_position = get_option( 'currency_symbol_position' );
	$currency_space = get_option( 'currency_symbol_space' ); 
	$client_currency = get_post_meta( $client_id, 'currency_symbol', true );
	$client_currency_code = get_post_meta( $client_id, 'currency_code', true );
	$client_currency_space = get_post_meta( $client_id, 'currency_space', true );       
	$client_currency_position = get_post_meta( $client_id, 'currency_position', true ); 
	$quote_currency = get_post_meta( $post->ID, 'currency_symbol', true );
	$quote_currency_code = get_post_meta( $post->ID, 'currency_code', true );
	$quote_currency_space = get_post_meta( $post->ID, 'currency_space', true );     
	$quote_currency_position = get_post_meta( $post->ID, 'currency_position', true );   
	?>
	<p><?php esc_html_e( 'If you would like to override the currency on this invoice you can do so here. By default the invoice will use the system currency settings, however if the client that the invoice is assigned to has custom settings these will be used instead.', 'projectopia-core' ); ?></p>
	<div class="cqpim-alert cqpim-alert-info alert-display">
		<p><strong><?php esc_html_e( 'System Currency Settings', 'projectopia-core' ); ?></strong></p>
		<p>
			<?php esc_html_e( 'Currency Symbol:', 'projectopia-core' ); ?> <?php echo esc_html( $currency ); ?><br />
			<?php esc_html_e( 'Currency Code:', 'projectopia-core' ); ?> <?php echo esc_html( $currency_code ); ?><br />
			<?php esc_html_e( 'Currency Position:', 'projectopia-core' ); ?> <?php if ( $currency_position == 'l' ) { esc_html_e( 'Before Amount', 'projectopia-core' ); } else { esc_html_e( 'After Amount', 'projectopia-core' ); } ?><br />
			<?php esc_html_e( 'Currency Space:', 'projectopia-core' ); ?> <?php if ( $currency_space == '1' ) { esc_html_e( 'Yes', 'projectopia-core' ); } else { esc_html_e( 'No', 'projectopia-core' ); } ?>
		</p>
	</div>
	<?php
	if ( ! empty( $client_id ) ) { 
		if ( ! empty( $client_currency_space ) ) {
			if ( $client_currency_space == 'l' ) { 
				$sstring = __( 'Yes', 'projectopia-core' ); 
			} else { 
				$sstring = __( 'No', 'projectopia-core' ); 
			}
		} else {
			if ( $currency_space == 'l' ) { 
				$sstring = __( 'Yes (System Setting)', 'projectopia-core' ); 
			} else { 
				$sstring = __( 'No (System Setting)', 'projectopia-core' ); 
			}           
		}
		if ( ! empty( $client_currency_position ) ) {
			if ( $client_currency_position == 'l' ) { 
				$string = __( 'Before Amount', 'projectopia-core' ); 
			} else { 
				$string = __( 'After Amount', 'projectopia-core' ); 
			}
		} else {
			if ( $currency_position == 'l' ) { 
				$string = __( 'Before Amount (System Setting)', 'projectopia-core' ); 
			} else { 
				$string = __( 'After Amount (System Setting)', 'projectopia-core' ); 
			}           
		}
		?>
		<div class="cqpim-alert cqpim-alert-info alert-display">
			<p><strong><?php esc_html_e( 'Client Currency Settings', 'projectopia-core' ); ?></strong></p>
			<p>
				<?php esc_html_e( 'Currency Symbol:', 'projectopia-core' ); ?> <?php if ( ! empty( $client_currency ) ) { echo esc_html( $client_currency ); } else { echo esc_html( $currency ) . ' ' . esc_html__( '(System Setting)', 'projectopia-core' ); } ?><br />
				<?php esc_html_e( 'Currency Code:', 'projectopia-core' ); ?> <?php if ( ! empty( $client_currency_code ) ) { echo esc_html( $client_currency_code ); } else { echo esc_html( $currency_code ) . ' ' . esc_html__( '(System Setting)', 'projectopia-core' ); } ?><br />
				<?php esc_html_e( 'Currency Position:', 'projectopia-core' ); ?> <?php echo esc_html( $string ); ?><br />
				<?php esc_html_e( 'Currency Space:', 'projectopia-core' ); ?> <?php echo esc_html( $sstring ); ?>
			</p>
		</div>
	<?php
	} ?>
	<p><strong><?php esc_html_e( 'Active Currency Settings', 'projectopia-core' ); ?></strong></p>
	<table style="width:100%">
		<tr>
			<td>
				<?php
				
				pto_generate_fields( array(
					'id'    => 'currency_symbol',
					'label' => __( 'Invoice Currency Symbol:', 'projectopia-core' ),
					'value' => $quote_currency,
				) );
				
				?>
			</td>
		</tr>
		<tr>
			<td>
				<?php
				
				pto_generate_fields( array(
					'type'    => 'select',
					'id'      => 'currency_code',
					'label'   => __( 'Invoice Currency Code:', 'projectopia-core' ),
					'value'   => $quote_currency_code,
					'options' => pto_return_currency_select(),
					'tooltip' => __( 'Some of the available currencies are not supported by PayPal.', 'projectopia-core' ),
					'default' => __( 'Choose a Currency...', 'projectopia-core' ),
				) );
				
				?>
			</td>
		</tr>
		<tr>
			<td>
				<?php
				
				pto_generate_fields( array(
					'type'    => 'select',
					'id'      => 'currency_position',
					'label'   => __( 'Invoice Currency Symbol Position:', 'projectopia-core' ),
					'value'   => $quote_currency_position,
					'options' => array(
						'l' => __( 'Before Amount', 'projectopia-core' ),
						'r' => __( 'After Amount', 'projectopia-core' ),
					),
					'default' => __( 'Choose...', 'projectopia-core' ),
				) );
				
				?>
			</td>
		</tr>
		<tr>
			<td style="padding-top: 15px;">
				<?php
				
				pto_generate_fields( array(
					'type'    => 'checkbox',
					'id'      => 'currency_space',
					'label'   => __( 'Add a space between the currency symbol and amount', 'projectopia-core' ),
					'checked' => '1' == $quote_currency_space,
				) );
				
				?>
			</td>
		</tr>
	</table>		
	<?php
}