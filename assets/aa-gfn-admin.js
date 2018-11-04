/* A+A Gravity Flow Notes */
jQuery(function() {
	var $container = jQuery('#postbox-container-aa-gfn');

	var gfn = {
		nonce: jQuery('#aa-gfn-nonce').val(),
		form_id: jQuery('#aa-gfn-form_id').val(),
		entry_id: jQuery('#aa-gfn-entry_id').val(),
		fields: JSON.parse( jQuery('#aa-gfn-fields').val() )
	};

	var has_any_unsaved_changes = false;

	var $fields = $container.find('textarea');

	// Make a field "dirty" if the content has changed from what is currently saved.
	var test_field_dirty = function( $textarea ) {
		var clean_value = $textarea.data('clean_value');

		if ( $textarea.val() === clean_value ) {
			$textarea.closest('.gfn-note').removeClass('gfn-dirty');

			if ( $container.find('.gfn-note.gfn-dirty').length < 1 ) has_any_unsaved_changes = false;
		}else{
			$textarea.closest('.gfn-note').addClass('gfn-dirty');
			has_any_unsaved_changes = true;
		}
	};

	// Make the field "clean" aka it matches the value that is currently saved.
	var set_field_clean = function( $textarea, saved_value ) {
		if ( typeof saved_value !== "undefined" ) {
			// Use the saved value as the clean value. If the user typed while ajax was running, it might actually be dirty!
			$textarea.data('clean_value', saved_value );
		}else{
			$textarea.data('clean_value', $textarea.val());
		}

		test_field_dirty( $textarea );
	};

	// Save the field via ajax, and mark it as clean.
	var save_field = function( $button ) {
		var target_id = $button.attr('data-target');
		var $textarea = jQuery( '#' + target_id );
		var field_key = $textarea.attr('data-key');
		var field_value = $textarea.val();

		// If still loading, do nothing
		var $note_parent = $textarea.closest('.gfn-note');

		if ( $note_parent.hasClass('gfn-loading') ) {
			$button.text('Hang on, still saving...');
			return false;
		}

		$note_parent.addClass('gfn-loading');

		// Prepare ajax payload
		var gfn_submitted_data = {
			action: 'aa-gfn-save-note',
			gfn_data: {
				nonce: gfn.nonce,
				entry_id: gfn.entry_id,
				form_id: gfn.form_id,
				key: field_key,
				value: field_value
			}
		};

		jQuery.getJSON(
			ajaxurl,
			gfn_submitted_data,
			function( data, status, xhr ) {
				if ( typeof data.result !== "undefined" ) {
					if ( data.result ) {
						// Success
						// Set clean value of the field. This might not match the field if the ajax response took a long time and the user edited the textarea.
						set_field_clean( $textarea, gfn_submitted_data.value );
					}else{
						if ( typeof data.error_message !== "undefined" ) {
							// Error given
							alert( data.error_message );
						}else{
							// Error, but no details
							alert('Error saving notes, no details given');
						}
					}
				}
			}
		).fail(function() {
			// Warn about long messages
			if ( field_value.length > 5000 ) {
				alert('Message failed to save. It is probably because the message was too long. Try shortening the message to around 7000 characters (currently ' + field_value.length + ' characters). The actual character limit depends on your server configuration.');
				return;
			}

			// Error usually by connection or http code
			alert('Error saving notes: connection or other error occurred. Advanced users, check the network tab');
		}).always(function() {
			$note_parent.removeClass('gfn-loading');
			$button.text('Save');
		});
	};

	// Browser warning for unsaved changes
	jQuery(window).bind('beforeunload', function() {
		if( has_any_unsaved_changes )  return "Some notes have not been saved.";
	});

	// Initialize the clean state
	$fields.each(function() {
		set_field_clean( jQuery(this) );
	});

	// Monitor when typing into text field to mark it as dirty.
	$fields.on('keyup', function() { test_field_dirty( jQuery(this) ); });

	// Save notes when clicking the button
	$container.on('click', '.gfn-save-single-note', function() { save_field( jQuery(this) ); return false; });

	// Now that we are all set up, let's move the container so it's above the timeline, then reveal it. It's hidden by default.
	jQuery('#postbox-container-2').before( $container );
	$container.css('display', '');
});