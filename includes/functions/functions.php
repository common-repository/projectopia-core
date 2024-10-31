<?php
require_once('admin/admin_functions.php');
require_once('client/client_functions.php');
require_once('frontend/frontend_functions.php');
require_once('general/general_functions.php');
require_once('teams/team_functions.php');
require_once('messaging/messaging_functions.php');
require_once('invoice/invoice_functions.php');
require_once('quote/quote_functions.php');
require_once('project/project_functions.php');
require_once('task/task_functions.php');
require_once('ticket/ticket_functions.php');
require_once('forms/forms_functions.php');
require_once('templates/template_functions.php');
require_once('piping/piping_functions.php');
require_once('leads/leads_functions.php');
require_once('notifications/notification_functions.php');
require_once('faq/faq_functions.php');

/**
 * Function to remove the preview link for all CPT.
 */
add_filter( 'preview_post_link', 'pto_hide_client_cpt_preview_link', 10, 2 );
function pto_hide_client_cpt_preview_link( $preview_link, $post ) {
    $all_CPT = array(
        'cqpim_client',
		'cqpim_clients',
        'cqpim_faq',
		'cqpim_faqs',
        'cqpim_form',
		'cqpim_forms',
        'cqpim_invoice',
		'cqpim_invoices',
        'cqpim_leadform',
		'cqpim_leadforms',
        'cqpim_lead',
		'cqpim_leads',
        'cqpim_messages',
        'cqpim_conversations',
        'cqpim_project',
		'cqpim_projects',
        'cqpim_quote',
		'cqpim_quotes',
        'cqpim_support',
		'cqpim_supports',
        'cqpim_task',
		'cqpim_tasks',
        'cqpim_teams',
		'cqpim_team',
        'cqpim_template',
		'cqpim_templates',
        'cqpim_term',
		'cqpim_terms',
    );
	if ( empty( $preview_link ) || in_array( $post->post_type, $all_CPT ) ) {
		return;
	}
	return $preview_link;
}

add_action('post_locked_dialog', 'pto_post_locked_dialog', 10, 2);
add_action('post_lock_lost_dialog', 'pto_post_locked_dialog', 10);
function pto_post_locked_dialog( $post, $user = '' ) {
	?>
	<p class="post_locked_button_wrapper">
		<a class="button post-preview-link" href="javascript:void(0)" data-postid="<?php echo esc_attr( $post->ID ); ?>"><?php esc_html_e('View', 'projectopia-core'); ?></a>
		<script type="text/javascript">
			jQuery(function($){
				jQuery(document).on('click', '.post-preview-link', function(){
					jQuery('#post-lock-dialog').addClass('pto-hidden');
					jQuery('form').each(function (){
						jQuery(this).find(':input').attr('readonly', 'readonly').attr('disabled', 'disabled');
						jQuery(this).find('input[type=checkbox], input[type=radio], input[type=file], input[type=date]').attr('disabled', 'disabled');
						jQuery(this).find('button, input[type=submit]').attr('disabled', 'disabled');
					});
					if (typeof tinymce != 'undefined'){
						if (tinymce.editors.length > 0) {
							for (i = 0; i < tinymce.editors.length; i++) {
								tinymce.editors[i].getBody().setAttribute('contenteditable', false);
							}
						}
					}
				});
			});
		</script>
		<style type="text/css">
			.pto-hidden{display:none !important;}.post_locked_button_wrapper{display: inline-block;margin: 1em;}.post_locked_button_wrapper + p{float:left;}
			input.disabled, input:disabled, select.disabled, select:disabled, textarea.disabled, textarea:disabled{background-color: transparent;border-color: #7e8993;box-shadow: none;color: inherit;}
		</style>
	</p>
	<?php
}

add_action( 'admin_init', 'pto_post_types_set_filters' );
function pto_post_types_set_filters() {
	$post_types = get_post_types();
	foreach ( $post_types as $post_type ) {
		if ( strpos( $post_type, 'cqpim' ) !== false ) {
            add_filter( "bulk_actions-edit-{$post_type}", 'pto_disable_bulk_actions', 20 );
			add_filter( "handle_bulk_actions-edit-{$post_type}", 'pto_bulk_force_delete', 10, 3 );
			add_action( "views_edit-{$post_type}", 'pto_disable_status_filter_views' );
        }
	}
}

add_filter( 'get_avatar', 'pto_filter_avatar_client_team', 1, 6 );
function pto_filter_avatar_client_team( $avatar, $id, $size, $default, $alt, $args ) {
	$user = get_user_by( 'id', $id );
	if ( ! $id ) {
		return $avatar;
	}
	$new_avatar = '';
	$team = pto_get_team_from_userid( $user );
	if ( $team ) {
		$team_avatar = get_post_meta( $team, 'team_avatar', true );
		if ( ! empty( $team_avatar ) ) {
			$new_avatar = wp_get_attachment_image( $team_avatar, [ $size, $size ], false, [
				'class' => pto_set_avatar_css_class( $args ),
				'alt'   => $alt,
			] );
		}
	} else {
		$team = pto_get_client_from_userid( $user );
		if ( empty( $team ) || empty( $team['assigned'] ) ) {
			return $avatar;
		}
		$assigned = $team['assigned'];
		if ( ! empty( $team['type'] ) && 'admin' === $team['type'] ) {
			$team_avatar = get_post_meta( $assigned, 'team_avatar', true );
			if ( ! empty( $team_avatar ) ) {
				$new_avatar = wp_get_attachment_image( $team_avatar, [ $size, $size ], false, [
					'class' => pto_set_avatar_css_class( $args ),
					'alt'   => $alt,
				] );
			}
		} else {
			$client_contacts = get_post_meta( $assigned, 'client_contacts', true );
			$client_contacts = $client_contacts && is_array( $client_contacts ) ? $client_contacts : array();
			foreach ( $client_contacts as $key => $contact ) {
				if ( $key == $user->ID && ! empty( $contact['team_avatar'] ) ) {
					$team_avatar = $contact['team_avatar'];
				}
			}
			if ( ! empty( $team_avatar ) ) {
				$new_avatar = wp_get_attachment_image( $team_avatar, [ $size, $size ], false, [
					'class' => pto_set_avatar_css_class( $args ),
					'alt'   => $alt,
				] );
			}
		}
	}

	if ( ! empty( $new_avatar ) ) {
		$avatar = $new_avatar;
	}
	
	return $avatar;
}

function pto_set_avatar_css_class( $args ) {
	$class = array( 'avatar', 'avatar-' . (int) $args['size'], 'photo' );
 
    if ( ! $args['found_avatar'] || $args['force_default'] ) {
        $class[] = 'avatar-default';
    }
 
    if ( ! empty( $args['class'] ) ) {
		$css_class = is_array( $args['class'] ) ? implode( ' ', $args['class'] ) : $args['class'];
	} else {
        $css_class = implode( ' ', $class );
    }

	return $css_class;
}

function pto_disable_bulk_actions( $actions ) {
	unset( $actions['edit'] );
	unset( $actions['trash'] );

	if ( 'cqpim_client' === get_post_type() ) {
		$actions['pto_bulk_delete'] = __( 'Delete Client', 'projectopia-core' );
		$actions['pto_bulk_delete_user'] = __( 'Delete Client & User', 'projectopia-core' );
	} else {
		$actions['pto_bulk_delete'] = __( 'Delete', 'projectopia-core' );
	}
	return $actions;
}

function pto_bulk_force_delete( $redirect, $doaction, $object_ids ) {
	// let's remove query args first
	$redirect = remove_query_arg( 'pto_bulk_delete_done', $redirect );
	$do_action = trim( $doaction );
 	if ( strpos( $do_action, 'pto_bulk_delete' ) !== false ) {
		foreach ( $object_ids as $post_id ) {
			$user_details = [];
			$post_type = get_post_type( $post_id );
			if ( 'cqpim_client' === $post_type ) {
				$user_details = get_post_meta( $post_id, 'client_details', true );
			} elseif ( 'cqpim_teams' === $post_type ) {
				$user_details = get_post_meta( $post_id, 'team_details', true );
			}

			if ( ! empty( $user_details ) && 'pto_bulk_delete_user' === $do_action ) {
				$client_id = isset( $user_details['user_id'] ) ? intval( $user_details['user_id'] ) : '';
				if ( ! empty( $client_id ) ) {
					$user = get_user_by( 'id', $client_id );
					wp_delete_user( $user->ID );
				}
			}
			 
			wp_delete_post( $post_id, true );
		}

		if ( 'pto_bulk_delete' === trim( $doaction ) ) {
			foreach ( $object_ids as $post_id ) {
			
				wp_delete_post( $post_id, true );
				do_action( 'pto_bulk_delete_object', $post_id );
			}
		}

		// do not forget to add query args to URL because we will show notices later
		$redirect = add_query_arg( 'pto_bulk_delete_done', count( $object_ids ), $redirect );
	}

	return $redirect;
}

function pto_disable_status_filter_views( $views ) {
	unset( $views['private'] );
	unset( $views['trash'] );
	if ( count( $views ) <= 1 ) {
		return [];
	}

	return $views;
}

add_filter( 'display_post_states', 'pto_remove_post_state', 10, 2 );
function pto_remove_post_state( $post_states, $post ) {
	if ( strpos( $post->post_type, 'cqpim' ) === false ) {
		return $post_states;
	}

	unset( $post_states['private'] );
	unset( $post_states['protected'] );
	
	return $post_states;
}

add_action( 'admin_notices', 'pto_bulk_admin_notices' );
function pto_bulk_admin_notices() {
	if ( ! empty( $_REQUEST['pto_bulk_delete_done'] ) ) {

		$count = intval( sanitize_textarea_field( wp_unslash( $_REQUEST['pto_bulk_delete_done'] ) ) );

		// depending on ho much posts were changed, make the message different
		printf( '<div id="message" class="updated notice is-dismissible"><p>' .
			/* translators: %s: Post Count */
			esc_attr( _n( '%s entry has been deleted permanently.', '%s entries have been deleted permanently.', $count, 'projectopia-core' ) 
		) . '</p></div>', esc_html( $count ) );

	}
}

function pto_project_updates_element( $data ) { ?>
	<div class="projectActivities UpdatesWrapper" style="max-height: 500px;overflow-y: auto;">
		<div class="card m-0">
			<div class="card-body dailyUpdate">
				<!-- If these is no updates -->
				<?php if ( empty( $data ) ) { ?>
				<div class="dailyUpdateInner m-0" style="padding: 1.5625rem 1.875rem;">
					<h3 class="dailyUpdateTitle"><?php esc_html_e( 'Nothing Here!', 'projectopia-core' ); ?></h3>
					<span><?php esc_html_e( 'No updates...', 'projectopia-core' ); ?></span>	
				</div>
				<?php } else {
					krsort( $data );
					foreach ( $data as $date => $updates ) {
						$current_date = $updates['date'];
						unset( $updates['date'] ); ?>
						<div class="dailyUpdateInner">
							<h3 class="dailyUpdateTitle"> <?php echo esc_html( $current_date ); ?> </h3>
							<ul>
							<?php 
							uasort( $updates, function( $a, $b ) {
								return strcmp( $b['timestamp'], $a['timestamp'] );
							} );
							foreach ( $updates as $update ) { 
								if ( is_array( $update['update_message'] ) ) {
									$message = implode( '<br />', $update['update_message'] );
								} else {
									$message = $update['update_message'];
								}
								
								$del_btn = '';
								if ( isset( $update['delete_btn'] ) ) {
									$del_btn = $update['delete_btn'];
								} ?>
								<li>
									<div class="singleUpdateContainer d-flex justify-content-between">
										<div class="singleUpdate">
											<?php $avatar = get_option( 'cqpim_disable_avatars' );
											if ( empty( $avatar ) ) {
												echo '<div class="upImg">' . wp_kses_post( $update['avatar'] ) . '</div>';
											} ?>
											<div class="singleUpdateInfo">
												<span class="singleUpdateInfoName"><?php echo wp_kses_post( $update['member_name'] ); ?></span>
												<h4 class="title"></h4>
												<p><?php echo wp_kses_post( wpautop( $message ) ); ?></p>
											</div>
										</div>
										<div class="activeDate text-right">
											<p>&#x1F551; <?php echo wp_kses_post( $update['time'] ); ?><?php echo wp_kses_post( $del_btn ); ?> </p>
										</div>
									</div>
								</li>
							<?php } ?>
							</ul>
						</div>
					<?php }
					} ?>
			</div>
		</div>
	</div>
<?php
}

function pto_get_custom_fields( $data, $post, $is_client = false, $is_frontend = false ) {
	do_action( 'pto/custom_fields_output', $data, $post, $is_client, $is_frontend );
}

function pto_adjust_color_brightness( $hexCode, $adjustPercent ) {
    $hexCode = ltrim($hexCode, '#');

    if ( strlen($hexCode) == 3 ) {
        $hexCode = $hexCode[0] . $hexCode[0] . $hexCode[1] . $hexCode[1] . $hexCode[2] . $hexCode[2];
    }

    $hexCode = array_map('hexdec', str_split($hexCode, 2));

    foreach ( $hexCode as & $color ) {
        $adjustableLimit = $adjustPercent < 0 ? $color : 255 - $color;
        $adjustAmount = ceil($adjustableLimit * $adjustPercent);

        $color = str_pad(dechex($color + $adjustAmount), 2, '0', STR_PAD_LEFT);
    }

    return '#' . implode($hexCode);
}

function pto_generate_fields( $data ) {
	$data = apply_filters( 'pto_pre_generate_fields', $data, $data['id'] );

	if ( ! isset( $data['type'] ) || empty( $data['type'] ) ) {
		$data['type'] = 'text';
	}

	$class = [ 'form-control', 'input' ];
	if ( isset( $data['class'] ) && ! empty( $data['class'] ) ) {
		if ( is_array( $data['class'] ) ) {
			$class = array_merge( $class, $data['class'] );
		} else {
			array_push( $class, $data['class'] );
		}
	}

	$name = $data['id'];
	if ( isset( $data['name'] ) ) {
		$name = $data['name'];
	}

	$attr = [];
	if ( isset( $data['required'] ) && true === $data['required'] ) {
		$attr[] = 'required';
	}

	if ( isset( $data['checked'] ) && true === $data['checked'] ) {
		$attr[] = 'checked';
	}

	if ( isset( $data['disabled'] ) && true === $data['disabled'] ) {
		$attr[] = 'disabled';
	}

	if ( isset( $data['readonly'] ) && true === $data['readonly'] ) {
		$attr[] = 'readonly';
	}

	if ( isset( $data['attribute'] ) && ! empty( $data['attribute'] ) ) {
		if ( is_array( $data['attribute'] ) ) {
			foreach ( $data['attribute'] as $custom_attr ) {
				$attr[] = $custom_attr;
			}
		} else {
			array_push( $attr, $data['attribute'] );
		}
	}

	$placeholder = '';
	if ( isset( $data['placeholder'] ) ) {
		if ( is_bool( $data['placeholder'] ) && isset( $data['label'] ) ) {
			$placeholder = $data['label'];
		} else {
			$placeholder = $data['placeholder'];
		}
	}

	$value = '';
	if ( isset( $data['value'] ) ) {
		$value = $data['value'];
	}

	if ( isset( $data['type'] ) && $data['type'] == 'hidden' ) {
		echo '<input type="hidden" name="' . esc_attr( $name ) . '" id="' . esc_attr( $data['id'] ) . '" value="' . esc_attr( $value ) . '" />';
		return;
	}

	if ( isset( $data['type'] ) && $data['type'] == 'submit' ) {
		echo '<input type="submit" name="' . esc_attr( $name ) . '" id="' . esc_attr( $data['id'] ) . '" value="' . esc_attr( $value ) . '" ' . wp_kses_post( implode( ' ', array_unique( $attr ) ) ) . ' />';
		return;
	}

	if ( isset( $data['type'], $data['label'] ) && $data['type'] == 'checkbox' ) {
		$value = ! empty( $value ) ? $value : '1';
		echo '<div class="pto-inline-item-wrapper">';
			echo '<input type="checkbox" name="' . esc_attr( $name ) . '" id="' . esc_attr( $data['id'] ) . '" value="' . esc_attr( $value ) . '" ' . wp_kses_post( implode( ' ', array_unique( $attr ) ) ) . ' /> ' . esc_html( $data['label'] );
		echo '</div>';
		return;
	}

	if ( isset( $data['row_start'] ) && true === $data['row_start'] ) {
		echo '<div class="row">';
	}
	
	if ( isset( $data['col'] ) && true === $data['col'] ) {
		if ( isset( $data['col_num'] ) && ! empty( $data['col_num'] ) ) {
			echo '<div class="col-' . esc_attr( $data['col_num'] ) . '">';
		} else {
			echo '<div class="col-6">';
		}
	}

	echo '<div class="form-group">';
	if ( isset( $data['label'] ) ) {
		echo '<label for="' . esc_attr( $data['id'] ) . '">' . esc_html( $data['label'] );
		if ( isset( $data['tooltip'] ) ) {
			echo '<i class="fa fa-question-circle cqpim_tooltip" aria-hidden="true" title="' . esc_attr( $data['tooltip'] ) . '" style="margin-left: 8px;"></i>';
		}
		echo '</label>';
	}
	echo '<div class="input-group">';
	if ( isset( $data['type'] ) ) {
		if ( in_array( $data['type'], [ 'text', 'email', 'password', 'date' ] ) ) {
			if ( isset( $data['prepend'] ) ) {
				echo '<div class="input-group-prepend"><span class="input-group-text">' . wp_kses_post( $data['prepend'] ) . '</span></div>';
			}
			echo '<input type="' . esc_attr( $data['type'] ) . '" name="' . esc_attr( $name ) . '" id="' . esc_attr( $data['id'] ) . '" class="' . esc_attr( implode( ' ', array_unique( $class ) ) ) . '" value="' . esc_attr( $value ) . '" placeholder="' . esc_attr( $placeholder ) . '" ' . wp_kses_post( implode( ' ', array_unique( $attr ) ) ) . ' />';
			if ( isset( $data['append'] ) ) {
				echo '<div class="input-group-append"><span class="input-group-text">' . wp_kses_post( $data['prepend'] ). '</span></div>';
			}
		} elseif ( $data['type'] == 'textarea' ) {
			echo '<textarea class="' . esc_attr( implode( ' ', array_unique( $class ) ) ) . ' pto-textarea" id="' . esc_attr( $data['id'] ) . '" name="' . esc_attr( $name ) . '" placeholder="' . esc_attr( $placeholder ) . '" ' . wp_kses_post( implode( ' ', array_unique( $attr ) ) ) . '>' . wp_kses_post( $value ) . '</textarea>';
		} elseif ( $data['type'] == 'select' ) {
			if ( isset( $data['options'] ) && is_array( $data['options'] ) ) {
				echo '<select id="' . esc_attr( $data['id'] ) . '" class="' . esc_attr( implode( ' ', array_unique( $class ) ) ) . '" name="' . esc_attr( $name ) . '" ' . wp_kses_post( implode( ' ', array_unique( $attr ) ) ) . '>';
				if ( isset( $data['default'] ) ) {
					if ( is_bool( $data['default'] ) ) {
						echo '<option value="">' . esc_html__( 'Select...', 'projectopia-core' ) . '</option>';
					} else {
						echo '<option value="">' . esc_html( $data['default'] ) . '</option>';
					}
				}
				if ( ! empty( $data['options'] ) ) {
					foreach ( $data['options'] as $key => $option ) {
						echo '<option value="' . esc_attr( $key ) . '" ' . selected( $key, $value, false ) . '>' . esc_html( $option ) . '</option>';
					}
				}
				echo '</select>';
			}
		}
	}

	if ( isset( $data['col'] ) && true === $data['col'] ) {
		echo '</div>';
	}

	if ( isset( $data['row_end'] ) && true === $data['row_end'] ) {
		echo '</div>';
	}

	echo '</div>';
	echo '</div>';
}

function pto_files_meta_data( $post, $type, $is_client =  false ) {
	$all_attached_files = get_attached_media( '', $post->ID );
	
	if ( ! $all_attached_files ) {
		echo '<p>' . esc_html__( 'There are no files attached to this quote.', 'projectopia-core' ) . '</p>';
	} else {
		echo '<div class="card p-0 m-0"><table class="p-0 piaTableData table-responsive-lg table table-bordered w-100 dataTable" id="' . esc_attr( $type ) . '_table"><thead><tr>';
		echo '<th>' . esc_html__( 'File Name', 'projectopia-core' ) . '</th><th>' . esc_html__( 'File Type', 'projectopia-core' ) . '</th><th>' . esc_html__( 'Uploaded', 'projectopia-core' ) . '</th><th>' . esc_html__( 'Uploaded By', 'projectopia-core' ) . '</th><th>' . esc_html__( 'Actions', 'projectopia-core' ) . '</th>';
		echo '</tr></thead><tbody>';
		foreach ( $all_attached_files as $file ) {
			$file_object = get_post( $file->ID );
			$url = get_the_permalink( $file->ID );
			$user = get_user_by( 'id', $file->post_author );

			//Check file extension and mime type.
			$extension = pathinfo( $file->guid , PATHINFO_EXTENSION );
			$file_type = $extension;
			if ( ! empty( explode( '/', $file->post_mime_type )[0] == 'image' ) ) {
				$file_type = 'jpg';
			}

			//If file type icon is not available then assign docx as default for unknown type
			if ( ! in_array( $file_type, [ 'jpg', 'pdf', 'zip', 'docx' ] ) ) {
				$file_type = 'docx';
			}

			//Prepare file type icon.
			$file_type_icon = sprintf(
				'<span class="fileTypeWrapper align-items-center">
					<img src="%s" alt="%s" class="fileTypeIcon img-fluid mr-2" />
					<span class="mb-0">%s</span>
				</span>',
				PTO_PLUGIN_URL .'/assets/admin/img/' . $file_type . '.svg',
				$file_type,
				$extension
			);

			//Prepare download icon.
			$download_link = sprintf(
				'<a href="%s" download="%s" class="btn">
					<img src="%s" alt="download" class="img-fluid"/>
				</a>',
				esc_url( $file->guid ),
				$file->post_title,
				PTO_PLUGIN_URL .'/assets/admin/img/download.svg'
			);

			$delete_link = sprintf(
				'<button href="%s" class="delete_file btn" data-id="%s">
					<img src="%s" alt="delete" class="img-fluid"/>
				</button>',
				esc_url( $file->guid ),
				$file->ID,
				PTO_PLUGIN_URL .'/assets/admin/img/trash.svg'
			);

			echo '<tr>';
			echo '<td><a href="' . esc_url( $file->guid ) . '" target="_blank">' . esc_html( $file->post_title ) . '</a></td>';
			echo '<td>' . wp_kses_post( $file_type_icon ) . '</td>';
			echo '<td>' . esc_html( $file->post_date ) . '</td>';
			if ( is_object( $user ) ) {
				echo '<td>' . esc_html( $user->display_name ) . '</td>';
			} else {
				echo '<td></td>';
			}
			echo '<td>' . wp_kses_post( $download_link . ' ' . $delete_link ) . '</td>';
			echo '</tr>';
		}
		echo '</tbody></table></div><hr>';
	} ?>
	<div class="form-group mb-0">
    	<label for="attachments"><?php esc_html_e( 'Upload Files', 'projectopia-core' ); ?></label>
		<input type="file" class="cqpim-file-upload" name="async-upload" id="attachments" />
		<div id="upload_attachments"></div>
	</div>
	<input type="hidden" name="image_id" id="upload_attachment_ids">
	<?php
}

function pto_get_default_task_statuses() {
	$statuses = array(
 		'key'   => array( 'pending', 'on_hold', 'progress', 'complete' ),
		'color' => array( '#8ec165', '#8ec165', '#8ec165', '#8ec165' ),
		'value' => array( 'Pending', 'On Hold', 'In Progress', 'Complete' ),
	);

	return $statuses;
}

function pto_get_task_statuses() {
	$pto_task_status = get_option( 'pto_task_status' );
	if ( empty( $pto_task_status ) ) {
		$pto_task_status = pto_get_default_task_statuses();
 	}

	return $pto_task_status;
}

function pto_get_task_status_keys() {
	$status = array();
	$task_statuses = pto_get_task_statuses();
	foreach ( $task_statuses['key'] as $task_status ) {
		if ( ! empty( trim( $task_status ) ) ) {
			$status[] = $task_status;
		}
	}

	return $status;
}

function pto_get_task_status_kv() {
	$status = array();
	$pto_task_status = pto_get_task_statuses();
	for ( $i = 0; $i < count( $pto_task_status['key'] ); $i++ ) { 
		if ( empty( $pto_task_status['key'][ $i ] ) ) {
			continue;
		}

		$key = trim( $pto_task_status['key'][ $i ] );
		$status[ $key ] = $pto_task_status['value'][ $i ];
	}

	return $status;
}

function pto_get_task_status_value_by_key( $key, $type = 'value' ) {
	$status = array();
	$pto_task_status = pto_get_task_statuses();
	for ( $i = 0; $i < count( $pto_task_status['key'] ); $i++ ) { 
		if ( empty( $pto_task_status['key'][ $i ] ) ) {
			continue;
		}

		if ( $pto_task_status['key'][ $i ] === trim( $key ) ) {
			return $pto_task_status[ $type ][ $i ];
		}
	}

	return ucwords( str_replace( '_', ' ', $key ) );
}

function pto_get_task_status_html( $status, $deadline ) {
	$status_name = pto_get_task_status_value_by_key( $status );
	$status_color = pto_get_task_status_value_by_key( $status, 'color' );

	$status_string = '<span class="cqpim_button cqpim_small_button font-white rounded_2 op nolink rounded_2" style="background-color: ' . $status_color . ';">' . strtoupper( $status_name ) . '</span>';
	if ( $status != 'complete' ) {
		if ( $deadline ) {
			$today = strtotime( 'today' );
			$deadline = strtotime( gmdate( 'Y-m-d H:i:s', $deadline ) );
			if ( $today > $deadline ) {
		   		$status_string = '<span class="cqpim_button cqpim_small_button font-white bg-red rounded_2 op nolink rounded_2">' . __( 'OVERDUE', 'projectopia-core' ) . '</span>';
			}
	   	}
   	}

	return $status_string;
}

function pto_sanitize_rec_array( $array ) {
	foreach ( (array) $array as $k => $v ) {
		if ( is_array( $v ) ) {
			$array[ $k ] = pto_sanitize_rec_array( $v );
		} else {
			if ( strpos( $k, 'textarea' ) !== false ) {
				$array[ $k ] = sanitize_textarea_field( $v );
			} else {
				$array[ $k ] = sanitize_text_field( $v );
			}
		}
	}
   	return $array;                                                       
}

function pto_send_json( $data = null, $status_code = null, $options = 0 ) {
	if ( is_null( $data ) ) {
		$data = array( 
			'error'   => false,
			'message' => __( 'Message not specified!', 'projectopia-core' ),
		);
	}
	
	wp_send_json( $data, $status_code, $options );
}

add_action( 'check_ajax_referer', 'pto_check_ajax_referer', 10, 2 );
function pto_check_ajax_referer( $action, $result ) {
	if ( 'pto_nonce' === $action && false === $result ) {
		wp_send_json( array( 
			'error'   => true,
			'message' => __( 'Nonce Verification failed!', 'projectopia-core' ),
		) );
	}
}

add_filter( 'display_post_states', 'pto_filter_post_state', 10, 2 );
function pto_filter_post_state( $post_states, $post ) {
	$pto_template = get_post_meta( $post->ID, 'pto_template_type', true );
	if ( 'page' !== $post->post_type || ! $pto_template ) {
		return $post_states;
	}

	switch ( $pto_template ) {
		case 'cqpim_login_page':
			$template = __( 'Projectopia Client Login Page', 'projectopia-core' );
			break;
		case 'cqpim_reset_page':
			$template = __( 'Projectopia Password Reset Page', 'projectopia-core' );
			break;
		case 'cqpim_client_page':
			$template = __( 'Projectopia Client Dashboard Page', 'projectopia-core' );
			break;
		case 'cqpim_register_page':
			$template = __( 'Projectopia Client Register Page', 'projectopia-core' );
			break;
		default:
			$template = __( 'Projectopia Template', 'projectopia-core' );
	}

	$post_states['pto_template'] = $template;
	
	return $post_states;
}

add_filter( 'page_row_actions', 'pto_remove_page_row_actions', 10, 2 );
function pto_remove_page_row_actions( $actions, $post ) {
	$pto_template = get_post_meta( $post->ID, 'pto_template_type', true );
	if ( $pto_template ) {
		unset( $actions['trash'] );
	}
	return $actions;
}

add_filter( 'pre_trash_post', 'pto_block_trash_post_template', 10, 2 );
function pto_block_trash_post_template( $state, $post ) {
	$pto_template = get_post_meta( $post->ID, 'pto_template_type', true );
	if ( 'page' === $post->post_type && $pto_template ) {
		return false;
	}
	return $state;
}

add_action( 'wp_head', 'pto_cd_add_css_vars' );
function pto_cd_add_css_vars() {
	$gfont = pto_get_client_dashboard_gfont();
	$primary_color = get_option( 'client_dashboard_primary_color', '#002b78' );
	$secondary_color = get_option( 'client_dashboard_secondary_color', '#36c6d3' );
	$text_color = get_option( 'client_dashboard_text_color', '#001529' );
	$button_color = get_option( 'client_dashboard_button_color', '#337ab7' );
	$link_color = get_option( 'client_dashboard_link_color', pto_adjust_color_brightness( $text_color, -0.1 ) );
	$link_hover_color = get_option( 'client_dashboard_link_hover_color', pto_adjust_color_brightness( $text_color, -0.3 ) );
	?>
	<style>
        :root {
			<?php if ( ! empty( $gfont ) ) { ?>
            --pto-custom-font: "<?php echo esc_html( str_replace( '+', ' ', $gfont ) ); ?>";
			<?php } ?>
            --pto-primary-color: <?php echo esc_html( $primary_color ); ?>;
            --pto-secondary-color: <?php echo esc_html( $secondary_color ); ?>;
			--pto-menu-item-hover-color: <?php echo esc_html( pto_adjust_color_brightness( $primary_color, -0.2 ) ); ?>;
            --pto-menu-active-item-hover-color: <?php echo esc_html( pto_adjust_color_brightness( $secondary_color, -0.1 ) ); ?>;
			--pto-button-color: <?php echo esc_html( $button_color ); ?>;
			--pto-button-tx-color: <?php echo esc_html( pto_adjust_color_brightness( $primary_color, 0.3 ) ); ?>;
			--pto-text-color: <?php echo esc_html( $text_color ); ?>;
			--pto-link-color: <?php echo esc_html( $link_color ); ?>;
            --pto-link-hover-color: <?php echo esc_html( $link_hover_color ); ?>;
        }
    </style>
	<?php
}

function pto_get_client_dashboard_gfont() {
	$gfont = get_option( 'client_dashboard_gfont', 'Cabin' );
	if ( ! $gfont || 'default' == $gfont ) {
		$gfont = '';
	}

	return $gfont;
}

function pto_get_fs_addons() {
	$paid_addons = array(
		'pto_cf'     => array(
			'name'    => 'Custom Fields',
			'slug'    => 'pto-custom-fields',
			'fs_id'   => '9228',
			'fs_func' => 'projectopia_cf_fs',
		),
		'pto_st'     => array(
			'name'    => 'Support Tickets',
			'slug'    => 'pto-tickets',
			'fs_id'   => '9219',
			'fs_func' => 'projectopia_st_fs',
		),
		'pto_te'     => array(
			'name'    => 'Time Entries',
			'slug'    => 'pto-time-entries',
			'fs_id'   => '9221',
			'fs_func' => 'projectopia_te_fs',
		),
		'pto_roles'  => array(
			'name'    => 'Roles & Permissions',
			'slug'    => 'pto-roles',
			'fs_id'   => '9218',
			'fs_func' => 'projectopia_roles_fs',
		),
		'pto_woo'    => array(
			'name'    => 'WooCommerce',
			'slug'    => 'pto-woocommerce',
			'fs_id'   => '9227',
			'fs_func' => 'projectopia_woo_fs',
		),
		'pto_2co'    => array(
			'name'    => '2Checkout',
			'slug'    => 'pto-2checkout',
			'fs_id'   => '9222',
			'fs_func' => 'projectopia_2co_fs',
		),
		'pto_bugs'   => array(
			'name'    => 'Bug Tracker',
			'slug'    => 'pto-bugs',
			'fs_id'   => '9210',
			'fs_func' => 'projectopia_bugs_fs',
		),
		'pto_sub'    => array(
			'name'    => 'Subscriptions',
			'slug'    => 'pto-subscriptions',
			'fs_id'   => '9223',
			'fs_func' => 'projectopia_sub_fs',
		),
		'pto_re'     => array(
			'name'    => 'Reporting',
			'slug'    => 'pto-reporting',
			'fs_id'   => '9712',
			'fs_func' => 'projectopia_re_fs',
		),
		'pto_exp'    => array(
			'name'    => 'Expenses',
			'slug'    => 'pto-expenses',
			'fs_id'   => '9667',
			'fs_func' => 'projectopia_exp_fs',
		),
		'pto_tw'     => array(
			'name'    => 'Twilio',
			'slug'    => 'pto-twilio-integration',
			'fs_id'   => '9695',
			'fs_func' => 'projectopia_tw_fs',
		),
		'pto_kanban' => array(
			'name'    => 'Kanban Board',
			'slug'    => 'pto-kanban',
			'fs_id'   => '9217',
			'fs_func' => 'projectopia_kanban_fs',
		),
	);
	return $paid_addons;
}

function pto_has_addon_active_license( $name, $slug = false ) {
	$license = false;
	$addons = pto_get_fs_addons();
	if ( ! isset( $addons[ $name ] ) ) {
		return $license;
	}

	$data = $addons[ $name ];
	if ( ! function_exists( $data['fs_func'] ) ) {
		return $license;
	}

	// Initiate the Freemius instance.
	$addon_fs = call_user_func( $data['fs_func'] );

	if ( ! method_exists( $addon_fs, 'has_active_valid_license' ) ) {
		return $license;
	}
	
	if ( $addon_fs->has_active_valid_license() ) {
		$licenses = $addon_fs->_get_license();

		if ( is_object( $licenses ) && $licenses->parent_license_id ) {
			$license = FS_Plugin_License::is_valid_id( $licenses->parent_license_id );
		} else {
			$license = true;
		}
	}

	if ( $slug ) {
		return $license && pto_check_addon_status( $slug );
	}

	return $license;
}

function pto_convert_imgage_to_base64( $url ) {
	$type = pathinfo( $url, PATHINFO_EXTENSION );
	$image = file_get_contents( $url );
	if ( $image !== false ) {
		return 'data:image/' . $type . ';base64,' . base64_encode( $image );
	}
	return $url;
}