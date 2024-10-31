<?php

function pto_opening_hours_shortcode() {
	$open = pto_return_open();
	$code = '<style>.cqpim-alert {border-width: 1px;padding: 10px;border: 1px solid transparent;border-radius:2px;-moz-border-radius:2px;-webkit-border-radius:2px;-o-border-radius:2px;}.cqpim-alert-success {background-color: #abe7ed;border-color: #27a4b0; color: #27a4b0; } .cqpim-alert-info { background-color: #e0ebf9; border-color: #327ad5; color: #327ad5; } .cqpim-alert-warning { background-color: #f9e491; border-color: #c29d0b; color: #c29d0b; } .cqpim-alert-danger { background-color: #fbe1e3; border-color: #e73d4a; color: #e73d4a; } .alert-display { margin:20px 0 0; }</style>';
	if ( $open == 1 ) {
		$message = get_option('pto_support_closed_message');
		$code .= '<div class="cqpim-alert cqpim-alert-warning alert-display">' . esc_textarea( $message ) . '</div>';
	} elseif ( $open == 2 ) {
		$message = get_option('pto_support_open_message');
		$code .= '<div class="cqpim-alert cqpim-alert-info alert-display">' . esc_textarea( $message ) . '</div>';
	}
	return $code;
}
add_shortcode('pto_opening_hours', 'pto_opening_hours_shortcode');

