jQuery(document).ready(function() {
	jQuery('.disable_email').on('change', function(e) {
		var project_id = jQuery('#post_ID').val();
		var spinner = jQuery('#cqpim_overlay');
		var demail = jQuery(this).is(":checked");
		if(demail == true) {
			demail = 1;
		} else {
			demail = 0;
		}
		var key = jQuery(this).data('key');
		var data = {
			'action' : 'pto_update_project_team_email',
			'project_id' : project_id,
			'demail' : demail,
			'key' : key,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery.colorbox.resize();
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
			} else {
				spinner.hide();
			}
		});		
	});
	jQuery('.project_manager').on('change', function(e) {
		var project_id = jQuery('#post_ID').val();
		var spinner = jQuery('#cqpim_overlay');
		var pm = jQuery(this).is(":checked");
		if(pm == true) {
			pm = 'yes';
		} else {
			pm = 'no';
		}
		var key = jQuery(this).data('key');
		var data = {
			'action' : 'pto_update_project_team_pm',
			'project_id' : project_id,
			'pm' : pm,
			'key' : key,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery.colorbox.resize();
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				alert('Error occured!');
				window.location.reload();
			} else {
				spinner.hide();
				alert(localisation.projects.pm_update_text);
				window.location.reload();
			}
		});		
	});
	jQuery('#quote_client').on('change', function(e) {
		e.preventDefault();
		var client_id = jQuery(this).val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_update_client_contacts',
			'client_id' : client_id,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery.colorbox.resize();
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
			} else {
				spinner.hide();
				jQuery('#client_contact').prop('disabled', false);
				jQuery('#client_contact').html(response.contacts);
				jQuery.colorbox.resize();
			}
		});
	});
	jQuery('#add_team_member_ajax').click(function(e) {
		e.preventDefault();
		var team_ids = jQuery('#team_members').val();
		var project_id = jQuery('#post_ID').val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action'     : 'pto_add_team_to_project',
			'team_ids'   : team_ids,
			'project_id' : project_id,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#add_team_member_ajax').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#add_team_member_ajax').prop('disabled', false);
				jQuery('#add_team_messages').html(response.message);
				jQuery.colorbox.resize();
			} else {
				spinner.hide();
				jQuery('#add_team_member_ajax').prop('disabled', false);
				jQuery('#add_team_messages').html(response.message);
				jQuery.colorbox.resize();
				location.reload();
			}
		});
	});
	jQuery('.delete_team').click(function(e) {
		e.preventDefault();
		var key = jQuery(this).val();
		var team = jQuery(this).data('team');
		var project_id = jQuery('#post_ID').val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_remove_team_member',
			'key' : key,
			'project_id' : project_id,
			'team' : team,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('.delete_team').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('.delete_team').prop('disabled', false);
			} else {
				spinner.hide();
				jQuery('.delete_team').prop('disabled', false);
				location.reload();
			}
		});
	});
	// Open Add Milestone Colorbox
	jQuery('a#add-milestone').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox({			
			'width' : '500px',
			'maxWidth':'95%',
			'inline': true,
			'href': '#add-milestone-div',
			'opacity': '0.5',
		});	
		jQuery.colorbox.resize();
	});
	// Add Milestone AJAX
	jQuery('#add_quote_element').on('click', function(e) {
		e.preventDefault();
		var title = jQuery('#quote_element_title').val();
		var start = jQuery('#quote_element_start').val();
		var deadline = jQuery('#quote_element_finish').val();
		var cost = jQuery('#quote_element_cost').val();
		var milestone_id = jQuery('#add_milestone_id').val();
		var milestone_order = jQuery('#add_milestone_order').val();
		var post_id = jQuery('#post_ID').val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_add_step_to_quote',
			'title' : title,
			'start' : start,
			'deadline' : deadline,
			'ID' : post_id,
			'cost' : cost,
			'milestone_id' : milestone_id,
			'weight' : milestone_order,
			'type' : 'project',
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#add_quote_element').prop('disabled', true);
			},
		}).always(function(response){
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#add_quote_element').prop('disabled', false);
				alert(response.errors);
			} else {
				console.log(response);
				spinner.hide();
				jQuery('#add_quote_element').prop('disabled', false);
				jQuery.colorbox.close();
				jQuery('#no_ms_nag').remove();
				jQuery('#dd-container').append(response.markup);
				jQuery('#quote_element_title').val("");
				jQuery('#quote_element_start').val("");
				jQuery('#quote_element_finish').val("");
				jQuery('#quote_element_cost').val("");
				jQuery( ".dd-container" ).sortable( "refresh" );
				//location.reload();
			}
		});
	});
	// Retrieve Milestone Data For Editing
	jQuery(document).on('click', 'button.edit-milestone', function(e){
		e.preventDefault();
		var key = jQuery(this).val();
		var item = jQuery('#post_ID').val();
		var data = {
			'action' : 'pto_retrieve_milestone_data',
			'key' : key,
			'item' : item,
			'type' : 'project',
			'pto_nonce' : localisation.global_nonce
		};		
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
		}).always(function(response){
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				alert(response.errors);
			} else {
				jQuery('#edit_milestone_id').val(response.data.id);
				jQuery('#edit_milestone_title').val(response.data.title);
				jQuery('#edit_milestone_start').val(response.data.start);
				jQuery('#edit_milestone_end').val(response.data.deadline);
				jQuery('#edit_milestone_cost').val(response.data.cost);
				jQuery('#edit_milestone_fcost').val(response.data.fcost);
				jQuery('#edit_milestone_status').val(response.data.status);
				jQuery.colorbox({
					'width' : '500px',
					'maxWidth':'95%',
					'inline': true,
					'href': '#edit-milestone',
					'opacity': '0.5',
				});	
				jQuery.colorbox.resize();				
			}
		});
	});	
	// Save New Milestone Data and Update Milestones Container
	jQuery(document).on('click', 'button.save-milestone', function(e){
		e.preventDefault();
		var spinner = jQuery('#cqpim_overlay');
		var item = jQuery('#post_ID').val();
		var key = jQuery('#edit_milestone_id').val();
		var title = jQuery('#edit_milestone_title').val();
		var start = jQuery('#edit_milestone_start').val();
		var deadline = jQuery('#edit_milestone_end').val();
		var cost = jQuery('#edit_milestone_cost').val();
		var fcost = jQuery('#edit_milestone_fcost').val();
		var status = jQuery('#edit_milestone_status').val();
		spinner.show();
		var data = {
			'action' : 'pto_update_milestone_data',
			'key' : key,
			'item' : item,
			'title' : title,
			'start' : start,
			'deadline' : deadline,
			'cost' : cost,
			'fcost' : fcost,
			'status' : status,
			'type' : 'project',
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
		}).always(function(response){
			console.log(response);
			spinner.hide();
		}).done(function(response){
			if(response.error == true) {
				jQuery('#edit-milestone-messages').html(response.errors);
				jQuery.colorbox.resize();
			} else {
				jQuery('#edit-milestone-messages').html(response.message);
				jQuery('#ms_cost_'+response.data.id).html( response.data.cost );
				jQuery('#ms_start_'+response.data.id).html( response.data.start );
				jQuery('#ms_deadline_'+response.data.id).html( response.data.deadline );
				jQuery('#ms_title_'+response.data.id).html( response.data.title );
				jQuery('#ms_title_'+response.data.id).parents('#milestone-'+response.data.id).find('.dd-milestone-status').html( response.data.status_string);
				jQuery.colorbox.resize();
				jQuery.colorbox.close();
				setTimeout(function() {
					
					jQuery('#edit-milestone-messages').html('');
				}, 1000);

				//location.reload();
			}
		});
	});
	// Open Delete Milestone Colorbox & Set ID
	jQuery(document).on('click', 'button.delete_stage_conf', function(e){
		e.preventDefault();
		var key = jQuery(this).val();
		var item = jQuery('#post_ID').val();
		jQuery.colorbox({
			'width' : '500px',
			'maxWidth':'95%',
			'inline': true,
			'href': '#delete-milestone',
			'opacity': '0.5',
		});
		jQuery('#delete-milestone-button').val(key);
		jQuery.colorbox.resize();
	});	
	// Complete Milestone Removal and Remove MS Container
	jQuery(document).on('click', '#delete-milestone-button', function(e){
		e.preventDefault();
		var key = jQuery(this).val();		
		var item = jQuery('#post_ID').val();
		var spinner = jQuery('#cqpim_overlay');
		spinner.show();
		var data = {
			'action' : 'pto_delete_milestone_data',
			'key' : key,
			'item' : item,
			'type' : 'project',
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
		}).always(function(response){
			console.log(response);
			spinner.hide();
		}).done(function(response){
			if(response.error == true) {
				jQuery('#delete-milestone-messages').html(response.errors);
				jQuery.colorbox.resize();
			} else {
				jQuery('#delete-milestone-messages').html(response.message);
				jQuery.colorbox.resize();
				jQuery(response.container).remove();
				jQuery.colorbox.close();
				// setTimeout(function() {
				// 	jQuery.colorbox.close();
				// 	jQuery('#delete-milestone-messages').html('');
				// 	location.reload();
				// }, 1000);
			}
		});
	});
	// Reorder Milestones
	jQuery('#dd-container').sortable({
		handle: '.dd-reorder',
		cancel: '',
		update: function( event, ui ) {
			weights = {};
			i = 1;
			jQuery('.dd-milestone').each(function(i) {
				jQuery(this).children('input.element_weight').val(i);
				ms_id = jQuery(this).children('input.element_weight').data('msid');
				weights[ms_id] = {
					'ms_id' : ms_id,
					'weight' : i,
				};
				i = i + 1;
			});
			console.log(weights);
			var item = jQuery('#post_ID').val();
			var data = {
				'action' : 'pto_reorder_milestone_data',
				'item' : item,
				'weights' : weights,
				'type' : 'project',
				'pto_nonce' : localisation.global_nonce
			};
			jQuery.ajax({
				url: ajaxurl,
				data: data,
				type: 'POST',
				dataType: 'json',
			}).always(function(response){
				console.log(response);
			}).done(function(response){
			});
		}
	}).disableSelection();
	// Open Add Task Box and Fill in Milestone ID
	jQuery(document).on('click', '.add_task', function(e){
		e.preventDefault();
		var id = jQuery(this).data('ms');
		jQuery.colorbox({
			'width' : '500px',
			'maxWidth':'95%',
			'inline': true,
			'href': '#add-task-div',
			'opacity': '0.5',
		});
		jQuery('#add_task_milestone_id').val(id);
	});	
	// Add a Task
	jQuery('button.save-task').on('click', function(e){
		e.preventDefault();
		var spinner = jQuery('#cqpim_overlay');
		var ms_id = jQuery('#add_task_milestone_id').val();
		var title = jQuery('#add_task_title').val();
		var description = jQuery('#add_task_description').val();
		var start = jQuery('#add_task_start').val();
		var deadline = jQuery('#add_task_finish').val();
		var task_time = jQuery('#add_task_time').val();
		var owner = jQuery('#add_task_owner').val();
		var project_id = jQuery('#post_ID').val();
		var data = {
			'action' : 'pto_create_task',
			'task_finish' : deadline,
			'task_project_id' : project_id,
			'task_title' : title,
			'task_milestone_id' : ms_id,
			'description' : description,
			'start' : start,
			'task_time' : task_time,
			'owner' : owner,
			'task_project_id' : project_id,
			'type' : 'project',
			'ttype' : 'parent',
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				jQuery('button.save-task').prop('disabled', true);
				spinner.show();
			},
		}).always(function(response){
			console.log(response);
			spinner.hide();
		}).done(function(response){
			if(response.error == true) {
				jQuery('button.save-task').prop('disabled', false);
				jQuery('#add-task-messages').html(response.errors);
				jQuery.colorbox.resize();
			} else {
				jQuery('button.save-task').prop('disabled', false);
				jQuery('#add-task-messages').html(response.message);
				jQuery.colorbox.resize();
				jQuery.colorbox.close();
				jQuery('#no_ms_nag').remove();
				
				jQuery('#milestone-' + ms_id + ' .dd-tasks').append(response.markup);	
				
				
				jQuery('#add_task_milestone_id').val("");
				jQuery('#add_task_title').val("");
				jQuery('#add_task_description').val("");
				jQuery('#add_task_start').val("");
				jQuery('#add_task_finish').val("");
				jQuery('#add_task_time').val("");
				jQuery('#add-task-messages').html('');
				jQuery('#add_task_owner').val("")
				jQuery( ".dd-tasks" ).sortable( "refresh" );
				if(response.milestone_dates.milestone_id) {
					jQuery('#ms_start_' + response.milestone_dates.milestone_id).html(response.milestone_dates.start);
					jQuery('#ms_deadline_' + response.milestone_dates.milestone_id).html(response.milestone_dates.deadline);
				}
				if(response.milestone_status.milestone_id) {
					jQuery('#milestone-' + response.milestone_status.milestone_id + ' .dd-milestone-status').html(response.milestone_status.status);
				}

				//location.reload();
			}
		});	
	});
	// Open Add SubTask Box and Fill in Milestone ID & Parent
	jQuery(document).on('click', '.add_subtask', function(e){
		e.preventDefault();
		var id = jQuery(this).data('ms');
		var parent = jQuery(this).val();
		jQuery.colorbox({
			'width' : '500px',
			'maxWidth':'95%',
			'innerHeight': '600px',
			'inline': true,
			'href': '#add-subtask-div',
			'opacity': '0.5',
		});
		jQuery('#add_subtask_milestone_id').val(id);
		jQuery('#add_subtask_parent_id').val(parent);
	});		
	// Add a SubTask
	jQuery('button.save-subtask').on('click', function(e){
		e.preventDefault();
		var ms_id = jQuery('#add_subtask_milestone_id').val();
		var parent = jQuery('#add_subtask_parent_id').val();
		var title = jQuery('#add_subtask_title').val();
		var description = jQuery('#add_subtask_description').val();
		var start = jQuery('#add_subtask_start').val();
		var deadline = jQuery('#add_subtask_finish').val();
		var task_time = jQuery('#add_subtask_time').val();
		var owner = jQuery('#add_subtask_owner').val();
		var project_id = jQuery('#post_ID').val();
		var spinner = jQuery('#cqpim_overlay');

		var data = {
			'action' : 'pto_create_task',
			'task_finish' : deadline,
			'parent' : parent,
			'task_project_id' : project_id,
			'task_title' : title,
			'task_milestone_id' : ms_id,
			'description' : description,
			'start' : start,
			'task_time' : task_time,
			'owner' : owner,
			'type' : 'project',
			'ttype' : 'sub',
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				jQuery('button.save-subtask').prop('disabled', true);
				spinner.show();
			},
		}).done(function(response) {
			spinner.hide();

			if(response.error == true) {
				jQuery('button.save-subtask').prop('disabled', false);
				jQuery('#add-subtask-messages').html(response.errors);
				jQuery.colorbox.resize();
			} else {
				jQuery('button.save-subtask').prop('disabled', false);
				jQuery('#add-subtask-messages').html(response.message);
				jQuery.colorbox.resize();
				jQuery.colorbox.close();
				jQuery('#no_ms_nag').remove();
				jQuery('#task-' + parent + ' .dd-subtasks').append(response.markup);
				jQuery('#add_subtask_parent_id').val("");
				jQuery('#add_subtask_milestone_id').val("");
				jQuery('#add_subtask_title').val("");
				jQuery('#add_subtask_description').val("");
				jQuery('#add_subtask_start').val("");
				jQuery('#add_subtask_finish').val("");
				jQuery('#add_subtask_time').val("");
				jQuery('#add-subtask-messages').html('');
				jQuery( ".dd-subtasks" ).sortable( "refresh" );
				if(response.milestone_dates.milestone_id) {
					jQuery('#ms_start_' + response.milestone_dates.milestone_id).html(response.milestone_dates.start);
					jQuery('#ms_deadline_' + response.milestone_dates.milestone_id).html(response.milestone_dates.deadline);
				}
				//location.reload();
			}
		});	
	});	
	// Retrieve Task Data For Editing
	jQuery(document).on('click', 'button.edit-task', function(e){
		e.preventDefault();
		var task_id = jQuery(this).val();
		var item = jQuery('#post_ID').val();
		var data = {
			'action' : 'pto_retrieve_task_data',
			'task_id' : task_id,
			'item' : item,
			'type' : 'project',
			'pto_nonce' : localisation.global_nonce
		};		
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
		}).always(function(response){
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				alert(response.errors);
			} else {
				jQuery('#edit_task_id').val(response.data.id);
				jQuery('#edit_task_title').val(response.data.title);
				jQuery('#edit_task_start').val(response.data.start);
				jQuery('#edit_task_finish').val(response.data.deadline);
				jQuery('#edit_task_description').val(response.data.desc);
				jQuery('#edit_task_time').val(response.data.time);
				jQuery.colorbox({
					'width' : '500px',
					'maxWidth':'95%',
					'inline': true,
					'href': '#edit-task',
					'opacity': '0.5',
				});	
				jQuery.colorbox.resize();				
			}
		});
	});	
	// Save New Task Data and Update Tasks Container
	jQuery(document).on('click', 'button.update-task', function(e){
		e.preventDefault();
		var item = jQuery('#post_ID').val();
		var task_id = jQuery('#edit_task_id').val();
		var title = jQuery('#edit_task_title').val();
		var desc = jQuery('#edit_task_description').val();
		var start = jQuery('#edit_task_start').val();
		var deadline = jQuery('#edit_task_finish').val();
		var time = jQuery('#edit_task_time').val();
		var spinner = jQuery('#cqpim_overlay');
		spinner.show();

		var data = {
			'action' : 'pto_update_task_data',
			'task_id' : task_id,
			'item' : item,
			'title' : title,
			'start' : start,
			'deadline' : deadline,
			'desc' : desc,
			'time' : time,
			'type' : 'project',
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
		}).always(function(response){
			console.log(response);
			spinner.hide();
		}).done(function(response){
			if(response.error == true) {
				jQuery('#edit_task_messages').html(response.errors);
				jQuery.colorbox.resize();
			} else {
				jQuery('#edit_task_messages').html(response.message);
				jQuery.colorbox.resize();
				jQuery('#task_title_' + response.data.id).html(response.data.title);
				jQuery('#task_start_' + response.data.id).html(response.data.start);
				jQuery('#task_deadline_' + response.data.id).html(response.data.deadline);
				jQuery('#task_time_' + response.data.id).html(response.data.time);
				// Set task start and deadline after task updated
				jQuery('#start_' + response.data.id).val(response.data.start);
				jQuery('#end_' + response.data.id).val(response.data.deadline);
				jQuery('#est_time_' + response.data.id).text(response.data.time);
				if(response.milestone_dates.milestone_id) {
					jQuery('#ms_start_' + response.milestone_dates.milestone_id).html(response.milestone_dates.start);
					jQuery('#ms_deadline_' + response.milestone_dates.milestone_id).html(response.milestone_dates.deadline);
				}
				setTimeout(function() {
					jQuery.colorbox.close();
					jQuery('#edit_task_messages').html('');
				}, 1000);
			}
		});
	});
	// Open Delete Task Colorbox & Set ID
	jQuery(document).on('click', 'button.delete_task_trigger', function(e){
		e.preventDefault();
		var key = jQuery(this).val();
		jQuery.colorbox({
			'width' : '500px',
			'maxWidth':'95%',
			'inline': true,
			'href': '#delete-task',
			'opacity': '0.5',
		});
		jQuery('#delete-task-button').val(key);
		jQuery('#delete-task-messages').html('');
		jQuery.colorbox.resize();
	});	
	// Open Delete SubTask Colorbox & Set ID
	jQuery(document).on('click', 'button.delete_subtask_trigger', function(e){
		e.preventDefault();
		var key = jQuery(this).val();
		jQuery.colorbox({
			'width' : '500px',
			'maxWidth':'95%',
			'inline': true,
			'href': '#delete-subtask',
			'opacity': '0.5',
		});
		jQuery('#delete-subtask-button').val(key);
		jQuery('#delete-subtask-messages').html('');
		jQuery.colorbox.resize();
	});	
	// Complete Task Removal and Remove MS Container
	jQuery(document).on('click', '#delete-task-button', function(e){
		e.preventDefault();
		var task_id = jQuery(this).val();
		var spinner = jQuery('#cqpim_overlay');
		spinner.show();

		var data = {
			'action' : 'pto_delete_task_data',
			'task_id' : task_id,
			'type' : 'project',
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
		}).always(function(response){
			console.log(response);
			spinner.hide();
		}).done(function(response){
			if(response.error == true) {
				jQuery('#delete-task-messages').html(response.errors);
				jQuery.colorbox.resize();
			} else {
				jQuery('#delete-task-messages').html(response.message);
				jQuery.colorbox.resize();
				jQuery(response.container).remove();
				if(response.milestone_dates.milestone_id) {
					jQuery('#ms_start_' + response.milestone_dates.milestone_id).html(response.milestone_dates.start);
					jQuery('#ms_deadline_' + response.milestone_dates.milestone_id).html(response.milestone_dates.deadline);
				}
				setTimeout(function() {
					jQuery.colorbox.close();
					jQuery('#delete-task-messages').html('');
				}, 1000);
			}
		});
	});	
	// Complete Task Removal and Remove MS Container
	jQuery(document).on('click', '#delete-subtask-button', function(e){
		e.preventDefault();
		var task_id = jQuery(this).val();
		var spinner = jQuery('#cqpim_overlay');
		spinner.show();
	
		var data = {
			'action' : 'pto_delete_task_data',
			'task_id' : task_id,
			'type' : 'project',
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
		}).always(function(response){
			console.log(response);
			spinner.hide();
		}).done(function(response){
			if(response.error == true) {
				jQuery('#delete-subtask-messages').html(response.errors);
				jQuery.colorbox.resize();
			} else {
				jQuery('#delete-subtask-messages').html(response.message);
				jQuery.colorbox.resize();
				jQuery(response.container).remove();
				if(response.milestone_dates.milestone_id) {
					jQuery('#ms_start_' + response.milestone_dates.milestone_id).html(response.milestone_dates.start);
					jQuery('#ms_deadline_' + response.milestone_dates.milestone_id).html(response.milestone_dates.deadline);
				}
				setTimeout(function() {
					jQuery.colorbox.close();
					jQuery('#delete-subtask-messages').html('');
				}, 1000);
			}
		});
	});	
	// Reorder Tasks
	jQuery('.dd-tasks').sortable({
		handle: '.dd-reorder',
		cancel: '',
		update: function( event, ui ) {
			weights = {};
			i = 1;
			milestone = jQuery(this).parent('.dd-milestone').attr('id');
			jQuery('#' + milestone + ' .dd-task').each(function(i) {
				jQuery(this).children('input.task_weight').val(i);
				task_id = jQuery(this).children('input.task_id').val();
				weights[task_id] = {
					'task_id' : task_id,
					'weight' : i,
				};
				i = i + 1;
			});
			console.log(weights);
			var item = jQuery('#post_ID').val();
			var data = {
				'action' : 'pto_reorder_task_data',
				'item' : item,
				'weights' : weights,
				'type' : 'project',
				'pto_nonce' : localisation.global_nonce
			};
			jQuery.ajax({
				url: ajaxurl,
				data: data,
				type: 'POST',
				dataType: 'json',
			}).always(function(response){
				console.log(response);
			}).done(function(response){
			});
		}
	}).disableSelection();	
	// Reorder SubTasks
	jQuery('.dd-subtasks').sortable({
		handle: '.dd-reorder',
		cancel: '',
		update: function( event, ui ) {
			weights = {};
			i = 1;
			milestone = jQuery(this).parent('.dd-task').attr('id');
			jQuery('#' + milestone + ' .dd-subtask').each(function(i) {
				jQuery(this).children('input.task_weight').val(i);
				task_id = jQuery(this).children('input.task_id').val();
				weights[task_id] = {
					'task_id' : task_id,
					'weight' : i,
				};
				i = i + 1;
			});
			console.log(weights);
			var item = jQuery('#post_ID').val();
			var data = {
				'action' : 'pto_reorder_task_data',
				'item' : item,
				'weights' : weights,
				'type' : 'quote',
				'pto_nonce' : localisation.global_nonce
			};
			jQuery.ajax({
				url: ajaxurl,
				data: data,
				type: 'POST',
				dataType: 'json',
			}).always(function(response){
				console.log(response);
			}).done(function(response){
			});
		}
	}).disableSelection();
	// Mark Task Complete Button
	jQuery(document).on('click', 'button.item_complete', function(e){
		e.preventDefault();
		var item_id = jQuery(this).val();
		var type = jQuery(this).data('type');
		var project_post_id = jQuery('#post_ID').val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_mark_item_complete',
			'item_id' : item_id,
			'type' : type,
			'ppid' : project_post_id,
			'pto_nonce' : localisation.global_nonce
		};
			jQuery.ajax({
				url: ajaxurl,
				data: data,
				type: 'POST',
				dataType: 'json',
				beforeSend: function(){
					spinner.show();
					jQuery('button.item_complete').prop('disabled', true);
				},
			}).always(function(response){
				console.log(response);
			}).done(function(response){
				if(response.error == true) {
					spinner.hide();
					jQuery('button.item_complete').prop('disabled', false);
					alert(response.errors);
				} else {
					spinner.hide();
					jQuery('button.item_complete').prop('disabled', false);
					jQuery('#task-' + item_id).removeClass('border-amber');
					jQuery('#task-' + item_id).removeClass('border-red');
					jQuery('#task-' + item_id + ' .dd-task-status').html(response.status);
					if(response.ms_complete.milestone_id) {
						jQuery('#milestone-' + response.ms_complete.milestone_id + ' .dd-milestone-status').html(response.ms_complete.status_string);						
					}
				}
			});		
	});
	jQuery('#toggle_all_tasks').on('click', function(e){
		e.preventDefault();
		var project_post_id = jQuery('#post_ID').val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_toggle_complete',
			'ppid' : project_post_id,
			'pto_nonce' : localisation.global_nonce
		};
			jQuery.ajax({
				url: ajaxurl,
				data: data,
				type: 'POST',
				dataType: 'json',
				beforeSend: function(){
					spinner.show();
					jQuery('#toggle_all_tasks').prop('disabled', true);
				},
			}).always(function(response){
				console.log(response);
			}).done(function(response){
				if(response.error == true) {
					spinner.hide();
					jQuery('#toggle_all_tasks').prop('disabled', false);
					alert(response.errors);
				} else {
					spinner.hide();
					jQuery('#toggle_all_tasks').prop('disabled', false);
					location.reload();
				}
			});		
	});
	jQuery('#send_contract').click(function(e) {
		e.preventDefault();
		jQuery('#messages').html('');
		var project_id = jQuery(this).data('id');
		var spinner = jQuery('#cqpim_overlay');
		var messages = jQuery('#messages');
		var domain = document.domain;
		var data = {
			'action' : 'pto_process_contract_emails',
			'project_id' : project_id,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#send_contract').prop('disabled', true);
			},
			error: function () {
				spinner.hide();
				location.reload();
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#send_contract').prop('disabled', false);
				jQuery('#messages').html(response.errors);
			} else {
				spinner.hide();
				jQuery('#send_contract').prop('disabled', false);
				jQuery('#messages').html(response.message);
				jQuery('#publish').trigger('click');
			}
		});
	});
	jQuery('#signed_off').click(function(e) {
		e.preventDefault();
		var project_id = jQuery(this).data('id');
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_mark_project_complete',
			'project_id' : project_id,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#signed_off').prop('disabled', true);
			},
		}).always(function(response){
			console.log(response);			
		}).done(function(response){
			if(response.error == true) {
			spinner.hide();
				jQuery('#signed_off').prop('disabled', false);
				jQuery('#messages').html(response.errors);
			} else {
				spinner.hide();
				jQuery('#signed_off').prop('disabled', false);
				jQuery('#messages').html(response.messages);
				location.reload();
			}
		});
	});
	jQuery('.save-unsigned').click(function(e) {
		e.preventDefault();
		var project_id = jQuery('#post_ID').val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_mark_project_incomplete',
			'project_id' : project_id,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('.save-unsigned').prop('disabled', true);
			},
		}).always(function(response){
			console.log(response);			
		}).done(function(response){
			if(response.error == true) {
			spinner.hide();
				jQuery('.save-unsigned').prop('disabled', false);
				jQuery('#unsign-error').html(response.messages);
			} else {
				spinner.hide();
				jQuery('.save-unsigned').prop('disabled', false);
				jQuery('#unsign-error').html(response.messages);
				location.reload();
			}
		});
	});
	jQuery('#close_off').click(function(e) {
		e.preventDefault();
		var project_id = jQuery(this).data('id');
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_mark_project_closed',
			'project_id' : project_id,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#close_off').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
			spinner.hide();
				jQuery('#close_off').prop('disabled', false);
				jQuery('#messages').html(response.errors);
			} else {
				spinner.hide();
				jQuery('#close_off').prop('disabled', false);
				jQuery('#messages').html(response.messages);
				location.reload();
			}
		});
	});
	jQuery('#unclose_off').click(function(e) {
		e.preventDefault();
		var project_id = jQuery(this).data('id');
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_mark_project_open',
			'project_id' : project_id,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#unclose_off').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
			spinner.hide();
				jQuery('#unclose_off').prop('disabled', false);
				jQuery('#messages').html(response.errors);
			} else {
				spinner.hide();
				jQuery('#unclose_off').prop('disabled', false);
				jQuery('#messages').html(response.messages);
				location.reload();
			}
		});
	});
	jQuery('#add_message_ajax').click(function(e) {
		e.preventDefault();
		var visibility = jQuery('#add_message_visibility').val();
		var message = jQuery('#add_message_text').val();
		var project_id = jQuery('#post_ID').val();
		var who = jQuery('#message_who').val();
		if(jQuery('#send_to_team').is(':checked')) {
			send_to_team = 1;
		} else {
			send_to_team = 0
		}
		if(jQuery('#send_to_client').is(':checked')) {
			send_to_client = 1;
		} else {
			send_to_client = 0
		}
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_add_message_to_project',
			'visibility' : visibility,
			'message' : message,
			'project_id' : project_id,
			'who' : who,
			'send_to_team' : send_to_team,
			'send_to_client' : send_to_client,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#add_message_trigger').prop('disabled', true);
				jQuery.colorbox.resize();
			},
		}).always(function(response){
			if(response.error == true) {
			spinner.hide();
				jQuery('#add_message_ajax').prop('disabled', false);
				jQuery('#message_messages').html('<p>' + response.errors + '</p>');
			} else {
				spinner.hide();
				jQuery('#add_message_ajax').prop('disabled', false);
				jQuery('#message_messages').html('<p>' + response.errors + '</p>');
				jQuery.colorbox.resize();
				location.reload();
			}
		});
	});
	jQuery('button.delete_message').click(function(e) {
		e.preventDefault();
		var project_id = jQuery('#post_ID').val();
		var key = jQuery(this).data('id');
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_delete_project_message',
			'project_id' : project_id,
			'key' : key,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			//dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#button.delete_message').prop('disabled', true);
			},
		}).done(function(){
				location.reload();
		});
	});
	jQuery('a.time_remove').click(function(e) {
		e.preventDefault();
		var task_id = jQuery(this).data('task');
		var key = jQuery(this).data('key');
		var element = jQuery(this);
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_remove_time_entry',
			'task_id' : task_id,
			'key' : key,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('a.time_remove').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
			spinner.hide();
				jQuery('a.time_remove').prop('disabled', false);
			} else {
				spinner.hide();
				jQuery('a.time_remove').prop('disabled', false);
				jQuery(element).parents('li').fadeOut('slow');
				jQuery(element).parents('li').remove();
			}				
		});
	});
	jQuery('#template_choice').on('change', function(e) {
		e.preventDefault();
		jQuery('#template_team_warning').hide();
		var project_id = jQuery('#post_ID').val();
		var template = jQuery('#template_choice').val();
		var spinner = jQuery('#cqpim_overlay');
		var domain = document.domain;
		var data = {
			'action' : 'pto_check_template_assignees',
			'project_id' : project_id,
			'template' : template,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery.colorbox.resize();
			},
		}).always(function(response) {
			console.log(response);		
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#template_team_warning').show();
				jQuery('#template_team_warning').html(response.message);
				jQuery.colorbox.resize();
			} else {
				spinner.hide();
				jQuery.colorbox.resize();
			}
		});
	});
	jQuery('#apply-template-action').click(function(e) {
		e.preventDefault();
		jQuery('#apply-template-messages').html('');
		var quote_id = jQuery(this).val();
		var type = jQuery(this).attr('data-type');
		var hid = jQuery(this).data('hid');
		var hwe = jQuery(this).data('hwe');
		var template = jQuery('#template_choice').val();
		var spinner = jQuery('#cqpim_overlay');
		var messages = jQuery('#apply-template-messages');
		var domain = document.domain;
		var data = {
			'action' : 'pto_apply_template',
			'quote_id' : quote_id,
			'type' : type,
			'template' : template,
			'hid' : hid,
			'hwe' : hwe,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery.colorbox.resize();
				jQuery('#apply-template-action').prop('disabled', true);
			},
		}).always(function(response) {
			console.log(response);		
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#apply-template-action').prop('disabled', false);
				jQuery(messages).html('<p>' + response.errors + '</p>');
				jQuery.colorbox.resize();
			} else {
				spinner.hide();
				jQuery('#apply-template-action').prop('disabled', false);
				jQuery(messages).html('<p>' + response.messages + '</p>');
				jQuery.colorbox.resize();
				location.reload();
			}
		});
	});
	jQuery('#clear-all-action').click(function(e) {
		e.preventDefault();
		jQuery('#apply-template-messages').html('');
		var quote_id = jQuery(this).val();
		var type = jQuery(this).attr('data-type');
		var spinner = jQuery('#cqpim_overlay');
		var messages = jQuery('#clear-all-messages');
		var domain = document.domain;
		var data = {
			'action' : 'pto_clear_all_action',
			'quote_id' : quote_id,
			'type' : type,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery.colorbox.resize();
				jQuery('#clear-all-action').prop('disabled', true);
			},
		}).always(function(response) {
			console.log(response);		
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#clear-all-action').prop('disabled', false);
				jQuery(messages).html('<p>' + response.errors + '</p>');
				jQuery.colorbox.resize();
			} else {
				spinner.hide();
				jQuery('#clear-all-action').prop('disabled', false);
				jQuery(messages).html('<p>' + response.messages + '</p>');
				jQuery.colorbox.resize();
				location.reload();
			}
		});
	});
	jQuery('#send_deposit').click(function(e) {
		e.preventDefault();
		var project_id = jQuery('#post_ID').val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_create_deposit_invoice',
			'project_id' : project_id,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#send_deposit').prop('disabled', true);
			},
		}).always(function(response){
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#send_deposit').prop('disabled', false);
			} else {
				spinner.hide();
				jQuery('#send_deposit').prop('disabled', false);
				location.reload();
			}
		});
	});
	jQuery('.assign-all-confirm').click(function(e) {
		e.preventDefault();
		var ms = jQuery('#assign_all_ms').val();
		var assignee = jQuery('#assign_all_assignee').val();
		var notify = jQuery('#assign_all_notify').is(":checked");
		if(notify == true) {
			notify = 1;
		} else {
			notify = 0;
		}
		var project_id = jQuery('#post_ID').val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_assign_all_ms',
			'project_id' : project_id,
			'assignee' : assignee,
			'ms' : ms,
			'notify' : notify,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('.assign-all-confirm').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('.assign-all-confirm').prop('disabled', false);
				jQuery('#assign-all-message').html(response.message);
				jQuery.colorbox.resize();
			} else {
				spinner.hide();
				jQuery('.assign-all-confirm').prop('disabled', false);
				jQuery('#assign-all-message').html(response.message);
				jQuery.colorbox.resize();
				if(response.ids) {
					jQuery.each(response.ids, function( index, value ) {
						jQuery('#task-' + value + ' .admin_task_assignee').val(assignee);	
					});				
				}
				jQuery.colorbox.close();
				jQuery('#assign-all-message').html('');
				jQuery('#assign_all_assignee').val("");
			}
		});
	});
	jQuery(document).on('click', '.toggle_tasks', function(e) {
		e.preventDefault();
		var direction = jQuery(this).val();
		var ms = jQuery(this).data('ms')
		var project = jQuery(this).data('project');
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_toggle_project_ms',
			'project' : project,
			'ms' : ms,
			'direction' : direction,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('.toggle_tasks').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('.toggle_tasks').prop('disabled', false);
			} else {
				spinner.hide();
				jQuery('.toggle_tasks').prop('disabled', false);
				if(response.status == 'on') {
					jQuery('#milestone-' + response.ms).removeClass('ms-toggled');
					jQuery('#toggle-' + response.ms + ' i.fa').removeClass('fa-chevron-circle-down');
					jQuery('#toggle-' + response.ms + ' i.fa').addClass('fa-chevron-circle-up');
					jQuery('#toggle-' + response.ms).val('hide');
				} else {
					jQuery('#milestone-' + response.ms).addClass('ms-toggled');
					jQuery('#toggle-' + response.ms + ' i.fa').addClass('fa-chevron-circle-down');
					jQuery('#toggle-' + response.ms + ' i.fa').removeClass('fa-chevron-circle-up');
					jQuery('#toggle-' + response.ms).val('show');
				}
			}
		});
	});
	jQuery(document).on('change', '.start_editable', function(e) {
		e.preventDefault();
		var task_id = jQuery(this).data('id');
		var date = jQuery(this).val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_editable_start',
			'task_id' : task_id,
			'date' : date,
			'pto_nonce' : localisation.global_nonce
		};

		// Compare task start and deadline date
		var task_start    = jQuery(this).val();
		var task_deadline = jQuery(this).parents( '.dd-task-info' ).find( '.end_editable' ).val();
		// Convert string to data object
		/*task_start = task_start.split('/');
		task_deadline = task_deadline.split('/');
		var task_start_date = new Date(task_start[2],task_start[1],task_start[0]);
		var task_deadline_date = new Date(task_deadline[2],task_deadline[1],task_deadline[0]);*/

		
		//patch
		if(localisation.calendar.dateFormat=="dd/mm/yy"){
			task_start_Arr =  task_start.split("/");
			task_deadline_Arr =  task_deadline.split("/");

			task_start =  task_start_Arr[1]+"/"+task_start_Arr[0]+"/"+task_start_Arr[2];
			task_deadline =  task_deadline_Arr[1]+"/"+task_deadline_Arr[0]+"/"+task_deadline_Arr[2];
		}else if(localisation.calendar.dateFormat=="dd.mm.yy"){
			task_start_Arr =  task_start.split(".");
			task_deadline_Arr =  task_deadline.split(".");

			task_start =  task_start_Arr[1]+"/"+task_start_Arr[0]+"/"+task_start_Arr[2];
			task_deadline =  task_deadline_Arr[1]+"/"+task_deadline_Arr[0]+"/"+task_deadline_Arr[2];
		}else if(localisation.calendar.dateFormat=="mm/dd/yy"){
			
			task_start_Arr =  task_start.split("/");
			task_deadline_Arr =  task_deadline.split("/");

			task_start =  task_start_Arr[0]+"/"+task_start_Arr[1]+"/"+task_start_Arr[2];
			task_deadline =  task_deadline_Arr[0]+"/"+task_deadline_Arr[1]+"/"+task_deadline_Arr[2];
		}else{
			task_start =  task_start.split("/").join("/");
			task_deadline =  task_deadline.split("/").join("/");
		}

		var task_start_date = new Date(task_start);
		var task_deadline_date = new Date(task_deadline);

		// Task deadline should greater than task start date before post
		if ( task_deadline_date >= task_start_date ) {
			jQuery.ajax({
				url: ajaxurl,
				data: data,
				type: 'POST',
				dataType: 'json',
				beforeSend: function(){
					spinner.show();
					jQuery('.start_editable').prop('disabled', true);
				},
			}).done(function(response){
				if(response.error == true) {
					spinner.hide();
					jQuery('.start_editable').prop('disabled', false);
				} else {
					spinner.hide();
					jQuery('.start_editable').prop('disabled', false);
					if(response.milestone_dates.milestone_id) {
						jQuery('#ms_start_' + response.milestone_dates.milestone_id).html(response.milestone_dates.start);
						jQuery('#ms_deadline_' + response.milestone_dates.milestone_id).html(response.milestone_dates.deadline);
					}
				}
			});
		}else{
			alert( 'Deadline should be greater than the start date' );
			jQuery('#cqpim_overlay').hide();
			return;
		}
	});
	jQuery(document).on('change', '.end_editable', function(e) {
		e.preventDefault();
		var task_id = jQuery(this).data('id');
		var date = jQuery(this).val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_editable_end',
			'task_id' : task_id,
			'date' : date,
			'pto_nonce' : localisation.global_nonce
		};

		// Compare task start and deadline date
		var task_deadline    = jQuery(this).val();
		var task_start = jQuery(this).parents('.dd-task-info').find('.start_editable').val();
		// Convert string to data object
		//patch
		if(localisation.calendar.dateFormat=="dd/mm/yy"){
			task_start_Arr =  task_start.split("/");
			task_deadline_Arr =  task_deadline.split("/");

			task_start =  task_start_Arr[1]+"/"+task_start_Arr[0]+"/"+task_start_Arr[2];
			task_deadline =  task_deadline_Arr[1]+"/"+task_deadline_Arr[0]+"/"+task_deadline_Arr[2];
		}else if(localisation.calendar.dateFormat=="dd.mm.yy"){
			task_start_Arr =  task_start.split(".");
			task_deadline_Arr =  task_deadline.split(".");

			task_start =  task_start_Arr[1]+"/"+task_start_Arr[0]+"/"+task_start_Arr[2];
			task_deadline =  task_deadline_Arr[1]+"/"+task_deadline_Arr[0]+"/"+task_deadline_Arr[2];
		}else if(localisation.calendar.dateFormat=="mm/dd/yy"){
			
			task_start_Arr =  task_start.split("/");
			task_deadline_Arr =  task_deadline.split("/");

			task_start =  task_start_Arr[0]+"/"+task_start_Arr[1]+"/"+task_start_Arr[2];
			task_deadline =  task_deadline_Arr[0]+"/"+task_deadline_Arr[1]+"/"+task_deadline_Arr[2];
		}else{
			task_start =  task_start.split("/").join("/");
			task_deadline =  task_deadline.split("/").join("/");
		}
		

		/*task_start = task_start.split('/');
		task_deadline = task_deadline.split('/');
		var task_start_date = new Date(task_start[2],task_start[1],task_start[0]);
		var task_deadline_date = new Date(task_deadline[2],task_deadline[1],task_deadline[0]);
		*/
		//patch
		var task_start_date = new Date(task_start);
		var task_deadline_date = new Date(task_deadline);
		
		// Task deadline should greater than task start date before post
		if ( task_deadline_date >= task_start_date ) {
			jQuery.ajax({
				url: ajaxurl,
				data: data,
				type: 'POST',
				dataType: 'json',
				beforeSend: function(){
					spinner.show();
					jQuery('.end_editable').prop('disabled', true);
				},
			}).always(function(response){
				console.log(response)
			}).done(function(response){
				if(response.error == true) {
					spinner.hide();
					jQuery('.end_editable').prop('disabled', false);
				} else {
					spinner.hide();
					jQuery('.end_editable').prop('disabled', false);
					if(response.task_status_string) {
						jQuery('#task-' + response.task_id + '-status').html(response.task_status_string);
					}
					if(!response.overdue) {
						jQuery('#task-' + response.task_id).removeClass('border-red');
						jQuery('#task-' + response.task_id).removeClass('border-amber');
						jQuery('#task-' + response.task_id).removeClass('overdue');
					} else {
						if(response.overdue == "border-red overdue") {
							jQuery('#task-' + response.task_id).addClass('border-red');
							jQuery('#task-' + response.task_id).addClass('overdue');						
						} else if(response.overdue == "border-amber overdue") {
							jQuery('#task-' + response.task_id).addClass('border-amber');
							jQuery('#task-' + response.task_id).addClass('overdue');						
						}
					}
					if(response.milestone_dates.milestone_id) {
						jQuery('#ms_start_' + response.milestone_dates.milestone_id).html(response.milestone_dates.start);
						jQuery('#ms_deadline_' + response.milestone_dates.milestone_id).html(response.milestone_dates.deadline);
					}
					if(response.tasks_to_update) {
						jQuery(response.tasks_to_update).each(function(e, v) {
							jQuery('#start_' + v.task_id).val(v.start);
							jQuery('#end_' + v.task_id).val(v.deadline);
						});
					}
				}
			});
		}else{
			alert( 'Deadline should be greater than the start date' );
			jQuery('#cqpim_overlay').hide();
			return;
		}
	});
	jQuery(document).on('change', '.assignee_editable', function(e) {
		e.preventDefault();
		var task_id = jQuery(this).data('id');
		var assignee = jQuery(this).val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_editable_assignee',
			'task_id' : task_id,
			'assignee' : assignee,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('.assignee_editable').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('.assignee_editable').prop('disabled', false);
			} else {
				spinner.hide();
				jQuery('.assignee_editable').prop('disabled', false);
			}
		});
	});
	jQuery('#auto_dates').on('change', function(e) {
		e.preventDefault();
		var dates = jQuery('#auto_dates').is(":checked");
		if(dates == true) {
			dates = 1;
		} else {
			dates = 0;
		}
		var project_id = jQuery('#post_ID').val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_switch_auto_dates',
			'project_id' : project_id,
			'dates' : dates,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#auto_dates').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#auto_dates').prop('disabled', false);
			} else {
				spinner.hide();
				jQuery('#auto_dates').prop('disabled', false);
			}
		});
	});

	// Add members select options.
	jQuery('#team_members').select2( {
		//allowClear: true,
	} );

	// Delete project updates
	jQuery('#delete_project_updates').click(function(e) {
		e.preventDefault();
		var project_id = jQuery('#post_ID').val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action'     : 'pto_delete_project_updates',
			'project_id' : project_id,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#delete_project_updates').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#delete_project_updates').prop('disabled', false);
			} else {
				spinner.hide();
				jQuery('#delete_project_updates').prop('disabled', false);
				jQuery.colorbox.resize();
				location.reload();
			}
		});
	});


	//new
	jQuery(document).on('click', 'button.milestone_mark_as_paid', function(e){
		e.preventDefault();
		e.stopPropagation();

		jQuery(this).prop("disabled",true);
		jQuery(this).text("Please wait...");

		var key = jQuery(this).attr("data-ms");
		var item = jQuery(this).attr("data-project");
		var paid_status = jQuery(this).attr("data-status");
		var data = {
			'action' : 'pto_update_milestone_paid_status',
			'key' : key,
			'item' : item,
			'paid_status' : paid_status,
			'type' : 'project',
			'pto_nonce' : localisation.global_nonce
		};

		//console.log(data);return false;
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
		}).always(function(response){
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				alert(response.errors);
				return false;
			} else {
				//jQuery('#edit-milestone-messages').html(response.message);
				window.location.reload();
			}
		});
	});
});
