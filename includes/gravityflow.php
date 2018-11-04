<?php

if ( !defined( 'ABSPATH' ) ) exit;


function rs_gfn_add_metabox( $form, $entry ) {
	if ( !is_admin() ) return;
	$u = get_current_user_id();
	if ( $u != 2 ) return;
	
	$field_types = array('notes' => "Notes");
	
	// Allow adding multiple custom note fields
	$field_types = apply_filters( 'rs-gfn-default-note-fields', $field_types, $form, $entry );
	
	// Just in case you screw up the filter :)
	if ( empty($field_types) ) {
		echo '(RS Gravity Flow Notes: No note fields specified.)';
		return;
	}
	
	// Get our fields
	$fields = array();
	
	foreach( $field_types as $key => $label ) {
		$fields[] = array(
			'key' => $key,
			'label' => $label,
			'html_id' => 'gfn-admin-notes_' . $key,
			'value' => gform_get_meta( $entry['id'], 'gfn-admin-notes_' . $key ),
			'cols' => 150,
			'rows' => 3,
			
		);
	}
	?>
	<div id="postbox-container-rs-gfn" class="postbox-container rs-gfn-box" style="display: none;">
		<input type="hidden" id="rs-gfn-nonce" value="<?php echo esc_attr(wp_create_nonce('save-gfn-notes')); ?>">
		<input type="hidden" id="rs-gfn-form_id" value="<?php echo esc_attr($form['id']); ?>">
		<input type="hidden" id="rs-gfn-entry_id" value="<?php echo esc_attr($entry['id']); ?>">
		<input type="hidden" id="rs-gfn-fields" value="<?php echo esc_attr(json_encode($fields)); ?>">
		
		<div class="postbox">
			<h3><label for="<?php echo esc_attr($fields[0]['html_id']); ?>">Admin Notes</label></h3>
			
			<div class="inside">
				<div class="gfn-admin-notes">
					
					<div class="gfn-admin-notes-list">
					<?php
					foreach( $fields as $i => $f ) {
						echo '<div class="gfn-note gfn-note-'. $i .'">';
							echo '<p class="gfn-label"><label for="'. esc_attr($f['html_id']) .'"><strong>'. $f['label'] .'</strong></label></p>';
							echo '<div class="gfn-textarea">';
								echo '<textarea id="'. esc_attr($f['html_id']) .'" data-key="'. esc_attr($f['key']) .'" cols="'. intval($f['cols']) .'" rows="'. intval($f['rows']) .'">'. esc_textarea($f['value']) .'</textarea>';
								echo ' <a href="#" class="button button-secondary gfn-save-single-note" data-target="'. esc_attr($f['html_id']) .'">Save</a>';
							echo '</div>';
						echo '</div>';
					}
					?>
					</div>
					
				</div>
			</div>
		</div>
	</div>
	<?php
}
add_action( 'gravityflow_entry_detail_content_before', 'rs_gfn_add_metabox', 30, 2 );


function rs_gfn_save_field() {
	$data = isset($_REQUEST['gfn_data']) ? stripslashes_deep($_REQUEST['gfn_data']) : false;
	if ( empty($data['entry_id']) ) return;
	
	// Validate nonce to make sure this request is legit
	if ( !wp_verify_nonce($data['nonce'], 'save-gfn-notes') ) _rs_gfn_die_ajax_error('Nonce verification failed, session timed out. Reload the page and try again.');
	
	// Get submitted data
	$entry_id = $data['entry_id'];
	$form_id = $data['form_id'];
	$key = $data['key']; // Field being updated
	$value = $data['value']; // Value being saved
	
	$form = GFAPI::get_form( $form_id );
	if ( empty($form['id']) ) _rs_gfn_die_ajax_error('Invalid form #' . $form_id);
	
	$entry = GFAPI::get_entry( $entry_id );
	if ( empty($entry['id']) ) _rs_gfn_die_ajax_error('Invalid entry #' . $entry_id);
	
	// Get field types that we enable
	$field_types = array('notes' => "Notes");
	$field_types = apply_filters( 'rs-gfn-default-note-fields', $field_types, $form, $entry );
	if ( !isset($field_types[$key]) ) _rs_gfn_die_ajax_error('Invalid field type "'. esc_html($key) .'"');
	
	// Save the value
	gform_update_meta( $entry['id'], 'gfn-admin-notes_' . $key, $value, $form['id'] );
	
	echo json_encode(array('result' => 1));
	exit;
}
add_action( 'wp_ajax_rs-gfn-save-note', 'rs_gfn_save_field' );

function _rs_gfn_die_ajax_error( $message ) {
	echo json_encode(array('result' => 0, 'error_message' => $message));
	exit;
}