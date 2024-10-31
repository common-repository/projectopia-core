<?php 
include('header.php');
$client_details = get_post_meta($assigned, 'client_details', true);
$client_ids = get_post_meta($assigned, 'client_ids', true);
$ppid = get_post_meta($post->ID, 'bug_project', true); 
$project_details = get_post_meta($ppid, 'project_details', true);
$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
$looper = get_post_meta($post->ID, 'looper', true);
$looper = $looper ? $looper : 0;
if ( time() - $looper > 5 ) {
	$user = wp_get_current_user();
	$client_logs = get_post_meta($client_id, 'client_logs', true);
	if ( empty($client_logs) ) {
		$client_logs = array();
	}
	$now = time();
	$p_title = get_the_title();
	$p_title = str_replace('Private:', '', $p_title);
	$client_logs[ $now ] = array(
		'user' => $user->ID,
		/* translators: %s: Bug Title */
		'page' => sprintf(esc_html__('Bug - %1$s', 'projectopia-core'), $p_title),
	);
	update_post_meta($client_id, 'client_logs', $client_logs);
	update_post_meta($post->ID, 'looper', time());
}
?>
<div id="cqpim-dash-sidebar-back"></div>
<div class="cqpim-dash-content">
	<div id="cqpim-dash-sidebar">
		<?php include('sidebar.php'); ?>
	</div>
	<div id="cqpim-dash-content">
		<div id="cqpim_admin_title">
			<?php if ( $assigned == $client_id ) {
			$ptitle = get_post($ppid);
			$ptitle = $ptitle->post_title;
			$p_title = get_the_title(); $p_title = str_replace('Private:', '', $p_title); echo '<a href="' . esc_url( get_the_permalink($client_dash) ) . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <i class="fa fa-circle"></i> <a href="' . esc_url( get_the_permalink($ppid) ) . '?pto-page=summary&sub=updates">' . esc_html( $ptitle ) . '</a> <i class="fa fa-circle"></i> ' . esc_html( $p_title );
			} else {
				esc_html_e('ACCESS DENIED', 'projectopia-core');
			}
			?>
		</div>
		<style>
			.bug_update {
				border-bottom: 2px solid #eee;
				padding-bottom:10px;
				margin-bottom:10px;
			}
			.bug_update.last {
				margin-bottom:0px;
			}
			.update_title {
				padding-bottom:10px;
				margin-bottom:10px;
			}
			.bug_update.last .update_title {
				padding-bottom:0px;
				margin-bottom:0px;
			}
			.update_av {
				float:left;
			}
			.update_info {
				position:relative;
				width:calc(100% - 90px);
				float:right;
			}
			.update_status {
				position:absolute;
				top:0;
				right:10px;
			}
			.update_status span {
				text-transform:uppercase;
			}
			.update_update {
			}
			.bug_changes {
				margin:0 !important;
				font-size:12px;
			}
			p.underline {
				margin:7px 0;
			}
		</style>
		<div id="cqpim-cdash-inside">
			<?php
			if ( $assigned == $client_id ) {
				if ( function_exists('pto_render_bug_cd_metaboxes') ) {
					pto_render_bug_cd_metaboxes($post->ID);
				}
			} else {
				echo '<h1 style="margin-top:0">' . esc_html__('ACCESS DENIED', 'projectopia-core') . '</h1>';
			}
			?>	
		</div>
	</div>
</div>
<?php include('footer_inc.php'); ?>