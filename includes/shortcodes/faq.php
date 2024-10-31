<?php
function pto_frontend_faq( $atts ) {
	$cats = isset($atts['category']) ? $atts['category'] : '';
	$terms = get_terms([
		'taxonomy'   => 'cqpim_faq_cat',
		'hide_empty' => false,
	]);
    // Initial shortcode output
    $output = '';
	if ( ! empty($cats) ) {
		foreach ( $terms as $term ) {
            $args = array(
                'post_type'      => 'cqpim_faq',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
                'tax_query'      => [
                    [
                        'taxonomy'         => 'cqpim_faq_cat',
                        'terms'            => $term->term_id,
                        'include_children' => true,
                    ],
                ],
                'meta_key'       => 'faq_order',
                'orderby'        => 'meta_value_num',
                'order'          => 'ASC',
            );
            $faq = get_posts($args);
            $output .= '<h3>' . $term->name . '</h3>';
            foreach ( $faq as $f ) {
                $output .= '<p><a href="' . get_the_permalink($f->ID) . '">' . $f->post_title . '</a></p>';
            }
        }
	} else {
        $args = array(
            'post_type'      => 'cqpim_faq',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_key'       => 'faq_order',
            'orderby'        => 'meta_value_num',
            'order'          => 'ASC',
        );
        $faq = get_posts($args);
        foreach ( $faq as $f ) {
            $output .= '<p><a href="' . get_the_permalink($f->ID) . '">' . $f->post_title . '</a></p>';
        }
    }

    // return shortcode output
    return $output;
}
add_shortcode('pto_faq', 'pto_frontend_faq');