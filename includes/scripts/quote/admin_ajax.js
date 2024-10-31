jQuery(document).ready(function() {
	
	// Update Contacts Dropdown in Quotes
	
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
			'type' : 'quote',
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
			if(response.error == true) {
				alert(response.errors);
			} else {
				jQuery('#edit_milestone_id').val(response.data.id);
				jQuery('#edit_milestone_title').val(response.data.title);
				jQuery('#edit_milestone_start').val(response.data.start);
				jQuery('#edit_milestone_end').val(response.data.deadline);
				jQuery('#edit_milestone_cost').val(response.data.cost);
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
		var item = jQuery('#post_ID').val();
		var key = jQuery('#edit_milestone_id').val();
		var title = jQuery('#edit_milestone_title').val();
		var start = jQuery('#edit_milestone_start').val();
		var deadline = jQuery('#edit_milestone_end').val();
		var cost = jQuery('#edit_milestone_cost').val();
		var data = {
			'action' : 'pto_update_milestone_data',
			'key' : key,
			'item' : item,
			'title' : title,
			'start' : start,
			'deadline' : deadline,
			'cost' : cost,
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
			if(response.error == true) {
				jQuery('#edit-milestone-messages').html(response.errors);
				jQuery.colorbox.resize();
			} else {
				jQuery('#edit-milestone-messages').html(response.message);
				jQuery.colorbox.resize();
				jQuery('#ms_title_' + response.data.id).html(response.data.title);
				jQuery('#ms_start_' + response.data.id).html(response.data.start);
				jQuery('#ms_deadline_' + response.data.id).html(response.data.deadline);
				jQuery('#ms_cost_' + response.data.id).html(response.data.cost);
				setTimeout(function() {
					jQuery.colorbox.close();
					jQuery('#edit-milestone-messages').html('');
				}, 1000);
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
		var data = {
			'action' : 'pto_delete_milestone_data',
			'key' : key,
			'item' : item,
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
			if(response.error == true) {
				jQuery('#delete-milestone-messages').html(response.errors);
				jQuery.colorbox.resize();
			} else {
				jQuery('#delete-milestone-messages').html(response.message);
				jQuery.colorbox.resize();
				jQuery(response.container).remove();
				setTimeout(function() {
					jQuery.colorbox.close();
					jQuery('#delete-milestone-messages').html('');
				}, 1000);
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
	});
	
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
		var ms_id = jQuery('#add_task_milestone_id').val();
		var title = jQuery('#add_task_title').val();
		var description = jQuery('#add_task_description').val();
		var start = jQuery('#add_task_start').val();
		var deadline = jQuery('#add_task_finish').val();
		var task_time = jQuery('#add_task_time').val();
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
			'type' : 'quote',
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
			},
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
				jQuery( ".dd-tasks" ).sortable( "refresh" );
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
		var project_id = jQuery('#post_ID').val();
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
			'type' : 'quote',
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
			},
		}).done(function(response){
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
		var data = {
			'action' : 'pto_update_task_data',
			'task_id' : task_id,
			'item' : item,
			'title' : title,
			'start' : start,
			'deadline' : deadline,
			'desc' : desc,
			'time' : time,
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
		var data = {
			'action' : 'pto_delete_task_data',
			'task_id' : task_id,
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
			if(response.error == true) {
				jQuery('#delete-task-messages').html(response.errors);
				jQuery.colorbox.resize();
			} else {
				jQuery('#delete-task-messages').html(response.message);
				jQuery.colorbox.resize();
				jQuery(response.container).remove();
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
		var data = {
			'action' : 'pto_delete_task_data',
			'task_id' : task_id,
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
			if(response.error == true) {
				jQuery('#delete-subtask-messages').html(response.errors);
				jQuery.colorbox.resize();
			} else {
				jQuery('#delete-subtask-messages').html(response.message);
				jQuery.colorbox.resize();
				jQuery(response.container).remove();
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
	});	
	
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
	});	
	
	jQuery('#send_quote').click(function(e) {
		e.preventDefault();
		jQuery('#messages').html('');
		var quote_id = jQuery(this).data('id');
		var spinner = jQuery('#cqpim_overlay');
		var messages = jQuery('#messages');
		var domain = document.domain;
		var data = {
			'action' : 'pto_process_quote_emails',
			'quote_id' : quote_id,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#send_quote').prop('disabled', true);
			},
		}).always(function(response) {
		console.log(response);
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#send_quote').prop('disabled', false);
				jQuery('#messages').html(response.errors);
			} else {
				spinner.hide();
				jQuery('#send_quote').prop('disabled', false);
				jQuery('#messages').html(response.message);
				location.reload();
			}
		});
	});
	jQuery('#apply-template-action').click(function(e) {
		e.preventDefault();
		jQuery('#apply-template-messages').html('');
		var quote_id = jQuery(this).val();
		var type = jQuery(this).attr('data-type');
		var template = jQuery('#template_choice').val();
		var hid = jQuery(this).data('hid');
		var hwe = jQuery(this).data('hwe');
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
	jQuery('.convert_confirm').click(function(e) {
		e.preventDefault();
		jQuery('#convert-error').html('');
		var quote_id = jQuery(this).val();
		var messages = jQuery('#convert-error');
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_manual_quote_convert',
			'quote_id' : quote_id,
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
				jQuery('.convert_confirm').prop('disabled', true);
			},
		}).always(function(response) {
			console.log(response);		
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('.convert_confirm').prop('disabled', false);
				jQuery(messages).html(response.message);
				jQuery.colorbox.resize();
			} else {
				spinner.hide();
				jQuery('.convert_confirm').prop('disabled', false);
				jQuery(messages).html(response.message);
				jQuery.colorbox.resize();
				var url = response.url.replace("&amp;", "&");
				console.log(url);
				window.location.replace(url);
			}
		});
	});

	// Call ajax function to send the quote messages.
	jQuery('#add_message_ajax').click(function(e) {
		e.preventDefault();
		var visibility     = jQuery('#add_message_visibility').val();
		var message        = jQuery('#add_message_text').val();
		var quote_id       = jQuery('#post_ID').val();
		var who            = jQuery('#message_who').val();
		var spinner        = jQuery('#cqpim_overlay');
		var send_to_client = 0;

		if ( jQuery( '#send_to_client' ).is( ':checked' ) ) {
			send_to_client = 1;
		}

		var data = {
			'action'         : 'pto_add_message_to_quote',
			'visibility'     : visibility,
			'message'        : message,
			'quote_id'       : quote_id,
			'who'            : who,
			'send_to_client' : send_to_client,
			'pto_nonce' 	 : localisation.global_nonce
		};

		jQuery.ajax({
			url        : ajaxurl,
			data       : data,
			type       : 'POST',
			dataType   : 'json',
			beforeSend : function(){
				spinner.show();
				jQuery('#add_message_trigger').prop('disabled', true);
				jQuery.colorbox.resize();
			},
		}).always( function( response ) {
			spinner.hide();
			jQuery('#add_message_ajax').prop('disabled', false);
			if( response.error == true ) {
				jQuery('#message_messages').html('<p>' + response.message + '</p>');
			} else {
				jQuery('#message_messages').html('<p>' + response.message + '</p>');
				jQuery.colorbox.resize();
				location.reload();
			}
		});
	});

	// Call ajax function to delete the quote messages.
	jQuery('button.delete_message').click(function(e) {
		e.preventDefault();
		var quote_id = jQuery('#post_ID').val();
		var key      = jQuery(this).data('id');
		var spinner  = jQuery('#cqpim_overlay');
		var data     = {
			'action'   : 'pto_delete_quote_message',
			'quote_id' : quote_id,
			'key'      : key,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url        : ajaxurl,
			data       : data,
			type       : 'POST',
			dataType   : 'json',
			beforeSend : function(){
				spinner.show();
				jQuery('#button.delete_message').prop('disabled', true);
			},
		}).done(function(){
				location.reload();
		});
	});

});