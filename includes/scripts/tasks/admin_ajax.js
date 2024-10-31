jQuery(document).ready(function() {
	jQuery('#task_project_id').change(function(e) {
		e.preventDefault();
		update_milestone_dropdown();
	});
	jQuery('.time_remove').on('click', function(e) {
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
				location.reload();
			}				
		});
	});

	jQuery('#add_time_ajax').on('click', function(e) {
		e.preventDefault();
		jQuery('#wpwrap').append('<div class="shade"></div>');
		jQuery('#wpwrap').css("pointer-events","none");
		jQuery('body').append('<div id="timer_note_popup">'
			+'<h4>'+localisation.tasks.add_notes+' <button class="close-note" type="button" id="cboxClose"></button></h4>'
			+'<div class="form-group"><div class="input-group"><textarea rows="2" cols="20" id="timer_note_2" class="form-control input pto-textarea"></textarea></div></div>'
			+'<div class="timer_pop_btn"><button id="submit_time_ajax_2" class="piaBtn right">'
			+ localisation.tasks.submit_btn + '</button><button id="skip_time_ajax_2" class="piaBtn redColor">'
			+localisation.tasks.skip_btn+'</button></div></div>');
	});

	jQuery(document).on( 'click', '#submit_time_ajax_2, #skip_time_ajax_2', function(e) {
		var task_id = jQuery('#task_time_task').val();
		var time = jQuery('#task_time_value').val();
		var spinner = jQuery('#cqpim_overlay');
		var timer_note = jQuery('#timer_note_2').val();
		var data = {
			'action' : 'pto_add_timer_time',
			'task_id' : task_id,
			'time' : time,
			'timer_note': timer_note,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#add_time_ajax').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
			spinner.hide();
				jQuery('#add_time_ajax').prop('disabled', false);
				alert(response.message);
			} else {
				spinner.hide();
				jQuery('#add_time_ajax').prop('disabled', false);
				jQuery('#time_messages').html(response.message);
				location.reload();
			}				
		});
	});

	jQuery('#add_mtime_ajax').on('click', function(e) {
		e.preventDefault();
		jQuery('#wpwrap').append('<div class="shade"></div>');
		jQuery('#wpwrap').css("pointer-events","none");
		jQuery('body').append('<div id="timer_note_popup">'
			+'<h4>' + localisation.tasks.add_notes + ' <button class="close-note" type="button" id="cboxClose"></button></h4>'
			+'<div class="form-group"><div class="input-group"><textarea rows="2" cols="20" id="timer_note" class="form-control input pto-textarea"></textarea></div></div>'
			+'<div class="timer_pop_btn"><button id="submit_time_ajax" class="piaBtn right">'
			+ localisation.tasks.submit_btn + '</button><button id="skip_time_ajax" class="piaBtn redColor">'
			+ localisation.tasks.skip_btn + '</button></div></div>'); 
	});

	jQuery(document).on( 'click', '#submit_time_ajax, #skip_time_ajax', function(e) {
		e.preventDefault();
		var task_id = jQuery('#task_time_task').val();
		var hours = jQuery('#add_time_hours').val();
		var minutes = jQuery('#add_time_minutes').val();
		var timer_note = jQuery('#timer_note').val();
		var entry_date = jQuery('#add_time_entry_date').val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_add_manual_task_time',
			'task_id' : task_id,
			'hours' : hours,
			'minutes' : minutes,
			'timer_note': timer_note,
			'entry_date': entry_date,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#add_mtime_ajax').prop('disabled', true);
			},
		}).always(function(response){
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
			spinner.hide();
				jQuery('#add_mtime_ajax').prop('disabled', false);
				alert(response.errors);
			} else {
				spinner.hide();
				jQuery('#add_mtime_ajax').prop('disabled', false);
				location.reload();
			}				
		});
	});

	jQuery('.tooltip').on('click', function(e) {  
		jQuery('#wpwrap').append('<div class="shade"></div>');
		jQuery('#wpwrap').css("pointer-events","none");
		note = jQuery(this).data('value');
		jQuery('body').append('<div id="timer_note_popup">'
			+'<h4 class="font-blue">' + localisation.tasks.notes + '</h4><button class="close-note" type="button" id="cboxClose"></button><hr>'
			+'<textarea rows="2" cols="20" id="timer_note" readonly>'+ note +' </textarea> </div>'); 

	});

	jQuery(document).on('click', '.close-note', function(e) { 
		jQuery(".shade").remove();
		jQuery('#wpwrap').css("pointer-events","");
 		jQuery("#timer_note_popup").remove();
	});
 
	jQuery('#delete_task').on('click', function(e) {
		e.preventDefault();
		var c = confirm('It will delete this Task completely. Are you sure?');
		if ( c !== true ) {
			return false;
		}
		var task_id = jQuery(this).data('id');
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_delete_task_page',
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
			'action' : 'pto_delete_task_message',
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
});
function update_milestone_dropdown() {
		var project_id = jQuery('#task_project_id').val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_populate_project_milestone',
			'ID' : project_id,
			'pto_nonce' : localisation.global_nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#task_project_id').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#task_project_id').prop('disabled', false);
				jQuery('#task_milestone_id').html(response.options);
				jQuery('#task_owner').html(response.team_options);
			} else {
				spinner.hide();
				jQuery('#task_project_id').prop('disabled', false);
				jQuery('#task_milestone_id').html(response.options);
				jQuery('#task_owner').html(response.team_options);
			}
		});
}