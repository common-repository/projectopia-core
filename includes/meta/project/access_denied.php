<?php

function pto_project_denied_metabox_callback( $post ) {
	echo '<p style="padding:20px">' . esc_html__('ACCESS DENIED: You are not assigned to this project', 'projectopia-core') . '</p>';
}