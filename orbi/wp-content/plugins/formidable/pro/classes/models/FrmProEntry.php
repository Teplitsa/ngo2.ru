<?php
class FrmProEntry{

	public static function validate( $params, $fields, $form, $title, $description ) {
        global $frm_vars;

        $frm_settings = FrmAppHelper::get_settings();

        if ( (($_POST && isset($_POST['frm_page_order_'. $form->id])) || FrmProFormsHelper::going_to_prev($form->id)) && ! FrmProFormsHelper::saving_draft() ) {
            $errors = '';
            $fields = FrmFieldsHelper::get_form_fields($form->id);
            $submit = isset($form->options['submit_value']) ? $form->options['submit_value'] : $frm_settings->submit_value;
            $values = $fields ? FrmEntriesHelper::setup_new_vars($fields, $form) : array();
            require(FrmAppHelper::plugin_path() .'/classes/views/frm-entries/new.php');
            add_filter('frm_continue_to_create', '__return_false');
		} else if ( $form->editable && isset( $form->options['single_entry'] ) && $form->options['single_entry'] && $form->options['single_entry_type'] == 'user' ) {

            $user_ID = get_current_user_id();
			if ( $user_ID ) {
                $entry = FrmEntry::getAll( array( 'it.user_id' => $user_ID, 'it.form_id' => $form->id), '', 1, true);
				if ( $entry ) {
					$entry = reset( $entry );
				}
            }else{
                $entry = false;
            }

            if ( $entry && ! empty($entry) && ( ! isset($frm_vars['created_entries'][ $form->id ]) || ! isset($frm_vars['created_entries'][ $form->id ]['entry_id']) || $entry->id != $frm_vars['created_entries'][$form->id]['entry_id'] ) ) {
                FrmProEntriesController::show_responses($entry, $fields, $form, $title, $description);
            }else{
                $record = $frm_vars['created_entries'][$form->id]['entry_id'];
                $saved_message = isset($form->options['success_msg']) ? $form->options['success_msg'] : $frm_settings->success_msg;
                if ( FrmProFormsHelper::saving_draft() ) {
                    $saved_message = isset($form->options['draft_msg']) ? $form->options['draft_msg'] : __( 'Your draft has been saved.', 'formidable' );
                }
                $saved_message = apply_filters('frm_content', $saved_message, $form, ($record ? $record : false));
                $message = wpautop(do_shortcode($record ? $saved_message : $frm_settings->failed_msg));
                $message = '<div class="frm_message" id="message">'. $message .'</div>';

                FrmProEntriesController::show_responses($record, $fields, $form, $title, $description, $message);
            }
            add_filter('frm_continue_to_create', '__return_false');
		} else if ( FrmProFormsHelper::saving_draft() ) {
            $record = ( isset( $frm_vars['created_entries'] ) && isset( $frm_vars['created_entries'][ $form->id ] ) ) ? $frm_vars['created_entries'][ $form->id ]['entry_id'] : 0;

            if ( ! $record ) {
                return;
            }

            $saved_message = '';
            FrmProFormsHelper::save_draft_msg( $saved_message, $form, $record );

			$message = FrmFormsHelper::get_success_message( array(
				'message' => $saved_message, 'form' => $form,
				'entry_id' => $record, 'class' => 'frm_message',
			) );

            FrmProEntriesController::show_responses($record, $fields, $form, $title, $description, $message);
            add_filter('frm_continue_to_create', '__return_false');
        }
    }

    public static function save_sub_entries($values, $action = 'create') {
        $form_id = isset($values['form_id']) ? (int) $values['form_id']: 0;
        if ( ! $form_id || ! isset($values['item_meta']) ) {
            return $values;
        }

        $form_fields = FrmProFormsHelper::has_field('form', $form_id, false);
        $section_fields = FrmProFormsHelper::has_field('divider', $form_id, false);

        if ( ! $form_fields && ! $section_fields ) {
            // only continue if there could be sub entries
            return $values;
        }

        $form_fields = array_merge($section_fields, $form_fields);

        $new_values = $values;
        unset($new_values['item_meta']);

        // allow for multiple embeded forms
        foreach ( $form_fields as $field ) {
            if ( ! isset($values['item_meta'][ $field->id ]) || ! isset($field->field_options['form_select']) || ! isset($values['item_meta'][ $field->id ]['form']) ) {
                // don't continue if we don't know which form to insert the sub entries into
                unset($values['item_meta'][$field->id]);
                continue;
            }

            if ( 'divider' == $field->type && ! FrmField::is_repeating_field($field) ) {
                // only create sub entries for repeatable sections
                continue;
            }

            $field_values = $values['item_meta'][$field->id];

            $sub_form_id = $field->field_options['form_select'];
            unset($field_values['form']);

            if ( $action != 'create' && isset($field_values['id']) ) {
                $old_ids = FrmEntryMeta::get_entry_meta_by_field($values['id'], $field->id);
                $old_ids = array_filter( (array) $old_ids, 'is_numeric');
                unset($field_values['id']);
            } else {
                $old_ids = array();
            }

            $sub_ids = array();

            foreach ( $field_values as $k => $v ) {
                $entry_values = $new_values;
                $entry_values['form_id'] = $sub_form_id;
                $entry_values['item_meta'] = $v;
                $entry_values['parent_item_id'] = isset($values['id']) ? $values['id'] : 0;
				$entry_values['parent_form_id'] = $form_id;
				// include a nonce just to be sure the parent_form_id is legit
				$entry_values['parent_nonce'] = wp_create_nonce( 'parent' );

                // set values for later user (file upload and tags fields)
                $_POST['item_meta']['key_pointer'] = $k;
                $_POST['item_meta']['parent_field'] = $field->id;

                if ( ! is_numeric($k) && in_array( str_replace('i', '', $k), $old_ids ) ) {
                    // update existing sub entries
                    $entry_values['id'] = str_replace('i', '', $k);
                    FrmEntry::update($entry_values['id'], $entry_values);
                    $sub_id = $entry_values['id'];
                } else {
                    // create new sub entries
                    $sub_id = FrmEntry::create($entry_values);
                }

                if ( $sub_id ) {
                    $sub_ids[] = $sub_id;
                }

                unset($k, $v, $entry_values, $sub_id);
            }

            $values['item_meta'][$field->id] = $sub_ids; // array of sub entry ids

            $old_ids = array_diff($old_ids, $sub_ids);
            if ( ! empty($old_ids) ) {
                // delete entries that were removed from section
                foreach ( $old_ids as $old_id ) {
                    FrmEntry::destroy( $old_id );
                }
            }

            unset($field);
        }

        return $values;
    }

    /**
     * After an entry is duplicated, also duplicate the sub entries
     * @since 2.0
     */
    public static function duplicate_sub_entries( $entry_id, $form_id, $args ) {
        $form_fields = FrmProFormsHelper::has_field('form', $form_id, false);
        $section_fields = FrmProFormsHelper::has_repeat_field($form_id, false);
        $form_fields = array_merge($section_fields, $form_fields);
        if ( empty($form_fields) ) {
            // there are no fields for child entries
            return;
        }

        $entry = FrmEntry::getOne($entry_id, true);

        $sub_ids = array();
        foreach ( $form_fields as $field ) {
            if ( ! isset($entry->metas[$field->id]) ) {
                continue;
            }

            $field_ids = array();
            $ids = maybe_unserialize($entry->metas[$field->id]);
            if ( ! empty($ids) ) {
                // duplicate all entries for this field
                foreach ( (array) $ids as $sub_id ) {
                    $field_ids[] = FrmEntry::duplicate( $sub_id );
                    unset($sub_id);
                }

                FrmEntryMeta::update_entry_meta($entry_id, $field->id, null, $field_ids);
                $sub_ids = array_merge($field_ids, $sub_ids);
            }

            unset($field, $field_ids);
        }

        if ( ! empty($sub_ids) ) {
            // update the parent id for new entries
            global $wpdb;
			$where = array( 'id' => $sub_ids );
			FrmDb::get_where_clause_and_values( $where );
			array_unshift( $where['values'], $entry_id );

			$wpdb->query( $wpdb->prepare('UPDATE ' . $wpdb->prefix . 'frm_items SET parent_item_id = %d ' . $where['where'], $where['values'] ) );
        }
    }

    /**
     * After the sub entry and parent entry are created, we can update the parent id field
     * @since 2.0
     */
    public static function update_parent_id($entry_id, $form_id) {
        $form_fields = FrmProFormsHelper::has_field('form', $form_id, false);
        $section_fields = FrmProFormsHelper::has_repeat_field($form_id, false);

        if ( ! $form_fields && ! $section_fields ) {
            return;
        }

        $form_fields = array_merge($section_fields, $form_fields);
        $entry = FrmEntry::getOne($entry_id, true);

        if ( ! $entry || $entry->form_id != $form_id ) {
            return;
        }

        $sub_ids = array();
        foreach ( $form_fields as $field ) {
            if ( ! isset($entry->metas[$field->id]) ) {
                continue;
            }

            $ids = maybe_unserialize($entry->metas[$field->id]);
            if ( ! empty($ids) ) {
                $sub_ids = array_merge($ids, $sub_ids);
            }

            unset($field);
        }

        if ( ! empty($sub_ids) ) {
			$where = array( 'id' => $sub_ids );
			FrmDb::get_where_clause_and_values( $where );
			array_unshift( $where['values'], $entry_id );

            global $wpdb;
			$wpdb->query( $wpdb->prepare( 'UPDATE ' . $wpdb->prefix . 'frm_items SET parent_item_id = %d' . $where['where'], $where['values'] ) );
        }
    }

    public static function get_sub_entries($entry_id, $meta = false) {
		$entries = FrmEntry::getAll( array( 'parent_item_id' => $entry_id ), '', '', $meta, false );
        return $entries;
    }

	public static function save_post( $action, $entry, $form ) {
		_deprecated_function( __FUNCTION__, '2.0.9', 'FrmProPost::save_post' );
		return FrmProPost::save_post( $action, $entry, $form );
	}

    /**
     *
     * Modify values just before creating entry or saving form
     *
     * @since 2.0
     *
     * @param array $values - posted values
     * @param string $location If Other vals are not cleared by JavaScript when selection is changed, value should be cleared in this function. Other vals are not cleared with JavaScript on the back-end.
     * @return array $values
     */
	public static function mod_other_vals( $values = false, $location = 'front' ) {
        $set_post = false;
        if ( ! $values ) {
            $values = $_POST;
            $set_post = true;
        }

		// Modify posted confirmation values as well
		self::mod_conf_vals( $values, $location, $set_post );

        if ( ! isset( $values['item_meta']['other'] ) ) {
            return $values;
        }

        $other_array = (array) $values['item_meta']['other'];
        foreach ( $other_array as $f_id => $o_val ) {
            // For checkboxes and multi-select dropdowns
            if ( is_array( $o_val ) ) {
                if ( $location == 'back' ) {
                    // Check if "other" item was selected. If not, remove other text string from saved array
                    foreach ( $o_val as $opt_key => $saved_val ) {
                        if ( $saved_val && !empty( $values['item_meta'][$f_id][$opt_key] ) ) {
                            $values['item_meta'][$f_id][$opt_key] = $saved_val;
                        }
                        unset( $opt_key, $saved_val);
                    }
                } else if ( isset($values['item_meta'][$f_id]) ) {
                    $values['item_meta'][$f_id] = array_merge( (array)$values['item_meta'][$f_id], $o_val );
                }

            //For radio buttons and regular dropdowns
            } else if ( $o_val ) {
                if ( $location == 'back' && isset( $values['item_meta'][$f_id] ) && !empty( $values['item_meta'][$f_id] ) ) {
                    $field = FrmField::getOne( $f_id );

                    if ( $field ) {
                        // Get array key for Other option
                        $other_key = array_filter( array_keys( $field->options ), 'is_string' );
                        $other_key = reset( $other_key );

                        // Check if the Other option is selected. If so, set the value in text field.
                        if ( $values['item_meta'][$f_id] == $field->options[$other_key] ) {
                            $values['item_meta'][$f_id] = $o_val;
                        }
                    }
                } else {
                    $values['item_meta'][$f_id] = $o_val;
                }
            }
            unset( $f_id, $o_val );
        }
        unset( $values['item_meta']['other'] );

        // Modify post values directly, if needed
        if ( $set_post ) {
            $_POST['item_meta'] = $values['item_meta'];
        }

        return $values;
    }

    /**
     *
     * Modify posted values for Confirmation fields just before creating or updating entry
     *
     * @since 2.0
     *
     * @param array $values - posted values
     * @return array $values
     */
	public static function mod_conf_vals( &$values = false, $location, $set_post = false ) {
		// Check if we are saving or creating an entry
		if ( $location != 'front' || ! isset( $values['item_meta'] ) ) {
			return;
		}

		// Check for posted confirmation field values and delete them
		foreach ( $values['item_meta'] as $key => $val ) {
			if ( strpos( $key, 'conf_' ) !== false ) {
				unset( $values['item_meta'][$key] );
			}
		}

        // Modify post values directly, if needed
        if ( $set_post ) {
            $_POST['item_meta'] = $values['item_meta'];
        }
	}

	//If page size is set for views, only get the current page of entries
	public static function get_view_page( $current_p, $p_size, $where, $args ) {
		//Make sure values are ints for use in DB call
		$current_p = (int) $current_p;
		$p_size = (int) $p_size;

		//Calculate end_index and start_index
        $end_index = $current_p * $p_size;
        $start_index = $end_index - $p_size;

		//Set limit and pass it to get_view_results
		$args['limit'] = " LIMIT $start_index,$p_size";
		$results = self::get_view_results($where, $args);

        return $results;
    }

	//Get ordered and filtered entries for Views
	public static function get_view_results( $where, $args ) {
        global $wpdb;

		$defaults = array(
			'order_by_array' => array(), 'order_array' => array(),
			'limit' 	=> '', 'posts' => array(),
			'display'   => false,
		);

		$args = wp_parse_args($args, $defaults);
        $args['time_field'] = false;

        $query = array(
        	'select'    => 'SELECT it.id FROM '. $wpdb->prefix .'frm_items it',
            'where'     => $where,
            'order'     => 'ORDER BY it.created_at ASC',
        );

        //If order is set
		if ( ! empty($args['order_by_array']) ) {
			self::prepare_entries_query($query, $args);
		}
        $query = apply_filters('frm_view_order', $query, $args);

        if ( ! empty($query['where']) ) {
			$query['where'] = FrmAppHelper::prepend_and_or_where( 'WHERE ', $query['where'] );
        }

		$query['order'] = rtrim($query['order'], ', ');

		$query = implode($query, ' ') . $args['limit'];
		$entry_ids = $wpdb->get_col( $query );

		self::reorder_time_entries( $entry_ids, $args['time_field'] );

		return $entry_ids;
	}

    private static function prepare_entries_query( &$query, &$args ) {
        if ( in_array( 'rand', $args['order_by_array']) ) {
            //If random is set, set the order to random
            $query['order'] = ' ORDER BY RAND()';
            return;
        }

		//Remove other ordering fields if created_at or updated_at is selected for first ordering field
		if ( reset($args['order_by_array']) == 'created_at' || reset($args['order_by_array']) == 'updated_at' ) {
			foreach ( $args['order_by_array'] as $o_key => $order_by_field ) {
				if ( is_numeric($order_by_field) ) {
					unset($args['order_by_array'][$o_key]);
					unset($args['order_array'][$o_key]);
				}
			}
            $numeric_order_array = array();
		} else {
		//Get number of fields in $args['order_by_array'] - this will not include created_at, updated_at, or random
		    $numeric_order_array = array_filter($args['order_by_array'], 'is_numeric');
		}

        if ( ! count($numeric_order_array) ) {
            //If ordering by creation date and/or update date without any fields
			$query['order'] = ' ORDER BY';

			foreach ( $args['order_by_array'] as $o_key => $order_by ) {
				$query['order'] .= ' it.' . sanitize_title( $order_by ) . ' ' . $args['order_array'][ $o_key ] . ', ';
				unset($order_by);
			}
            return;
        }

	    //If ordering by at least one field (not just created_at, updated_at, or entry ID)
		$order_fields = array();
		foreach ( $args['order_by_array'] as $o_key => $order_by_field ) {
			if ( is_numeric($order_by_field) ) {
				$order_fields[$o_key] = FrmField::getOne($order_by_field);
			} else {
				$order_fields[$o_key] = $order_by_field;
			}
		}

		//Get all post IDs for this form
        $linked_posts = array();
       	foreach ( $args['posts'] as $post_meta ) {
        	$linked_posts[$post_meta->post_id] = $post_meta->id;
        }

        $first_order = true;
        $query['order'] = 'ORDER BY ';
		foreach ( $order_fields as $o_key => $o_field ) {
            self::prepare_ordered_entries_query( $query, $args, $o_key, $o_field, $first_order );
            $first_order = false;
			unset($o_field);
		}
    }

    private static function prepare_ordered_entries_query( &$query, &$args, $o_key, $o_field, $first_order ) {
        global $wpdb;

        //if field is some type of post field
		if ( isset($o_field->field_options['post_field']) && $o_field->field_options['post_field'] ) {

            //if field is custom field
			if ( $o_field->field_options['post_field'] == 'post_custom' ) {
                $query['select'] .= $wpdb->prepare(' LEFT JOIN '. $wpdb->postmeta .' pm'. $o_key .' ON pm'. $o_key .'.post_id=it.post_id AND pm'. $o_key .'.meta_key = %s ', $o_field->field_options['custom_field']);
                $query['order'] .= 'CASE when pm'. $o_key .'.meta_value IS NULL THEN 1 ELSE 0 END, pm'. $o_key .'.meta_value '. $args['order_array'][$o_key] .', ';
            } else if ( $o_field->field_options['post_field'] != 'post_category' ) {
                //if field is a non-category post field
                $query['select'] .= $first_order ? ' INNER ' : ' LEFT ';
				$query['select'] .= 'JOIN '. $wpdb->posts .' p'. $o_key .' ON p'. $o_key .'.ID=it.post_id ';

                $query['order'] .= 'CASE p'. $o_key .'.'. $o_field->field_options['post_field']." WHEN '' THEN 1 ELSE 0 END, p$o_key.". $o_field->field_options['post_field'].' '. $args['order_array'][$o_key] .', ';
            }
        } else if ( is_numeric($args['order_by_array'][$o_key]) ) {
            //if ordering by a normal, non-post field
            $query['select'] .= $wpdb->prepare(' LEFT JOIN '. $wpdb->prefix .'frm_item_metas em'. $o_key .' ON em'. $o_key .'.item_id=it.id AND em'. $o_key .'.field_id=%d ', $o_field->id);
            $query['order'] .= 'CASE when em'. $o_key .'.meta_value IS NULL THEN 1 ELSE 0 END, em'. $o_key .'.meta_value '. ( in_array($o_field->type, array( 'number', 'scale')) ? '+0 ' : '') . $args['order_array'][$o_key] .', ';

			//Meta value is only necessary for time field reordering and only if time field is first ordering field
			//Check if time field (for time field ordering)
			if ( $first_order && $o_field->type == 'time' ) {
                $args['time_field'] = $o_field;
            }
        } else {
            $query['order'] .= 'it.'. $o_field .' '. $args['order_array'][$o_key] .', ';
        }
    }

    /**
     * Reorder entries if 12 hour time field is selected for first ordering field.
     * If the $time_field variable is set, this means the first ordering field is a time field.
     */
	private static function reorder_time_entries( &$entry_ids, $time_field ) {
		if ( ! $time_field || ! is_array( $entry_ids ) || empty( $entry_ids ) ) {
            return;
        }

        if ( isset($time_field->field_options['clock']) && $time_field->field_options['clock'] != 12 ) {
            // only reorder with 12 hour times
            return;
        }

		global $wpdb;

		//Reorder entries
    	$new_order = array();
		$empty_times = array();
		$times = $wpdb->get_results( $wpdb->prepare( 'SELECT item_id, meta_value FROM ' . $wpdb->prefix . 'frm_item_metas WHERE field_id=%d', $time_field->id ), OBJECT_K );

		foreach ( $entry_ids as $e_key ) {
			if ( ! isset( $times[ $e_key ] ) ) {
				$empty_times[ $e_key ] = '';
				continue;
			}

			$time = $times[ $e_key ]->meta_value;
			$new_order[ $e_key ] = FrmProAppHelper::format_time( $time, 'Hi' );

        	unset($e_key, $entry);
		}

    	//array with sorted times
    	asort($new_order);

		$new_order = $new_order + $empty_times;
    	$final_order = array_keys( $new_order );

        $entry_ids = $final_order;
    }

	public static function pre_validate( $errors, $values ) {
		_deprecated_function( __FUNCTION__, '2.0.8', 'FrmProFormsHelper::can_submit_form_now' );
		return FrmProFormsHelper::can_submit_form_now( $errors, $values );
    }

	public static function get_field( $field = 'is_draft', $id ) {
        _deprecated_function( __FUNCTION__, '2.0', 'FrmProEntriesHelper::get_field');
        return FrmProEntriesHelper::get_field($field, $id);
    }

	public static function setup_post($action, $entry, $form) {
		_deprecated_function( __FUNCTION__, '2.0.9', array( 'FrmProPost::save_post') );
		return FrmProPost::setup_post( $action, $entry, $form );
	}

	public static function create_post($entry, $form, $action = false) {
		_deprecated_function( __FUNCTION__, '2.0.9', array( 'FrmProPost::create_post') );
		return FrmProPost::create_post($entry, $form, $action );
	}

	public static function insert_post( $entry, $new_post, $post, $form = false, $action = false ) {
		_deprecated_function( __FUNCTION__, '2.0.9', array( 'FrmProPost::insert_post') );
		return FrmProPost::insert_post( $entry, $new_post, $post, $form, $action );
	}

    public static function update_post() {
        _deprecated_function( __FUNCTION__, '2.0', array( 'FrmProPost::save_post') );
    }
}
