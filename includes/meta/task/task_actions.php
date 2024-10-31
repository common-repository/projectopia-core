<?php
function pto_task_actions_metabox_callback( $post ) {
 	$pid = get_post_meta( $post->ID, 'project_id', true );
	$parent_object = get_post( $pid );
	$parent_type = isset( $parent_object->post_type ) ? $parent_object->post_type : '';
	
	if ( current_user_can( 'cqpim_delete_assigned_tasks' ) ) { ?>
		<button class="s_button2 piaBtn btn btn-primary btn-block redColor my-2" data-id="<?php echo esc_attr( $post->ID ); ?>" id="delete_task"><?php esc_html_e( 'Delete Task', 'projectopia-core' ); ?></button>
	<?php } ?>
	<button class="s_button piaBtn btn btn-primary btn-block mt-0 save" data-id="<?php echo esc_attr( $post->ID ); ?>"><?php esc_html_e( 'Update Task', 'projectopia-core' ); ?></button>
	<?php if ( ! empty( $pid ) && $parent_type == 'cqpim_project' ) { ?>
		<a class="piaBtn btn btn-primary btn-block btn-orange" href="<?php echo esc_url( get_edit_post_link( $pid ) ); ?>"><?php esc_html_e( 'Back to Project', 'projectopia-core' ); ?></a>
	<?php } ?>
	<?php if ( ! empty( $pid ) && $parent_type == 'cqpim_support' ) { ?>
		<a class="piaBtn btn btn-primary btn-block btn-orange" href="<?php echo esc_url( get_edit_post_link( $pid ) ); ?>"><?php esc_html_e( 'Back to Support Ticket', 'projectopia-core' ); ?></a>
	<?php } ?>
	<?php
}