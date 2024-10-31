jQuery(document).ready(function() {
	jQuery('#task_pc').on('change', function() {
		var pc = jQuery(this).val();
		if(pc == 0) {
			jQuery('#task_status').val('pending');
		} else if(pc == 100) {
			jQuery('#task_status').val('complete');
		} else {
			jQuery('#task_status').val('progress');
		}
	});
	jQuery('button.s_button').on('click', function(e){
		e.preventDefault();
		//jQuery('#cqpim_overlay').show();
		setTimeout(function(){
			jQuery('#publish').trigger('click');
		}, 500);
	});
	jQuery(document).on('click', 'button.delete_file', function(e){		
		e.preventDefault();
		var attID = jQuery(this).data('id');
		jQuery('#cqpim_overlay').show();
		var hiddenField = '<input type="hidden" name="delete_file[]" value="' + attID + '" />';
		jQuery(this).parents('div.inside').prepend(hiddenField);
		jQuery('#publish').trigger('click');
	});
	var hasTimer = false;
	jQuery('.start-timer-btn').on('click', function(e) {
		e.preventDefault();
		hasTimer = true;
		jQuery('.timer').timer({
			editable: false,
			format: '%H:%M:%S' 
		});
		jQuery(this).addClass('hidden');
		jQuery('.pause-timer-btn, .remove-timer-btn').removeClass('hidden');
	});
	jQuery('.resume-timer-btn').on('click', function(e) {
		e.preventDefault();
		jQuery('.timer').timer('resume');
		jQuery(this).addClass('hidden');
		jQuery('.pause-timer-btn, .remove-timer-btn').removeClass('hidden');
	});
	jQuery('.pause-timer-btn').on('click', function(e) {
		e.preventDefault();
		jQuery('.timer').timer('pause');
		jQuery(this).addClass('hidden');
		jQuery('.resume-timer-btn').removeClass('hidden');
	});
	jQuery('.remove-timer-btn').on('click', function(e) {
		e.preventDefault();
		hasTimer = false;
		jQuery('.timer').timer('remove');
		jQuery(this).addClass('hidden');
		jQuery('.start-timer-btn').removeClass('hidden');
		jQuery('.pause-timer-btn, .resume-timer-btn').addClass('hidden');
	});
	jQuery('.timer').on('focus', function(e) {
		e.preventDefault();
		if(hasTimer) {
			jQuery('.pause-timer-btn').addClass('hidden');
			jQuery('.resume-timer-btn').removeClass('hidden');
		}
	});
	jQuery('.timer').on('blur', function(e) {
		e.preventDefault();
		if(hasTimer) {
			jQuery('.pause-timer-btn').removeClass('hidden');
			jQuery('.resume-timer-btn').addClass('hidden');
		}
	});

    jQuery( '#post' ).on( 'submit', function( event ) {
        // Prevent publishing
        event.preventDefault();
        var task_start    = jQuery( "input[name='task_start']" ).val();
        var task_deadline = jQuery( "input[name='task_deadline']" ).val();


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
            jQuery( this ).off( event ).submit();
        } else {
            alert( 'Deadline should be greater than the start date' );
            jQuery('#cqpim_overlay').hide();
        }
    });
});