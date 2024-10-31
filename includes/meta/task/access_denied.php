<?php

function pto_task_denied_metabox_callback( $post ) {
	echo '<p style="padding:20px">' . esc_html__('ACCESS DENIED: You are not assigned to this task.', 'projectopia-core') . '</p>';
}