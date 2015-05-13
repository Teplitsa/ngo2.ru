<?php

class FrmProFormsHelper{

	public static function setup_new_vars( $values ) {

        foreach ( self::get_default_opts() as $var => $default ) {
            $values[$var] = FrmAppHelper::get_param($var, $default);
        }
        return $values;
    }

	public static function setup_edit_vars( $values ) {
        $record = FrmForm::getOne($values['id']);
        foreach ( array( 'logged_in' => $record->logged_in, 'editable' => $record->editable) as $var => $default)
            $values[$var] = FrmAppHelper::get_param($var, $default);

		foreach ( self::get_default_opts() as $opt => $default ) {
            if ( ! isset($values[$opt]) ) {
                $values[$opt] = ( $_POST && isset($_POST['options'][$opt]) ) ? $_POST['options'][$opt] : $default;
            }

            unset($opt, $default);
        }

        return $values;
    }

    public static function get_sub_form($field_name, $field, $args = array()) {
        $defaults = array(
            'repeat'    => 0,
            'errors'    => array(),
        );

        $args = wp_parse_args($args, $defaults);

        $subform = FrmForm::getOne($field['form_select']);
		if ( empty( $subform ) ) {
			return;
		}

        $subfields = FrmField::get_all_for_form($field['form_select']);

?>
<input type="hidden" name="<?php echo esc_attr( $field_name ) ?>[form]" value="<?php echo esc_attr( $field['form_select'] ) ?>" />
<?php

        if ( empty($subfields) ) {
            return;
        }

        $repeat_atts = array(
            'form'  => $subform,
            'fields' => $subfields,
            'errors' => $args['errors'],
            'parent_field' => $field,
            'repeat'    => $args['repeat'],
        );

        if ( empty($field['value']) ) {
			// Row count must be zero if field value is empty
			$start_rows = apply_filters( 'frm_repeat_start_rows', 1, $field );

			for ( $i = 0, $j = $start_rows; $i < $j; $i++ ) {
				// add an empty sub entry
				$repeat_atts['row_count'] = $repeat_atts['i'] = $i;
				self::repeat_field_set( $field_name, $repeat_atts );
			}
            return;
        }

		$row_count = 0;
        foreach ( (array) $field['value'] as $k => $checked ) {
            $repeat_atts['i'] = $k;

            if ( ! isset($field['value']['form']) ) {
                // this is not a posted value from moving between pages
                $checked = apply_filters('frm_hidden_value', $checked, $field);
                if ( empty($checked) || ! is_numeric($checked) ) {
                    continue;
                }
?>
<input type="hidden" name="<?php echo esc_attr( $field_name ) ?>[id][]" value="<?php echo esc_attr( $checked ) ?>" />
<?php
                $repeat_atts['i'] = 'i'. $checked;
                $repeat_atts['entry_id'] = $checked;
            } else if ( $k === 'id' ) {
                foreach ( $checked as $entry_id ) {
?>
<input type="hidden" name="<?php echo esc_attr( $field_name ) ?>[id][]" value="<?php echo esc_attr( $entry_id ) ?>" />
<?php
                    unset($entry_id);
                }
                continue;
            } else if ( $k === 'form' ) {
                continue;
			} else if ( strpos( $k, 'i' ) === 0 ) {
				// include the entry id when values are posted
				$repeat_atts['entry_id'] = absint( str_replace( 'i', '', $k ) );
            }

			// Keep track of row count
			$repeat_atts['row_count'] = $row_count;
			$row_count++;

            // show each existing sub entry
            self::repeat_field_set($field_name, $repeat_atts);
            unset($k, $checked);
        }

        unset($subform, $subfields);
    }

    public static function repeat_field_set($field_name, $args = array() ) {
        $defaults = array(
            'i'         => 0,
            'entry_id'  => false,
            'form'      => false,
            'fields'    => array(),
            'errors'    => array(),
            'parent_field' => 0,
            'repeat'    => 0,
			'row_count'	=> false,
        );
        $args = wp_parse_args($args, $defaults);

        if ( empty($args['parent_field']) ) {
            return;
        }

        if ( is_numeric($args['parent_field']) ) {
            $args['parent_field'] = (array) FrmField::getOne($args['parent_field']);
            $args['parent_field']['format'] = isset($args['parent_field']['field_options']['format']) ? $args['parent_field']['field_options']['format'] : '';
        }

        FrmFormsHelper::maybe_get_form($args['form']);

        if ( empty($args['fields']) ) {
            $args['fields'] = FrmField::get_all_for_form($args['form']->id);
        }

        $values = array();

        if ( $args['fields'] ) {
            if ( empty($args['entry_id']) ) {
                $values = FrmEntriesHelper::setup_new_vars($args['fields'], $args['form']);
            } else {
                $entry = FrmEntry::getOne($args['entry_id'], true);
                if ( $entry && $entry->form_id == $args['form']->id ) {
                    $values = FrmAppHelper::setup_edit_vars($entry, 'entries', $args['fields']);
                } else {
                    return;
                }
            }
        }

        $format = isset($args['parent_field']['format']) ? $args['parent_field']['format'] : '';
        $end = false;
        $count = 0;
        foreach ( $values['fields'] as $subfield ) {
            if ( 'end_divider' == $subfield['type'] ) {
                $end = $subfield;
            } else if ( $subfield['type'] != 'hidden' ) {
                if ( isset( $subfield['conf_field'] ) && $subfield['conf_field'] ) {
                    $count = $count + 2;
                } else {
                    $count++;
                }
            }
            unset($subfield);
        }
        if ( $args['repeat'] ) {
            $count++;
        }

        $classes = array(
            2 => 'half',
            3 => 'third',
            4 => 'fourth',
            5 => 'fifth',
            6 => 'sixth',
            7 => 'seventh',
            8 => 'eighth',
        );

        $field_class = ( ! empty($format) && isset($classes[$count]) ) ? $classes[$count] : '';

        echo '<div id="frm_section_'. $args['parent_field']['id'] .'-'. $args['i'] .'" class="frm_repeat_'. ( empty($format) ? 'sec' : $format ) .' frm_repeat_'. $args['parent_field']['id'] . ( $args['row_count'] === 0 ? ' frm_first_repeat' : '' ) . '">' . "\n";

        $label_pos = 'top';
        $field_num = 1;
        foreach ( $values['fields'] as $subfield ) {
            if ( 'end_divider' == $subfield['type'] ) {
                continue;
            }

            $subfield_name = $field_name .'['. $args['i'] .']['. $subfield['id'] .']';
            $subfield_plus_id = '-'. $args['i'];
            $subfield_id = $subfield['id'] .'-'. $args['parent_field']['id'] . $subfield_plus_id;

            if ( $args['parent_field'] && ! empty($args['parent_field']['value']) && isset($args['parent_field']['value']['form']) && isset($args['parent_field']['value'][$args['i']]) && isset($args['parent_field']['value'][$args['i']][$subfield['id']]) ) {
                // this is a posted value from moving between pages, so set the POSTed value
                $subfield['value'] = $args['parent_field']['value'][$args['i']][$subfield['id']];
            }

            if ( !empty($field_class) ) {
                if ( 1 == $field_num ) {
                    $subfield['classes'] .= ' frm_first_'. $field_class;
                } else if ( $count == $field_num ) {
                    $subfield['classes'] .= ' frm_last_'. $field_class;
                } else {
                    $subfield['classes'] .= ' frm_'. $field_class;
                }
            }
            $field_num++;

            if ( 'top' == $label_pos && in_array($subfield['label'], array( 'top', 'hidden', '')) ) {
                // add placeholder label if repeating
                $label_pos = 'hidden';
            }

			$field_args = array(
				'field_name'    => $subfield_name,
				'field_id'      => $subfield_id,
				'field_plus_id' => $subfield_plus_id,
				'section_id'     => $args['parent_field']['id'],
			);

            if ( apply_filters('frm_show_normal_field_type', true, $subfield['type']) ) {
				echo FrmFieldsHelper::replace_shortcodes( $subfield['custom_html'], $subfield, $args['errors'], $args['form'], $field_args );
            } else {
				do_action( 'frm_show_other_field_type', $subfield, $args['form'], $field_args );
            }

            unset($subfield_name, $subfield_id);

            do_action('frm_get_field_scripts', $subfield, $args['form']);
        }

        if ( ! $args['repeat'] ) {
            echo '</div>'. "\n";
            return;
        }

        $args['format'] = $format;
        $args['label_pos'] = $label_pos;
        $args['field_class'] = $field_class;
        echo self::repeat_buttons($args, $end);

        echo '</div>'. "\n";
    }

    public static function repeat_buttons($args, $end = false) {
        $args['end_format'] = 'icon';

        if ( ! $end ) {
            global $wpdb;

            // get end field
			$query = array( 'fi.form_id' => $args['parent_field']['form_id'], 'type' => 'end_divider', 'field_order >' => $args['parent_field']['field_order'] + 1 );
            $end = (array) FrmField::getAll($query, 'field_order', 1);

			foreach ( array( 'format', 'add_label' ,'remove_label', 'classes' ) as $o ) {
                if ( isset($end['field_options'][$o]) ) {
                    $end[$o] = $end['field_options'][$o];
                }
            }
        }

        if ( $end ) {
            $args['add_label'] = $end['add_label'];
            $args['remove_label'] = $end['remove_label'];

            if (  ! empty($end['format']) ) {
                $args['end_format'] = $end['format'];
            }
        }

        $triggers = self::repeat_button_html($args, $end);

        return apply_filters('frm_repeat_triggers', $triggers, $end, $args['parent_field'], $args['field_class']);
    }

    public static function repeat_button_html($args, $end) {
        $defaults = array(
            'add_icon'      => '',
            'remove_icon'   => '',
            'add_label'     => __( 'Add', 'formidable' ),
            'remove_label'  => __( 'Remove', 'formidable' ),
            'add_classes'   => ' frm_button',
            'remove_classes' => ' frm_button',
        );

        $args = wp_parse_args($args, $defaults);

        if ( ! isset($args['end_format']) && isset($args['format']) ) {
            $args['end_format'] = $args['format'];
        }

        if ( 'both' == $args['end_format'] ) {
            $args['remove_icon'] = '<i class="frm_icon_font frm_minus_icon"> </i> ';
            $args['add_icon'] = '<i class="frm_icon_font frm_plus_icon"> </i> ';
        } else if ( 'text' != $args['end_format'] ) {
            $args['add_label'] = $args['remove_label'] = '';
            $args['add_classes'] = ' frm_icon_font frm_plus_icon';
            $args['remove_classes'] = ' frm_icon_font frm_minus_icon';
        }

		// Hide Remove button on first row
		if ( $args['row_count'] === 0 ) {
			$args['remove_classes'] .= ' frm_hidden';
		}

		$classes = 'frm_form_field frm_'. $args['label_pos'] .'_container';
		$classes .= empty( $args['field_class'] ) ? '' : ' frm_last_' . $args['field_class'];
		// Get classes for end divider
		$classes .= ( $end && isset( $end['classes'] ) ) ? ' ' . $end['classes'] : '';

		$triggers = '<div class="' . esc_attr( $classes ) . '">';

        if ( 'hidden' == $args['label_pos'] && ! empty($args['format']) ) {
            $triggers .= '<label class="frm_primary_label">&nbsp;</label>';
        }

		$triggers .= '<a href="#" class="frm_remove_form_row' . esc_attr( $args['remove_classes'] ) . '" data-key="' . esc_attr( $args['i'] ) . '" data-parent="' . esc_attr( $args['parent_field']['id'] ) . '">' . $args['remove_icon'] . $args['remove_label'] . '</a> ';
		$triggers .= '<a href="#" class="frm_add_form_row' . esc_attr( $args['add_classes'] ) . '" data-parent="' . esc_attr( $args['parent_field']['id'] ) . '">' . $args['add_icon'] . $args['add_label'] . '</a>' . "\n";

        $triggers .= '</div>';

        return $triggers;
    }

    public static function load_chosen_js($frm_vars) {
        if ( isset($frm_vars['chosen_loaded']) && $frm_vars['chosen_loaded'] ) {
            ?>$('.frm_chzn').chosen({<?php echo apply_filters('frm_chosen_js', 'allow_single_deselect:true') ?>});<?php
        }
    }

    public static function hide_conditional_fields($frm_vars) {
		$fields = array( 'hide' => array(), 'check' => array() );
        if ( ! isset($frm_vars['hidden_fields']) || empty($frm_vars['hidden_fields']) ) {
			return $fields;
        }

        $display_none = $trigger_check = array();

        foreach ( (array) $frm_vars['hidden_fields'] as $field ) {
            foreach ( $field['hide_field'] as $i => $hide_field ) {
                if ( ! is_numeric($hide_field) || in_array($hide_field, $trigger_check) ) {
                    continue;
                }

                $observed_field = FrmField::getOne($hide_field);

                if ( ! $observed_field ) {
                    continue;
                }

                $trigger_check[] = $observed_field->id;
                if ( ! isset($field['hide_opt'][$i]) && $observed_field->type == 'data' && self::is_show_data_field($field) ) {
                    self::hide_some_data_types($field, $observed_field, $display_none);
                    continue;
                }

                if ( ! isset($field['hide_opt'][$i]) ) {
                    continue;
                }

                if ( $observed_field->type != 'data' ) {
                    $display_none[] = $field['id'];
                    continue;
                }

                if ( $field['hide_opt'][$i] != '' && in_array($observed_field->field_options['data_type'], array( 'radio', 'checkbox', 'select')) ) {
                    $display_none[] = $field['id'];
                } else if ( $field['hide_opt'][$i] == '' && self::is_show_data_field($field) ) {
                    self::hide_some_data_types($field, $observed_field, $display_none);
                }

            }
            unset($observed_field, $i, $hide_field);
        }

		$fields['hide'] = array_unique( $display_none );
		$fields['check'] = array_unique( $trigger_check );

		return $fields;
    }

    private static function hide_some_data_types($field, $observed_field, array &$display_none) {
        $observed_options = maybe_unserialize($observed_field->field_options);
        if ( in_array($observed_options['data_type'], array( 'checkbox', 'select')) ) {
            $display_none[] = $field['id'];
        }
    }

	public static function load_datepicker_js( $frm_vars ) {
        if ( ! isset($frm_vars['datepicker_loaded']) || empty($frm_vars['datepicker_loaded']) || ! is_array($frm_vars['datepicker_loaded']) ) {
            return;
        }

		$frmpro_settings = FrmProAppHelper::get_settings();

        reset($frm_vars['datepicker_loaded']);
        $datepicker = key($frm_vars['datepicker_loaded']);
		$load_lang = false;

        foreach ( $frm_vars['datepicker_loaded'] as $date_field_id => $options ) {
            if ( strpos($date_field_id, '^') === 0 ) {
                // this is a repeating field
                $trigger_id = 'input[id^="'. str_replace('^', '', $date_field_id) .'"]';
            } else {
                $trigger_id = '#'. $date_field_id;
            }
        ?>
$(document).on('focusin','<?php echo $trigger_id ?>', function(){
$.datepicker.setDefaults($.datepicker.regional['']);
$(this).datepicker($.extend($.datepicker.regional['<?php echo $options['locale'] ?>'],{dateFormat:'<?php echo $frmpro_settings->cal_date_format ?>',changeMonth:true,changeYear:true,yearRange:'<?php echo $options['start_year'] .':'. $options['end_year'] ?>',defaultDate:<?php echo empty($options['default_date']) ? "''" : 'new Date('. $options['default_date'] .')';
do_action('frm_date_field_js', $date_field_id, $options);
?>}));
});
<?php
			if ( ! empty( $options['locale'] ) && ! $load_lang ) {
				$load_lang = true;
				$base_url = FrmAppHelper::jquery_ui_base_url();
				wp_enqueue_script( 'jquery-ui-i18n', $base_url . '/i18n/jquery-ui-i18n.min.js' );
				// this was enqueued late, so make sure it gets printed
				add_action( 'wp_footer', 'print_footer_scripts', 21 );
			}
        }

        self::load_timepicker_js($datepicker, $frm_vars);
    }

    public static function load_timepicker_js($datepicker, $frm_vars) {
        if ( ! isset($frm_vars['timepicker_loaded']) || empty($frm_vars['timepicker_loaded']) || ! $datepicker ) {
            return;
        }

        foreach ( $frm_vars['timepicker_loaded'] as $time_field_id => $options ) {
            if ( ! $options ) {
                continue;
            }
?>
$(document.getElementById('<?php echo $datepicker ?>')).change(function(){frmFrontForm.removeUsedTimes(this,'<?php echo $time_field_id ?>');});
<?php
        }
    }

    public static function load_calc_js($frm_vars) {
        if ( ! isset($frm_vars['calc_fields']) || empty($frm_vars['calc_fields']) ) {
            return;
        }

        $calc_rules = array(
            'fields'    => array(),
            'calc'      => array(),
            'fieldKeys' => array(),
        );

        $triggers = array();

        foreach ( $frm_vars['calc_fields'] as $result => $field ) {
            $calc = $field['calc'];
            preg_match_all("/\[(.?)\b(.*?)(?:(\/))?\]/s", $calc, $matches, PREG_PATTERN_ORDER);

            $field_keys = $calc_fields = array();

            foreach ( $matches[0] as $match_key => $val ) {
                $val = trim(trim($val, '['), ']');
                $calc_fields[$val] = FrmField::getOne($val);
                if ( ! $calc_fields[$val] ) {
                    unset($calc_fields[$val]);
                    continue;
                }

                $html_field_id = '="field_'. $calc_fields[$val]->field_key;
                if ( $field['form_id'] != $calc_fields[$val]->form_id || in_array($calc_fields[$val]->type, array( 'radio', 'scale', 'checkbox')) ) {
                    $html_field_id = '^'. $html_field_id .'-';
                }
                $field_keys[$calc_fields[$val]->id] = '[id'. $html_field_id .'"]';

                $calc = str_replace($matches[0][$match_key], '['. $calc_fields[$val]->id .']', $calc);
            }


            $triggers[] = reset($field_keys);
            $calc_rules['calc'][$result] = array(
				'calc'      	=> $calc,
				'calc_dec'		=> $field['calc_dec'],
				'fields'    	=> array(),
            );
            $calc_rules['fieldKeys'] = $calc_rules['fieldKeys'] + $field_keys;

            foreach ( $calc_fields as $calc_field ) {
                $calc_rules['calc'][$result]['fields'][] = $calc_field->id;
                if ( isset($calc_rules['fields'][$calc_field->id]) ) {
                    $calc_rules['fields'][$calc_field->id]['total'][] = $result;
                } else {
                    $calc_rules['fields'][$calc_field->id] = array(
                        'total' => array($result),
                        'type'  => $calc_field->type,
                        'key'   => $field_keys[$calc_field->id],
                    );
                }

                if ( $calc_field->type == 'date' ) {
                    if ( ! isset($frmpro_settings) ) {
                        $frmpro_settings = new FrmProSettings();
                    }
                    $calc_rules['date'] = $frmpro_settings->cal_date_format;
                }
            }
        }

        echo '__FRMCALC='. json_encode($calc_rules) .";\n";

        // trigger calculations on page load
        if ( ! empty($triggers) ) {
            $triggers = array_unique($triggers);
            ?>$('<?php echo implode(',', $triggers) ?>').trigger({type:'change',selfTriggered:true});<?php
        }
    }

    public static function load_input_mask_js($frm_input_masks) {
        if ( empty($frm_input_masks) ) {
            return;
        }

        foreach ( (array) $frm_input_masks as $f_key => $mask ) {
            if ( ! $mask ) {
                continue;
			} else if ( $mask !== true ) {
				// this isn't used in the plugin, but is here for those using the mask filter
				?>$(document).on('focusin','<?php echo is_numeric($f_key) ? 'input[name="item_meta['. $f_key .']"]' : '#field_'. $f_key; ?>',function(){$(this).mask("<?php echo $mask ?>");});<?php
			}
            unset($f_key, $mask);
        }
    }

	public static function get_default_opts() {
        $frmpro_settings = new FrmProSettings();

        return array(
            'edit_value' => $frmpro_settings->update_value, 'edit_msg' => $frmpro_settings->edit_msg,
            'edit_action' => 'message', 'edit_url' => '', 'edit_page_id' => 0,
            'logged_in' => 0, 'logged_in_role' => '', 'editable' => 0, 'save_draft' => 0,
            'draft_msg' => __( 'Your draft has been saved.', 'formidable' ),
            'editable_role' => '', 'open_editable_role' => '-1',
            'copy' => 0, 'single_entry' => 0, 'single_entry_type' => 'user',
            'success_page_id' => '', 'success_url' => '', 'ajax_submit' => 0,
            'cookie_expiration' => 8000, 'prev_value' => __( 'Previous', 'formidable' ),
            'submit_align' => '',
        );
    }

	public static function get_taxonomy_count( $taxonomy, $post_categories, $tax_count = 0 ) {
		if ( isset( $post_categories[ $taxonomy . $tax_count ] ) ) {
			$tax_count++;
			$tax_count = self::get_taxonomy_count( $taxonomy, $post_categories, $tax_count );
        }
        return $tax_count;
    }

	public static function going_to_prev( $form_id ) {
        $back = false;
        if ( $_POST && isset($_POST['frm_next_page']) && $_POST['frm_next_page'] != '' ) {
            $prev_page = FrmAppHelper::get_param('frm_page_order_'. $form_id, false);
            if ( ! $prev_page || ( $_POST['frm_next_page'] < $prev_page ) ) {
                $back = true; //no errors if going back a page
            }
        }
        return $back;
    }

    public static function get_prev_button( $form, $class = '' ) {
        $html = '[if back_button]<input type="submit" value="[back_label]" name="frm_prev_page" formnovalidate="formnovalidate" class="frm_prev_page '. $class .'" [back_hook] />[/if back_button]';
        return self::get_draft_button( $form, $class, $html, 'back_button' );
    }

    // check if this entry is currently being saved as a draft
    public static function saving_draft() {
        $saving = ( $_POST && isset($_POST['frm_saving_draft']) && $_POST['frm_saving_draft'] == '1' && is_user_logged_in() ) ? true : false;
        return $saving;
    }

    public static function save_draft_msg( &$message, $form, $record = false ) {
        if ( ! self::saving_draft() ) {
            return;
        }

        $message = isset($form->options['draft_msg']) ? $form->options['draft_msg'] : __( 'Your draft has been saved.', 'formidable' );
        $message = apply_filters('frm_content', $message, $form, $record);
        $message = wpautop( do_shortcode($message) );
    }

    public static function get_draft_button( $form, $class = '', $html = '', $button_type = 'save_draft' ) {
        if ( empty( $html ) ) {
            $html = '[if save_draft]<input type="submit" value="[draft_label]" name="frm_save_draft" formnovalidate="formnovalidate" class="frm_save_draft '. $class .'" [draft_hook] />[/if save_draft]';
        }

        $html = FrmProFormsController::replace_shortcodes($html, $form);
        if ( strpos( $html, '[if '. $button_type .']') !== false ) {
            $html = preg_replace('/(\[if\s+'. $button_type .'\])(.*?)(\[\/if\s+'. $button_type .'\])/mis', '', $html);
        }
        return $html;
    }

	public static function get_draft_link( $form ) {
        $html = self::get_draft_button($form, '', FrmFormsHelper::get_draft_link());
        return $html;
    }

    public static function is_show_data_field($field) {
        return $field['type'] == 'data' && ( $field['data_type'] == '' || $field['data_type'] == 'data' );
    }

    public static function has_field($type, $form_id, $single = true) {
        global $wpdb;

        if ( $single ) {
            $included = FrmDb::get_var( 'frm_fields', array( 'form_id' => $form_id, 'type' => $type) );
            if ( $included ) {
                $included = FrmField::getOne($included);
            }
        } else {
            $included = FrmField::get_all_types_in_form( $form_id, $type );
        }

        return $included;
    }

    /**
     * @since 2.0
     * @return array of repeatable section fields
     */
    public static function has_repeat_field($form_id, $single = true) {
        $fields = self::has_field('divider', $form_id, $single);
        if ( ! $fields ) {
            return $fields;
        }

        $repeat_fields = array();
        foreach ( $fields as $field ) {
            if ( FrmProFieldsHelper::is_repeating_field($field) ) {
                $repeat_fields[] = $field;
            }
        }

        return $repeat_fields;
    }

    public static function &post_type($form) {
        if ( is_numeric($form) ) {
            $form_id = $form;
        } else {
            $form_id = (array) $form['id'];
        }

        $action = FrmFormActionsHelper::get_action_for_form($form_id, 'wppost');
        $action = reset( $action );

        if ( ! $action || ! isset($action->post_content['post_type']) ) {
            $type = 'post';
        } else {
            $type = $action->post_content['post_type'];
        }

        return $type;
    }

    public static function hex2rgb($hex) {
        _deprecated_function( __FUNCTION__, '2.0', 'FrmStylesHelper::hex2rgb' );
        return FrmStylesHelper::hex2rgb($hex);
    }

}
