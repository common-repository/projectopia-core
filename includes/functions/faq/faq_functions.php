<?php
add_action( "wp_ajax_cqpim_update_faq_order", "cqpim_update_faq_order");
function cqpim_update_faq_order() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	if ( empty( $_POST['post'] ) || empty( $_POST['order'] ) ) {
		pto_send_json( array( 
			'error' => true,
		) ); 
	}
	update_post_meta(intval($_POST['post']), 'faq_order', sanitize_text_field(wp_unslash($_POST['order'])));   
	pto_send_json( array( 
		'error' => false,
	) ); 
}

add_filter( 'the_content', 'pto_replace_faq_content' );
function pto_replace_faq_content( $content ) {
    if ( is_singular('cqpim_faq') ) {
		global $post;
		$content = get_post_meta($post->ID, 'terms', true);
    }
    return $content;
}