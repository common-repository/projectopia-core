jQuery(document).ready(function() {
	jQuery('.cqpim-roles').repeater({
		show: function () {
			jQuery(this).slideDown();
		},
		hide: function (deleteElement) {
			if( confirm( jQuery( '.cqpim-roles' ).data( 'confirm-delete' ) ) ) {
				jQuery(this).slideUp(deleteElement);
			} else {
				return false;
			}
		},
		//isFirstItemUndeletable: true
	});
});