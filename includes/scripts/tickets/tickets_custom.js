jQuery(document).ready(function() {
	var scrollTop = localStorage.getItem('scrollTop');
    if (scrollTop !== null) {
        jQuery(window).scrollTop(Number(scrollTop));
        localStorage.removeItem('scrollTop');
    }
	jQuery('#publish').click(function(event) {
		localStorage.setItem('scrollTop', jQuery(window).scrollTop());
		return true;
	});
	jQuery('button.s_button').on('click', function(e){
		e.preventDefault();
		setTimeout(function(){
			jQuery('#publish').trigger('click');
		}, 500);
	});
	jQuery('button.save').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox.close();
		setTimeout(function(){
			jQuery('#publish').trigger('click');
		}, 500);
	});
	jQuery('button.cancel-colorbox').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox.close();
	});
	jQuery(document).on('click', 'button.delete_stage_conf', function(e){
		e.preventDefault();
		var id = jQuery(this).val();
			jQuery.colorbox({
				'inline': true,
				'fixed': true,
				'href': '#delete-milestone-div-' + id,	
				'opacity': '0.5',
			});			
	});
	jQuery(document).on('click', 'button.cancel_delete_stage', function(e){
		e.preventDefault();
		jQuery.colorbox.close();
	});
	jQuery(document).on('click', 'button.cancel_delete_task', function(e){
		e.preventDefault();
		jQuery.colorbox.close();
	});
	jQuery(document).on('click', 'button.delete_file', function(e){		
		e.preventDefault();
		var attID = jQuery(this).data('id');
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_delete_file',
			'ID' : attID,
			'pto_nonce' : localisation.global_nonce
		};	
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
			},
		}).done(function(response){
			spinner.hide();
			location.reload();
		});			
	});
	jQuery('a.save').on('click', function(e) {
		e.preventDefault();
		jQuery('#cqpim_overlay').show();
		jQuery('#publish').trigger('click');
	});
	jQuery('#delete_task').on('click', function(e) {
		e.preventDefault();
		// Prevent event trigger when title is focus
		if (jQuery("#title").is(":focus")) {
			return;
		}
		var c = confirm('It will delete this Task completely. Are you sure?');
		if ( c !== true ) {
			return false;
		}
		var task_id = jQuery(this).data('id');
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_delete_support_page',
			'task_id' : task_id,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#delete_task').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
			spinner.hide();
				jQuery('#delete_task').prop('disabled', false);
			} else {
				spinner.hide();
				jQuery('#delete_task').prop('disabled', false);
				window.location.href = response.redirect;			
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
			'action' : 'pto_delete_support_message',
			'project_id' : project_id,
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
				jQuery('#button.delete_message').prop('disabled', true);
			},
		}).done(function(){
			location.reload();
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
		var item = jQuery('#post_ID').val();
		var key = jQuery('#edit_milestone_id').val();
		var title = jQuery('#edit_milestone_title').val();
		var start = jQuery('#edit_milestone_start').val();
		var deadline = jQuery('#edit_milestone_end').val();
		var cost = jQuery('#edit_milestone_cost').val();
		var fcost = jQuery('#edit_milestone_fcost').val();
		var status = jQuery('#edit_milestone_status').val();
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
				jQuery('#milestone-' + response.data.id + ' .dd-milestone-status').html(response.data.status_string);
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
				jQuery('#add_task_owner').val("")
				jQuery( ".dd-tasks" ).sortable( "refresh" );
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
		var owner = jQuery('#add_subtask_owner').val();
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
			'owner' : owner,
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
					jQuery('#task-' + item_id).removeClass('overdue');
					jQuery('#task-' + item_id + ' .dd-task-status').html(response.status);
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

	jQuery('a#apply-template').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox({
			'width' : '500px',
			'maxWidth':'95%',
			'inline': true,
			'href': '#apply-template-div',
			'opacity': '0.5',
		});	
		jQuery.colorbox.resize();
	});
	jQuery('a#clear-all').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox({
			'width' : '500px',
			'maxWidth':'95%',
			'inline': true,
			'href': '#clear-all-div',
			'opacity': '0.5',
		});	
		jQuery.colorbox.resize();
	});
	jQuery('#send_ticket_invoice').on('click', function(e){
		e.preventDefault();
		var pid = jQuery('#post_ID').val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_send_ticket_invoice',
			'pid' : pid,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#send_ticket_invoice').prop('disabled', true);
			},
		}).always(function(response){
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#send_ticket_invoice').prop('disabled', false);
				alert(response.errors);
			} else {
				spinner.hide();
				jQuery('#send_ticket_invoice').prop('disabled', false);
				location.reload();
			}
		});		
	});
	jQuery('#ticket_client').on('change', function(e) {
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
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
			} else {
				spinner.hide();
				jQuery('#client_contact').prop('disabled', false);
				jQuery('#client_contact').html(response.contacts);
			}
		});
	});

	/* Create popup to modify priorities */
	jQuery('#add_support_ticket_priority').on('click', function(e) {
		e.preventDefault();
		jQuery.colorbox({
			'maxWidth': '95%',
			'width' : '400px',
			//'height': '500px',
			'maxHeight':'480px',
			'scrolling': true,
			'inline': true,
			'fixed': true,
			'href': '#add_priroty_popup',
		});
		jQuery.colorbox.resize();
	});

	/** Add new element in support priority list */
	jQuery('.btn-plus').click(function() {
		var remove_title = jQuery( this ).data( 'remove-title' );
		element = '<tr class="support_ticket_priorities_item"><td><div class="form-group"><div class="input-group"><input class="form-control input support_ticket_priorities_label" type="text" name="priority_name[]" ></div></div></td><td><input type="color" class="support_ticket_priorities_color" name="priority_color[]"></td><td><input type="button" class="btn-minus" style="margin: 0;" title="' + remove_title + '"></td></tr>';
		jQuery( '.support_ticket_priorities_list' ).append( element );
		jQuery.colorbox.resize();
	});

	/** Remove element from support priority list */
	jQuery( '.support_ticket_priorities_list').on('click', '.btn-minus', function() {
		jQuery( this ).closest( '.support_ticket_priorities_item' ).remove();
	});

	/* Call ajax to update the priority in the table */
	jQuery('#add_new_priority_ajax').on('click', function(e) {
		e.preventDefault();
		var priority_name  = [];
		jQuery('.support_ticket_priorities_label').each(function() {
			priority_name.push( jQuery(this).val() );
		});

		var priority_color = [];
		jQuery('.support_ticket_priorities_color').each(function() {
			priority_color.push( jQuery(this).val() );
		});

		var spinner = jQuery('#cqpim_overlay');

		var data = {
			'action' : 'pto_add_new_priority_label',
			'priority_name'  : priority_name,
			'priority_color' : priority_color,
		};

		jQuery.ajax ( {
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#add_new_priority_ajax').prop('disabled', true);
			},
		}).always(function(response){
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#add_new_priority_ajax').prop('disabled', false);
				alert(response.message);
			} else {
				alert( response.message );
				location.reload();
			}
		});
	});

    /* Call ajax to apply template */
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

});