<?php
if ( $field['type'] == 'divider' ) {

    if ( ! isset($field['slide']) ) {
        $field['slide'] = false;
    }

?>
<label for="frm_slide_field_<?php echo $field['id'] ?>" class="frm_inline_label frm_help" title="<?php esc_attr_e( 'Collapsible: This section will slide open and closed.', 'formidable' ) ?>" >
	<input type="checkbox" id="frm_slide_field_<?php echo $field['id'] ?>" name="field_options[slide_<?php echo $field['id'] ?>]" value="1" <?php echo $field['slide'] ? ' checked="checked"' : ''; ?>/>
	<?php _e( 'Collapsible', 'formidable' ) ?>
</label>
<?php
    if ( ! isset($field['repeat']) ) {
        $field['repeat'] = false;
    }
?>
<label for="frm_repeat_field_<?php echo $field['id'] ?>" class="frm_inline_label frm_help" title="<?php esc_attr_e( 'Repeatable: This section can be repeated when viewing your form.', 'formidable' ) ?>">
	<input type="checkbox" id="frm_repeat_field_<?php echo $field['id'] ?>" name="field_options[repeat_<?php echo $field['id'] ?>]" class="frm_repeat_field" value="1" <?php echo $field['repeat'] ? ' checked="checked"' : ''; ?> />
	<?php _e( 'Repeatable', 'formidable' ) ?>
</label>
<?php
}