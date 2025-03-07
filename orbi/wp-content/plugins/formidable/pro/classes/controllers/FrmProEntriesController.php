<?php

class FrmProEntriesController{

    public static function admin_js() {
        $frm_settings = FrmAppHelper::get_settings();

        add_filter('manage_'. sanitize_title($frm_settings->menu) .'_page_formidable-entries_columns', 'FrmProEntriesController::manage_columns', 25);

		$page = FrmAppHelper::simple_get( 'page', 'sanitize_title' );
		if ( $page != 'formidable-entries' ) {
            return;
        }

        wp_enqueue_script('jquery-ui-datepicker');

        if ( $frm_settings->accordion_js ) {
            wp_enqueue_script('jquery-ui-widget');
            wp_enqueue_script('jquery-ui-accordion');
        }

        $theme_css = FrmStylesController::get_style_val('theme_css');
        if ( $theme_css == -1 ) {
            return;
        }

        wp_enqueue_style($theme_css, FrmStylesHelper::jquery_css_url($theme_css));
    }

	public static function remove_fullscreen( $init ) {
		if ( isset( $init['plugins'] ) ) {
            $init['plugins'] = str_replace('wpfullscreen,', '', $init['plugins']);
            $init['plugins'] = str_replace('fullscreen,', '', $init['plugins']);
        }
        return $init;
    }

	public static function register_scripts() {
		_deprecated_function( __FUNCTION__, '2.0.9', 'FrmFormsController::register_pro_scripts' );
		FrmFormsController::register_pro_scripts();
	}

    public static function add_js(){
        if ( FrmAppHelper::is_admin() ) {
            return;
        }

        $frm_settings = FrmAppHelper::get_settings();

        global $frm_vars;
        if ( $frm_settings->jquery_css ) {
            $frm_vars['datepicker_loaded'][] = true;
        }

		if ( $frm_settings->accordion_js ) {
            wp_enqueue_script('jquery-ui-widget');
            wp_enqueue_script('jquery-ui-accordion');
        }
    }

	public static function print_ajax_scripts( $keep = '' ) {
		self::enqueue_footer_js();

		if ( $keep != 'all' ) {
			if ( $keep == 'none' ) {
				$keep_scripts = $keep_styles = array();
			} else {
				$keep_scripts = array(
					'recaptcha-api', 'jquery-frm-rating', 'jquery-chosen',
					'google_jsapi',
					'flashcanvas', 'jquery-signaturepad', 'frm-signature', // Remove these after add-on update
				);
				$keep_styles = array( 'dashicons', 'jquery-theme' );

				if ( is_array( $keep ) ) {
					$keep_scripts = array_merge( $keep_scripts, $keep );
				}
			}

			global $wp_scripts, $wp_styles;
			$keep_scripts = apply_filters( 'frm_ajax_load_scripts', $keep_scripts );
			$registered_scripts = (array) $wp_scripts->registered;
			$registered_scripts = array_diff( array_keys( $registered_scripts ), $keep_scripts );
			$wp_scripts->done = array_merge( $wp_scripts->done, $registered_scripts );

			$keep_styles = apply_filters( 'frm_ajax_load_styles', $keep_styles );
			$registered_styles = (array) $wp_styles->registered;
			$registered_styles = array_diff( array_keys( $registered_styles ), $keep_styles );
			if ( ! empty( $registered_styles ) ) {
				$wp_styles->done = array_merge( $wp_styles->done, $registered_styles );
			}
		}

		wp_print_footer_scripts();

		self::footer_js();
	}

    /**
     * Check if the form is loaded after the wp_footer hook.
     * If it is, we'll need to make sure the scripts are loaded.
     */
    public static function after_footer_loaded() {
        global $frm_vars;

		if ( ! isset( $frm_vars['footer_loaded'] ) || ! $frm_vars['footer_loaded'] ) {
            return;
        }

        self::enqueue_footer_js();

    	print_late_styles();
    	print_footer_scripts();

        self::footer_js();
    }

    public static function enqueue_footer_js(){
        global $frm_vars, $frm_input_masks;

        if ( empty($frm_vars['forms_loaded']) ) {
            return;
        }

		FrmFormsController::register_pro_scripts();

        if ( ! FrmAppHelper::doing_ajax() ) {
            wp_enqueue_script('formidable' );
        }

        if ( isset($frm_vars['tinymce_loaded']) && $frm_vars['tinymce_loaded'] ) {
            _WP_Editors::enqueue_scripts();
        }

		// trigger jQuery UI to be loaded on every page
		self::add_js();

        if ( isset($frm_vars['datepicker_loaded']) && ! empty($frm_vars['datepicker_loaded']) ) {
            if ( is_array($frm_vars['datepicker_loaded']) ) {
                foreach ( $frm_vars['datepicker_loaded'] as $fid => $o ) {
                    if ( ! $o ) {
                        unset($frm_vars['datepicker_loaded'][$fid]);
                    }
                    unset($fid, $o);
                }
            }

            if ( ! empty($frm_vars['datepicker_loaded']) ) {
                wp_enqueue_script('jquery-ui-datepicker');
                FrmStylesHelper::enqueue_jquery_css();
            }
        }

        if ( isset($frm_vars['chosen_loaded']) && $frm_vars['chosen_loaded'] ) {
            wp_enqueue_script('jquery-chosen');
        }

        if ( isset($frm_vars['star_loaded']) && ! empty($frm_vars['star_loaded']) ) {
            wp_enqueue_script('jquery-frm-rating');
            wp_enqueue_style( 'dashicons' );

            FrmStylesController::enqueue_style();
        }

        $frm_input_masks = apply_filters('frm_input_masks', $frm_input_masks, $frm_vars['forms_loaded']);
        foreach ( (array) $frm_input_masks as $fid => $o ) {
            if ( ! $o ) {
                unset($frm_input_masks[$fid]);
            }
            unset($fid, $o);
        }

        if ( ! empty($frm_input_masks) ) {
            wp_enqueue_script('jquery-maskedinput');
        }

        if ( isset($frm_vars['google_graphs']) && ! empty($frm_vars['google_graphs']) ) {
            wp_enqueue_script('google_jsapi', 'https://www.google.com/jsapi');
        }
    }

    public static function footer_js(){
        global $frm_vars, $frm_input_masks;

        $frm_vars['footer_loaded'] = true;

        if ( empty($frm_vars['forms_loaded']) ) {
            return;
        }

        $trigger_form = ( ! FrmAppHelper::doing_ajax() && ! FrmAppHelper::is_admin_page('formidable-entries') );

        include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-entries/footer_js.php');

		/**
		 * Add custom scripts after the form scripts are done loading
		 * @since 2.0.6
		 */
		do_action( 'frm_footer_scripts', $frm_vars['forms_loaded'] );
    }

	public static function data_sort( $options ) {
        natcasesort($options); //TODO: add sorting options
        return $options;
    }

    public static function register_widgets() {
        include_once(FrmAppHelper::plugin_path() .'/pro/classes/widgets/FrmListEntries.php');
        register_widget('FrmListEntries');
    }

    /* Back End CRUD */
	public static function show_comments( $entry ) {
        $id = $entry->id;
        $user_ID = get_current_user_id();

        if ( $_POST && isset($_POST['frm_comment']) && ! empty($_POST['frm_comment']) ) {
            FrmEntryMeta::add_entry_meta($_POST['item_id'], 0, '', array(
                'comment' => $_POST['frm_comment'], 'user_id' => $user_ID,
            ));
            //send email notifications
        }

		$comments = FrmEntryMeta::getAll( array( 'item_id' => $id, 'field_id' => 0), ' ORDER BY it.created_at ASC', '', true);
		$date_format = get_option( 'date_format' );
		$time_format = get_option( 'time_format' );

        include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-entries/show.php');
    }

	public static function add_duplicate_link( $entry ) {
        FrmProEntriesHelper::show_duplicate_link($entry);
    }

	public static function add_sidebar_links( $entry ) {
        include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-entries/_sidebar-shared-pub.php');
    }

    public static function add_edit_link() {
        FrmProEntriesHelper::edit_button();
    }

	public static function add_new_entry_link( $form ) {
        FrmProEntriesHelper::show_new_entry_button($form);
    }

    public static function new_entry(){
		if ( $form_id = FrmAppHelper::get_param( 'form', '', 'get', 'absint' ) ) {
            $form = FrmForm::getOne($form_id);
            self::get_new_vars('', $form);
        } else {
             include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-entries/new-selection.php');
        }
    }

    public static function create(){
        if ( ! current_user_can('frm_create_entries') ) {
            return FrmEntriesController::display_list();
        }

		$params = FrmForm::get_admin_params();
        $form = $record = false;
        if ( $params['form'] ) {
            $form = FrmForm::getOne($params['form']);
        }

        if ( ! $form ) {
            return;
        }

        $errors = FrmEntryValidate::validate( $_POST );

        if ( count($errors) > 0 ) {
            self::get_new_vars($errors, $form);
            return;
        }

        if ( ( isset($_POST['frm_page_order_'. $form->id]) || FrmProFormsHelper::going_to_prev($form->id) ) && ! FrmProFormsHelper::saving_draft() ) {
            self::get_new_vars('', $form);
            return;
        }

		$_SERVER['REQUEST_URI'] = str_replace( '&frm_action=new', '', FrmAppHelper::get_server_value( 'REQUEST_URI' ) );

        global $frm_vars;
        if ( ! isset($frm_vars['created_entries'][ $form->id ]) || ! $frm_vars['created_entries'][ $form->id ] ) {
            $frm_vars['created_entries'][$form->id] = array();
        }

        if ( ! isset($frm_vars['created_entries'][ $_POST['form_id'] ]['entry_id']) ) {
            $record = $frm_vars['created_entries'][$form->id]['entry_id'] = FrmEntry::create( $_POST );
        }

        if ( $record ) {
            if ( FrmProFormsHelper::saving_draft() ) {
                $message = __( 'Draft was Successfully Created', 'formidable' );
            } else {
                $message = __( 'Entry was Successfully Created', 'formidable' );
            }

            self::get_edit_vars($record, $errors, $message);
        } else {
            self::get_new_vars($errors, $form);
        }
    }

    public static function edit(){
		$id = FrmAppHelper::get_param( 'id', '', 'get', 'absint' );

        if ( ! current_user_can('frm_edit_entries') ) {
            return FrmEntriesController::show($id);
        }

        return self::get_edit_vars($id);
    }

    public static function update(){
		$id = FrmAppHelper::get_param( 'id', '', 'get', 'absint' );

        if ( ! current_user_can('frm_edit_entries') ) {
            return FrmEntriesController::show($id);
        }

        $message = '';
        $errors = FrmEntryValidate::validate( $_POST );

		if ( empty( $errors ) ) {
            if ( isset($_POST['form_id']) && ( isset($_POST['frm_page_order_'. $_POST['form_id']]) || FrmProFormsHelper::going_to_prev($_POST['form_id']) ) && ! FrmProFormsHelper::saving_draft() ) {
                return self::get_edit_vars($id);
            }else{
                FrmEntry::update( $id, $_POST );
                if ( isset($_POST['form_id']) && FrmProFormsHelper::saving_draft() ) {
                    $message = __( 'Draft was Successfully Updated', 'formidable' );
                } else {
                    $message = __( 'Entry was Successfully Updated', 'formidable' );
                }

                $message .= '<br/> <a href="?page=formidable-entries&form='. $_POST['form_id'] .'">&larr; '. __( 'Back to Entries', 'formidable' ) .'</a>';
            }
        }

        return self::get_edit_vars($id, $errors, $message);
    }

    public static function duplicate(){
		$params = FrmForm::get_admin_params();

        if ( ! current_user_can('frm_create_entries') ) {
            return FrmEntriesController::show($params['id']);
        }

        $message = $errors = '';

        $record = FrmEntry::duplicate( $params['id'] );
        if ( $record ) {
            $message = __( 'Entry was Successfully Duplicated', 'formidable' );
        } else {
            $errors = __( 'There was a problem duplicating that entry', 'formidable' );
        }

        if ( ! empty( $errors ) ) {
			return FrmEntriesController::display_list( $message, $errors );
        } else {
			return self::get_edit_vars( $record, array(), $message );
        }
    }

	public static function bulk_actions( $action = 'list-form' ) {
		$params = FrmForm::get_admin_params();
        $errors = array();
        $bulkaction = '-1';

		if ( $action == 'list-form' ) {
            if ( $_REQUEST['bulkaction'] != '-1' ) {
                $bulkaction = sanitize_text_field( $_REQUEST['bulkaction'] );
            } else if ( $_POST['bulkaction2'] != '-1' ) {
                $bulkaction = sanitize_text_field( $_REQUEST['bulkaction2'] );
			}
		} else {
            $bulkaction = str_replace('bulk_', '', $action);
        }

        $items = FrmAppHelper::get_param('item-action', '');
        if (empty($items)){
            $errors[] = __( 'No entries were specified', 'formidable' );
        }else{
            $frm_settings = FrmAppHelper::get_settings();

            if ( ! is_array($items) ) {
                $items = explode(',', $items);
            }

			if ( $bulkaction == 'delete' ) {
				if ( ! current_user_can( 'frm_delete_entries' ) ) {
                    $errors[] = $frm_settings->admin_permission;
				} else {
                    if ( is_array($items) ) {
                        foreach ( $items as $item_id ) {
                            FrmEntry::destroy($item_id);
                        }
                    }
                }
			} else if ( $bulkaction == 'csv' ) {
                FrmAppHelper::permission_check('frm_view_entries');

                $form_id = $params['form'];
                if ( ! $form_id ) {
					$form = FrmForm::get_published_forms( array(), 1 );
                    if ( ! empty($form) ) {
                        $form_id = $form->id;
                    } else {
                        $errors[] = __( 'No form was found', 'formidable' );
                    }
                }

                if ( $form_id && is_array($items) ) {
					echo '<script type="text/javascript">window.onload=function(){location.href="' . esc_url_raw( admin_url( 'admin-ajax.php' ) . '?form=' . $form_id . '&action=frm_entries_csv&item_id=' . implode( ',', $items ) ) . '";}</script>';
                }
            }
        }
		FrmEntriesController::display_list( '', $errors );
    }

    /* Front End CRUD */

    //Determine if this is a new entry or if we're editing an old one
	public static function maybe_editing( $continue, $form_id, $action = 'new' ) {
		$form_submitted = FrmAppHelper::get_param( 'form_id', '', 'get', 'absint' );
        if ( $action == 'new' || $action == 'preview' ) {
            $continue = true;
        } else {
			$continue = ( $form_submitted && (int) $form_id != $form_submitted );
        }

        return $continue;
    }

	public static function check_draft_status( $values, $id ) {
        if ( FrmProEntriesHelper::get_field('is_draft', $id) || $values['is_draft'] ) {
            //remove update hooks if submitting for the first time or is still draft

        }

        //if entry was not previously draft or continues to be draft
        if ( !FrmProEntriesHelper::get_field('is_draft', $id) || $values['is_draft'] ) {
            return $values;
        }

        //add the create hooks since the entry is switching draft status
        add_action('frm_after_update_entry', 'FrmProEntriesController::add_published_hooks', 2, 2);

        //change created timestamp
        $values['created_at'] = $values['updated_at'];

        return $values;
    }

	public static function remove_draft_hooks( $entry_id ) {
        if ( ! FrmProEntriesHelper::get_field('is_draft', $entry_id) ) {
            return;
        }

        // don't let sub entries remove these hooks
        $entry = FrmEntry::getOne($entry_id);
        if ( $entry->parent_item_id ) {
            return;
        }

        //remove hooks if saving as draft
        remove_action('frm_after_create_entry', 'FrmProEntriesController::set_cookie', 20);
        remove_action('frm_after_create_entry', 'FrmFormActionsController::trigger_create_actions', 20);
    }

    //add the create hooks since the entry is switching draft status
	public static function add_published_hooks( $entry_id, $form_id ) {
        do_action('frm_after_create_entry', $entry_id, $form_id);
        do_action('frm_after_create_entry_'. $form_id, $entry_id);
        remove_action('frm_after_update_entry', 'FrmProEntriesController::add_published_hooks', 2);
    }

	public static function process_update_entry( $params, $errors, $form, $args ) {
        global $frm_vars;

        if ( $params['action'] == 'update' && isset($frm_vars['saved_entries']) && in_array( (int) $params['id'], (array) $frm_vars['saved_entries'] ) ) {
            return;
        }

        if ( $params['action'] == 'create' && isset($frm_vars['created_entries'][$form->id]) && isset($frm_vars['created_entries'][$form->id]['entry_id']) && is_numeric($frm_vars['created_entries'][$form->id]['entry_id']) ) {
            $entry_id = $params['id'] = $frm_vars['created_entries'][$form->id]['entry_id'];

            self::set_cookie($entry_id, $form->id);

            $conf_method = apply_filters('frm_success_filter', 'message', $form, $form->options, $params['action']);
            if ($conf_method != 'redirect')
                return;

            $success_args = array( 'action' => $params['action']);

			if ( isset( $args['ajax'] ) ) {
                $success_args['ajax'] = $args['ajax'];
			}
            do_action('frm_success_action', $conf_method, $form, $form->options, $params['id'], $success_args);
        }else if ($params['action'] == 'update'){
            if ( isset($frm_vars['saved_entries']) && in_array((int) $params['id'], (array) $frm_vars['saved_entries']) ) {
                if ( isset($_POST['item_meta']) ) {
                    unset($_POST['item_meta']);
                }

                add_filter('frm_continue_to_new', '__return_false', 15);
                return;
            }

            //don't update if there are validation errors
            if ( ! empty( $errors ) ) {
                return;
            }

            //check if user is allowed to update
            if ( ! FrmProEntriesHelper::user_can_edit( (int) $params['id'], $form ) ) {
                $frm_settings = FrmAppHelper::get_settings();
                wp_die(do_shortcode($frm_settings->login_msg));
            }

            //update, but don't check for confirmation if saving draft
            if ( FrmProFormsHelper::saving_draft() ) {
                FrmEntry::update( $params['id'], $_POST );
                return;
            }

            //don't update if going back
            if ( isset($_POST['frm_page_order_'. $form->id]) || FrmProFormsHelper::going_to_prev($form->id) ) {
                return;
            }

            FrmEntry::update( $params['id'], $_POST );


            $success_args = array( 'action' => $params['action']);
            if ( $params['action'] != 'create' && FrmProEntriesHelper::is_new_entry($params['id']) ) {
                $success_args['action'] = 'create';
            }

            //check confirmation method
            $conf_method = apply_filters('frm_success_filter', 'message', $form, $success_args['action']);

			if ( $conf_method != 'redirect' ) {
                return;
			}

			if ( isset( $args['ajax'] ) ) {
                $success_args['ajax'] = $args['ajax'];
			}

            do_action('frm_success_action', $conf_method, $form, $form->options, $params['id'], $success_args);

		} else if ( $params['action'] == 'destroy' ) {
            //if the user who created the entry is deleting it
            self::ajax_destroy($form->id, false, false);
        }
    }

	public static function edit_update_form( $params, $fields, $form, $title, $description ) {
        global $frm_vars;

        $continue = true;

        if ( 'edit' == $params['action'] ) {
            self::front_edit_entry($form, $fields, $title, $description, $continue);
        } else if ( 'update' == $params['action'] && $params['posted_form_id'] == $form->id ) {
            self::front_update_entry($form, $fields, $title, $description, $continue, $params);
        } else if ( 'destroy' == $params['action'] ) {
            self::front_destroy_entry($form);
        } else if ( isset($frm_vars['editing_entry']) && $frm_vars['editing_entry'] ) {
            self::front_auto_edit_entry($form, $fields, $title, $description, $continue);
        } else {
            self::allow_front_create_entry($form, $continue);
        }

        remove_filter('frm_continue_to_new', '__return_'. ( $continue ? 'false' : 'true' ), 15); // remove the opposite filter
        add_filter('frm_continue_to_new', '__return_'. ($continue ? 'true' : 'false'), 15);
    }

    /**
     * Load form for editing
     */
    private static function front_edit_entry( $form, $fields, $title, $description, &$continue ) {
        global $wpdb;

		$entry_key = FrmAppHelper::get_param( 'entry', '', 'get', 'sanitize_title' );

        $query = array( 'it.form_id' => $form->id );

        if ( $entry_key ) {
            $query[1] = array( 'or' => 1, 'it.id' => $entry_key, 'it.item_key' => $entry_key );
            $in_form = FrmDb::get_var( $wpdb->prefix .'frm_items it', $query );

            if ( ! $in_form ) {
                $entry_key = false;
                unset( $query[1] );
            }
            unset($in_form);
        }

        $entry = FrmProEntriesHelper::user_can_edit( $entry_key, $form );
        if ( ! $entry ) {
            return;
        }

		if ( ! is_array($entry) ){
			$entry = FrmEntry::getAll( $query, '', 1, true );
		}

		if ( ! empty( $entry ) ) {
			global $frm_vars;
			$entry = reset($entry);
			$frm_vars['editing_entry'] = $entry->id;
			self::show_responses($entry, $fields, $form, $title, $description);
			$continue = false;
        }
    }

    /**
     * Automatically load the form for editing when a draft exists
     * or the form is limited to one per user
     */
    private static function front_auto_edit_entry( $form, $fields, $title, $description, &$continue ) {
        global $frm_vars, $wpdb;

        $user_ID = get_current_user_id();

        if ( is_numeric($frm_vars['editing_entry']) ) {
			//get entry from shortcode
			$entry_id = $frm_vars['editing_entry'];
        } else {
			// get all entry ids for this user
			$entry_ids = FrmDb::get_col( 'frm_items', array( 'user_id' => $user_ID, 'form_id' => $form->id) );

            if ( empty($entry_ids) ) {
                return;
            }

			//$where_options = $frm_vars['editing_entry']; // Is is possible the entry_id parameter in the shortcode is sql?
			$get_meta = FrmEntryMeta::getAll( array( 'it.item_id' => $entry_ids ), ' ORDER BY it.created_at DESC', ' LIMIT 1');
            $entry_id = $get_meta ? $get_meta->item_id : false;
        }

        if ( ! $entry_id ) {
            return;
        }

		if ( ! FrmProEntriesHelper::user_can_edit( $entry_id, $form ) ) {
			return;
		}

        $frm_vars['editing_entry'] = $entry_id;
        self::show_responses($entry_id, $fields, $form, $title, $description);
        $continue = false;
    }

    private static function front_destroy_entry( $form ) {
        //if the user who created the entry is deleting it
        self::ajax_destroy($form->id, false);
    }

	private static function front_update_entry( $form, $fields, $title, $description, &$continue, $params ) {
        global $frm_vars;

        $message = '';
        $errors = isset($frm_vars['created_entries'][$form->id]) ? $frm_vars['created_entries'][$form->id]['errors'] : false;

        if ( empty($errors) ) {
            $saving_draft = FrmProFormsHelper::saving_draft();
            if ( ( ! isset($_POST['frm_page_order_'. $form->id]) && ! FrmProFormsHelper::going_to_prev($form->id) ) || $saving_draft ) {
                $success_args = array( 'action' => $params['action']);
                if ( FrmProEntriesHelper::is_new_entry($params['id']) ) {
                    $success_args['action'] = 'create';
                }

                //check confirmation method
                $conf_method = apply_filters('frm_success_filter', 'message', $form, $success_args['action']);

                if ( $conf_method == 'message' ) {
                    $message = self::confirmation($conf_method, $form, $form->options, $params['id'], $success_args);
                } else {
                    do_action('frm_success_action', $conf_method, $form, $form->options, $params['id'], $success_args);
                    add_filter('frm_continue_to_new', '__return_false', 16);
                    return;
                }
            }
		} else {
			$fields = FrmFieldsHelper::get_form_fields( $form->id, true );
		}

        self::show_responses($params['id'], $fields, $form, $title, $description, $message, $errors);
        $continue = false;
    }

    /**
     * check to see if user is allowed to create another entry
     */
	private static function allow_front_create_entry( $form, &$continue ) {
		if ( ! isset( $form->options['single_entry'] ) || ! $form->options['single_entry'] ) {
			return;
		}

		if ( ! FrmProFormsHelper::user_can_submit_form( $form ) ) {
			$frmpro_settings = new FrmProSettings();
			echo $frmpro_settings->already_submitted;
			$continue = false;
		}
	}

    public static function show_responses( $id, $fields, $form, $title = false, $description = false, $message = '', $errors = array() ) {
        global $frm_vars;

		if ( is_object( $id ) ) {
            $item = $id;
            $id = $item->id;
		} else {
            $item = FrmEntry::getOne($id, true);
        }

        $frm_vars['editing_entry'] = $item->id;
        $values = FrmAppHelper::setup_edit_vars($item, 'entries', $fields);

        if ( $values['custom_style'] ) {
            $frm_vars['load_css'] = true;
        }
        $show_form = true;

        if ( $item->is_draft ) {
            if ( isset($values['submit_value']) ) {
                $edit_create = $values['submit_value'];
            } else {
                $frmpro_settings = new FrmProSettings();
                $edit_create = $frmpro_settings->submit_value;
            }
        } else {
            if ( isset($values['edit_value']) ) {
                $edit_create = $values['edit_value'];
            } else {
                $frmpro_settings = new FrmProSettings();
                $edit_create = $frmpro_settings->update_value;
            }
        }

        $submit = (isset($frm_vars['next_page'][$form->id])) ? $frm_vars['next_page'][$form->id] : $edit_create;
        unset($edit_create);

		if ( is_object( $submit ) ) {
            $submit = $submit->name;
		}

		if ( ! isset( $frm_vars['prev_page'][ $form->id ] ) && isset( $_POST['item_meta'] ) && empty( $errors ) && $form->id == FrmAppHelper::get_param( 'form_id', '', 'get', 'absint' ) ) {
            $show_form = (isset($form->options['show_form'])) ? $form->options['show_form'] : true;
            if ( FrmProFormsHelper::saving_draft() || FrmProFormsHelper::going_to_prev($form->id) ) {
                $show_form = true;
            }else{
                $show_form = apply_filters('frm_show_form_after_edit', $show_form, $form);
                $success_args = array( 'action' => 'update');
                if ( FrmProEntriesHelper::is_new_entry($id) ) {
                    $success_args['action'] = 'create';
                }

                $conf_method = apply_filters('frm_success_filter', 'message', $form, $success_args['action']);

                if ( $conf_method != 'message' ) {
                    do_action('frm_success_action', $conf_method, $form, $form->options, $id, $success_args);
					// End now so the form isn't shown when "Show Page Content" is selected
					return;
                }
            }
        } else if ( isset($frm_vars['prev_page'][$form->id]) || ! empty($errors) ) {
            $jump_to_form = true;
        }

        $user_ID = get_current_user_id();

        if ( isset($form->options['show_form']) && $form->options['show_form'] ) {
            //Do nothing because JavaScript is already loaded
        } else {
            //Load JavaScript here
            $frm_vars['forms_loaded'][] = true;
        }

        $frm_settings = FrmAppHelper::get_settings();
        require(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-entries/edit-front.php');
        add_filter('frm_continue_to_new', 'FrmProEntriesController::maybe_editing', 10, 3);
    }

    public static function ajax_submit_button() {
        global $frm_vars;

        if ( isset($frm_vars['novalidate']) && $frm_vars['novalidate'] ) {
            echo ' formnovalidate="formnovalidate"';
        }
    }

	public static function get_confirmation_method( $method, $form, $action = 'create' ) {
        $opt = ( $action == 'update' ) ? 'edit_action' : 'success_action';
        $method = ( isset( $form->options[ $opt ] ) && ! empty( $form->options[ $opt ] ) ) ? $form->options[ $opt ] : $method;
        if ( $method != 'message' && FrmProFormsHelper::saving_draft() ) {
            $method = 'message';
        }
        return $method;
    }

    public static function confirmation( $method, $form, $form_options, $entry_id, $args = array() ) {
        $opt = ( ! isset($args['action']) || $args['action'] == 'create' ) ? 'success' : 'edit';
        if ( $method == 'page' && is_numeric($form_options[$opt .'_page_id']) ) {
            global $post;
            if ( ! $post || $form_options[$opt .'_page_id'] != $post->ID ) {
                $page = get_post($form_options[$opt .'_page_id']);
                $old_post = $post;
                $post = $page;
                $content = apply_filters('frm_content', $page->post_content, $form, $entry_id);
                echo apply_filters('the_content', $content);
                $post = $old_post;
            }
		} else if ( $method == 'redirect' ) {
            global $frm_vars;

            add_filter('frm_use_wpautop', '__return_false');
            $success_url = apply_filters('frm_content', trim($form_options[$opt .'_url']), $form, $entry_id);
            $success_msg = isset($form_options[$opt .'_msg']) ? $form_options[$opt .'_msg'] : __( 'Please wait while you are redirected.', 'formidable' );

			$redirect_msg = '<div class="' . esc_attr( FrmFormsHelper::get_form_style_class( $form ) ) . '"><div class="frm-redirect-msg frm_message">' . $success_msg . '<br/>' .
                sprintf(__( '%1$sClick here%2$s if you are not automatically redirected.', 'formidable' ), '<a href="'. esc_url($success_url) .'">', '</a>') .
                '</div></div>';

            $redirect_msg = apply_filters('frm_redirect_msg', $redirect_msg, array(
                'entry_id' => $entry_id, 'form_id' => $form->id, 'form' => $form
            ));

            $args['id'] = $entry_id;
            add_filter('frm_redirect_url', 'FrmProEntriesController::redirect_url');
            //delete the entry on frm_redirect_url hook
            $success_url = apply_filters('frm_redirect_url', $success_url, $form, $args);
            $doing_ajax = FrmAppHelper::doing_ajax();

            if ( isset($args['ajax']) && $args['ajax'] && $doing_ajax ) {
                echo json_encode( array( 'redirect' => $success_url));
                die();
            } else if ( ! $doing_ajax && ! headers_sent() ) {
                wp_redirect( esc_url_raw( $success_url ) );
                die();
            }

            add_filter('frm_use_wpautop', '__return_true');

            $response = $redirect_msg;

			$response .= "<script type='text/javascript'>jQuery(document).ready(function(){ setTimeout(window.location='" . esc_url_raw( $success_url ) . "', 8000); });</script>";

			if ( headers_sent() ) {
				echo $response;
			} else {
                wp_redirect( esc_url_raw( $success_url ) );
                die();
            }
        } else {
            $frm_settings = FrmAppHelper::get_settings();
            $frmpro_settings = FrmProAppHelper::get_settings();

            $msg = ( $opt == 'edit' ) ? $frmpro_settings->edit_msg : $frm_settings->success_msg;
			$message = isset( $form->options[ $opt .'_msg' ] ) ? $form->options[ $opt .'_msg' ] : $msg;

            // Replace $message with save draft message if we are saving a draft
            FrmProFormsHelper::save_draft_msg( $message, $form );

			$class = 'frm_message';
			$message = FrmFormsHelper::get_success_message( compact( 'message', 'form', 'entry_id', 'class' ) );

            return $message;
        }
    }

	public static function delete_entry( $post_id ) {
        global $wpdb;
        $entry = FrmDb::get_row( 'frm_items', array( 'post_id' => $post_id), 'id');
        self::maybe_delete_entry($entry);
    }

	public static function trashed_post( $post_id ) {
        $form_id = get_post_meta($post_id, 'frm_form_id', true);
		if ( ! $form_id ) {
			return;
		}

        $display = FrmProDisplay::get_auto_custom_display( array( 'form_id' => $form_id));
		if ( $display ) {
            update_post_meta($post_id, 'frm_display_id', $display->ID);
		} else {
            delete_post_meta($post_id, 'frm_display_id');
		}
    }

    public static function create_entry_from_post_box( $post_type, $post = false ) {
        if ( ! $post || ! isset($post->ID) || $post_type == 'attachment' || $post_type == 'link' ) {
            return;
        }

        global $wpdb, $frm_vars;

        //don't show the meta box if there is already an entry for this post
        $post_entry = FrmDb::get_var( $wpdb->prefix .'frm_items', array( 'post_id' => $post->ID) );
        if ( $post_entry ) {
            return;
        }

        //don't show meta box if no forms are set up to create this post type
		$actions = FrmFormAction::get_action_for_form( 0, 'wppost' );
        if ( ! $actions ) {
            return;
        }

        $form_ids = array();
        foreach ( $actions as $action ) {
            if ( $action->post_content['post_type'] == $post_type && $action->menu_order ) {
                $form_ids[] = $action->menu_order;
            }
        }

        if ( empty($form_ids) ) {
            return;
        }

		$forms = FrmDb::get_results( 'frm_forms', array( 'id' => $form_ids ), 'id, name' );

        $frm_vars['post_forms'] = $forms;

        if ( current_user_can('frm_create_entries') ) {
            add_meta_box( 'frm_create_entry', __( 'Create Entry in Form', 'formidable' ), 'FrmProEntriesController::render_meta_box_content', null, 'side' );
        }
    }

	public static function render_meta_box_content( $post ) {
        global $frm_vars;
        $i = 1;

        echo '<p>';
        foreach ( (array) $frm_vars['post_forms'] as $form ) {
            if ( $i != 1 ) {
                echo ' | ';
            }

            $i++;
            echo '<a href="javascript:frmCreatePostEntry('. (int) $form->id .','. (int) $post->ID .')">'. esc_html( FrmAppHelper::truncate($form->name, 15) ) .'</a>';
            unset($form);
        }

        echo '</p>';
    }

    public static function create_post_entry( $id = false, $post_id = false ) {
        if ( FrmAppHelper::doing_ajax() ) {
            check_ajax_referer( 'frm_ajax', 'nonce' );
        }

        if ( ! $id ) {
            $id = (int) $_POST['id'];
        }

        if ( ! $post_id ) {
            $post_id = (int) $_POST['post_id'];
        }

        if ( ! is_numeric($id) || ! is_numeric($post_id) ) {
            return;
        }

        $post = get_post($post_id);

        global $wpdb;
        $values = array(
            'description' => __( 'Copied from Post', 'formidable' ),
            'form_id' => $id,
            'created_at' => $post->post_date_gmt,
            'name' => $post->post_title,
            'item_key' => FrmAppHelper::get_unique_key($post->post_name, $wpdb->prefix .'frm_items', 'item_key'),
            'user_id' => $post->post_author,
            'post_id' => $post->ID
        );

        $results = $wpdb->insert( $wpdb->prefix .'frm_items', $values );
        unset($values);

        if ( ! $results ) {
            wp_die();
        }

        $entry_id = $wpdb->insert_id;
        $user_id_field = FrmField::get_all_types_in_form($id, 'user_id', 1);

        if ( $user_id_field ) {
            $new_values = array(
                'meta_value' => $post->post_author,
                'item_id' => $entry_id,
                'field_id' => $user_id_field->id,
                'created_at' => current_time('mysql', 1)
            );

            $wpdb->insert( $wpdb->prefix .'frm_item_metas', $new_values );
        }

        $display = FrmProDisplay::get_auto_custom_display( array( 'form_id' => $id, 'entry_id' => $entry_id));
        if ( $display ) {
            update_post_meta($post->ID, 'frm_display_id', $display->ID);
        }

        wp_die();
    }

    /* Export to CSV */
    public static function csv( $form_id = false, $search = '', $fid = '' ) {
        FrmAppHelper::permission_check( 'frm_view_entries' );

        if ( ! $form_id ) {
			$form_id = FrmAppHelper::get_param( 'form', '', 'get', 'absint' );
			$search = FrmAppHelper::get_param( ( isset( $_REQUEST['s'] ) ? 's' : 'search' ), '', 'get', 'sanitize_text_field' );
			$fid = FrmAppHelper::get_param( 'fid', '', 'get', 'sanitize_text_field' );
        }

        if ( ! ini_get('safe_mode') ) {
            set_time_limit(0); //Remove time limit to execute this function
            $mem_limit = str_replace('M', '', ini_get('memory_limit'));
            if ( (int) $mem_limit < 256 ) {
                ini_set('memory_limit', '256M');
            }
        }

        global $wpdb;

        $form = FrmForm::getOne($form_id);
        $form_id = $form->id;

		$where = array( 'fi.type not' => FrmField::no_save_fields() );
		$where[] = array( 'or' => 1, 'fi.form_id' => $form->id, 'fr.parent_form_id' => $form->id );

		$csv_fields = apply_filters('frm_csv_field_ids', '', $form_id, array( 'form' => $form));
		if ( $csv_fields ) {
			if ( ! is_array( $csv_fields ) ) {
				$csv_fields = explode(',', $csv_fields);
			}
			if ( ! empty($csv_fields) )	{
				$where['fi.id'] = $csv_fields;
			}
		}
		$form_cols = FrmField::getAll( $where, 'field_order' );

		$item_id = FrmAppHelper::get_param( 'item_id', false, 'get', 'sanitize_text_field' );
		if ( ! empty( $item_id ) ) {
			$item_id = explode( ',', $item_id );
		}

        $query = array( 'form_id' => $form_id );

        if ( $item_id ) {
            $query['id'] = $item_id;
		}

		if ( ! empty($search) && ! $item_id ) {
			$query = FrmProEntriesHelper::get_search_str( $query, $search, $form_id, $fid );
        }

		/**
		 * Allows the query to be changed for fetching the entry ids to include in the export
		 *
		 * $query is the array of options to be filtered. It includes form_id, and maybe id (array of entry ids),
		 * and the search query. This should return an array, but it can be handled as a string as well.
		 */
        $query = apply_filters('frm_csv_where', $query, compact('form_id'));

		$entry_ids = FrmDb::get_col( $wpdb->prefix .'frm_items it', $query );
        unset($query);

		if ( empty( $entry_ids ) ) {
			esc_html_e( 'There are no entries for that form.', 'formidable' );
		} else {
			FrmProCSVExportHelper::generate_csv( compact( 'form', 'entry_ids', 'form_cols' ) );
		}

        wp_die();
    }

    public static function get_search_str( $where_clause = '', $search_str, $form_id = false, $fid = false ) {
        _deprecated_function( __FUNCTION__, '2.0', 'FrmProEntriesHelper::get_search_str' );
        return FrmProEntriesHelper::get_search_str($where_clause, $search_str, $form_id, $fid);
    }

	public static function get_new_vars( $errors = array(), $form = false, $message = '' ) {
        global $frm_vars;
        $description = true;
        $title = false;
        $form = apply_filters('frm_pre_display_form', $form);
        if ( ! $form ) {
            wp_die( __( 'You are trying to access an entry that does not exist.', 'formidable' ) );
            return;
        }

        $fields = FrmFieldsHelper::get_form_fields( $form->id, ! empty( $errors ) );
        $values = $fields ? FrmEntriesHelper::setup_new_vars($fields, $form) : array();

        $frm_settings = FrmAppHelper::get_settings();
        $submit = (isset($frm_vars['next_page'][$form->id])) ? $frm_vars['next_page'][$form->id] : (isset($values['submit_value']) ? $values['submit_value'] : $frm_settings->submit_value);

		if ( is_object( $submit ) ) {
            $submit = $submit->name;
		}
        require(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-entries/new.php');
    }

	private static function get_edit_vars( $id, $errors = array(), $message= '' ) {
        global $frm_vars;
        $description = true;
        $title = false;

        $record = FrmEntry::getOne( $id, true );
        if ( ! $record ) {
            wp_die( __( 'You are trying to access an entry that does not exist.', 'formidable' ) );
            return;
        }

        $frm_vars['editing_entry'] = $id;

        $form = FrmForm::getOne($record->form_id);
        $form = apply_filters('frm_pre_display_form', $form);

        $fields = FrmFieldsHelper::get_form_fields( $form->id, ! empty( $errors ) );
        $values = FrmAppHelper::setup_edit_vars($record, 'entries', $fields);

        $frmpro_settings = new FrmProSettings();
        $edit_create = ($record->is_draft) ? (isset($values['submit_value']) ? $values['submit_value'] : $frmpro_settings->submit_value) : (isset($values['edit_value']) ? $values['edit_value'] : $frmpro_settings->update_value);
        $submit = (isset($frm_vars['next_page'][$form->id])) ? $frm_vars['next_page'][$form->id] : $edit_create;
        unset($edit_create);

		if ( is_object( $submit ) ) {
            $submit = $submit->name;
		}
        require(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-entries/edit.php');
    }

	public static function &filter_shortcode_value( $value, $tag, $atts ) {
        if ( isset($atts['striphtml']) && $atts['striphtml'] ) {
            $allowed_tags = apply_filters('frm_striphtml_allowed_tags', array(), $atts);
            $value = wp_kses($value, $allowed_tags);
        }

        if ( ! isset($atts['keepjs']) || ! $atts['keepjs'] ) {
            if ( is_array($value) ) {
                foreach ( $value as $k => $v ) {
                    $value[$k] = wp_kses_post($v);
                    unset($k, $v);
                }
            } else {
                $value = wp_kses_post($value);
            }
        }

        return $value;
    }

	/**
	 * Trigger from the frm_display_value_atts hook
	 * @since 2.0
	 */
	public static function display_value_atts( $atts, $field ) {
		if ( in_array( $field->type, array( 'file', 'image' ) ) ) {
			$atts['truncate'] = false;
			$atts['html'] = true;
		}
		return $atts;
	}

    public static function &filter_display_value( $value, $field, $atts = array() ) {
		$defaults = array( 'html' => 0, 'type' => $field->type, 'keepjs' => 0 );
		$atts = array_merge( $defaults, $atts );

        switch ( $atts['type'] ) {
            case 'user_id':
                $value = FrmProFieldsHelper::get_display_name($value);
            break;
            case 'date':
                $value = FrmProFieldsHelper::get_date($value);
            break;
            case 'file':
                $old_value = $value;
                if ( $atts['html'] ) {
                    $value = '<div class="frm_file_container">';
                } else {
                    $value = '';
                }

                foreach ( (array) $old_value as $mid ) {
                    if ( $atts['html'] ){
                        $img = FrmProFieldsHelper::get_file_icon($mid);
                        $value .= $img;
						if ( $atts['show_filename'] && $img && preg_match( '/wp-includes\/images\/(crystal|media)/', $img ) ) {
                            //prevent two filenames
                            $atts['show_filename'] = $show_filename = false;
                        }

                        unset($img);

                        if ( $atts['html'] && $atts['show_filename'] ) {
                            $value .= '<br/>' . FrmProFieldsHelper::get_file_name($mid) . '<br/>';
                        }

                        if ( isset( $show_filename ) ) {
                            //if skipped filename, show it for the next file
                            $atts['show_filename'] = true;
                            unset($show_filename);
                        }
                    } else if ( $mid ) {
                        $value .= FrmProFieldsHelper::get_file_name($mid) . $atts['sep'];
                    }
                }

                $value = rtrim($value, $atts['sep']);
                if ( $atts['html'] ) {
                    $value .= '</div>';
                }
            break;

            case 'data':
                if ( ! is_numeric($value) ) {
                    if ( ! is_array($value) ) {
                        $value = explode($atts['sep'], $value);
                    }

                    if ( is_array($value) ) {
                        $new_value = '';
                        foreach ( $value as $entry_id ) {
                            if ( ! empty( $new_value ) ) {
                                $new_value .= $atts['sep'];
                            }

                            if ( is_numeric($entry_id) ) {
                                $new_value .= FrmProFieldsHelper::get_data_value($entry_id, $field, $atts);
                            } else {
                                $new_value .= $entry_id;
                            }
                        }
                        $value = $new_value;
                    }
                } else {
                    //replace item id with specified field
                    $new_value = FrmProFieldsHelper::get_data_value($value, $field, $atts);

					if ( FrmProField::is_list_field( $field ) ) {
                        $linked_field = FrmField::getOne($field->field_options['form_select']);
                        if ( $linked_field && $linked_field->type == 'file' ) {
                            $old_value = explode(', ', $new_value);
                            $new_value = '';
                            foreach ( $old_value as $v ) {
								$new_value .= '<img src="' . esc_url( $v ) . '" class="frm_image_from_url" alt="" />';
                                if ( $atts['show_filename'] ) {
                                    $new_value .= '<br/>'. $v;
                                }
                                unset($v);
                            }
                        } else {
                            $new_value = $value;
                        }
                    }

                    $value = $new_value;
                }
            break;

            case 'image':
				$value = FrmProFieldsHelper::get_image_display_value( $value, array( 'html' => true ) );
            break;
        }

        if ( ! $atts['keepjs'] ) {
			$value = FrmAppHelper::recursive_function_map( $value, 'wp_kses_post' );
        }

        return FrmEntriesController::filter_display_value($value, $field, $atts);
    }

	public static function route( $action ) {
        add_filter('frm_entry_stop_action_route', '__return_true');

		add_action( 'frm_load_form_hooks', 'FrmHooksController::trigger_load_form_hooks' );
        FrmAppHelper::trigger_hook_load( 'form' );

        switch ( $action ) {
            case 'create':
                return self::create();
            case 'edit':
                return self::edit();
            case 'update':
                return self::update();
            case 'duplicate':
                return self::duplicate();

            case 'new':
                return self::new_entry();

            default:
				$action = FrmAppHelper::get_param( 'action', '', 'get', 'sanitize_text_field' );
                if ( $action == -1 ) {
					$action = FrmAppHelper::get_param( 'action2', '', 'get', 'sanitize_title' );
                }

                if ( strpos($action, 'bulk_') === 0 ) {
                    FrmAppHelper::remove_get_action();
                    return self::bulk_actions($action);
                }

                return FrmEntriesController::display_list();
        }
    }

    /**
     * @return string The name of the entry listing class
     */
    public static function list_class(){
        return 'FrmProEntriesListHelper';
    }

	public static function manage_columns( $columns ) {
        global $frm_vars;
        $form_id = FrmForm::get_current_form_id();

        $columns = array( 'cb' => '<input type="checkbox" />') + $columns;
        $columns[$form_id .'_post_id'] = __( 'Post', 'formidable' );
        $columns[$form_id .'_is_draft'] = __( 'Draft', 'formidable' );

        $frm_vars['cols'] = $columns;

        return $columns;
    }

	public static function row_actions( $actions, $item ) {
        $edit_link = '?page=formidable-entries&frm_action=edit&id='. $item->id;
		if ( current_user_can('frm_edit_entries') ) {
		    $actions['edit'] = '<a href="' . esc_url( $edit_link ) .'">'. __( 'Edit') .'</a>';
		}

        if ( current_user_can('frm_create_entries') ) {
            $duplicate_link = '?page=formidable-entries&frm_action=duplicate&id='. $item->id .'&form='. $item->form_id;
			$actions['duplicate'] = '<a href="' . esc_url( wp_nonce_url( $duplicate_link ) ) . '">' . __( 'Duplicate', 'formidable' ) . '</a>';
        }

        // move delete link to the end of the links
        if ( isset($actions['delete']) ) {
            $delete_link = $actions['delete'];
            unset($actions['delete']);
            $actions['delete'] = $delete_link;
        }

        return $actions;
    }

	public static function get_form_results( $atts ) {
		FrmAppHelper::sanitize_array( $atts );

        $atts = shortcode_atts( array(
            'id' => false, 'cols' => 99, 'style' => true,
            'fields' => false, 'clickable' => false, 'user_id' => false,
            'google' => false, 'pagesize' => 20, 'sort' => true,
            'edit_link' => false, 'delete_link' => false, 'page_id' => false,
            'no_entries' => __( 'No Entries Found', 'formidable' ),
            'confirm' =>  __( 'Are you sure you want to delete that entry?', 'formidable' ),
			'drafts' => '0',
        ), $atts );

		$atts['form'] = self::get_form( $atts );
		if ( ! $atts['form'] ) {
			return;
		}

        if ( $atts['fields'] ) {
            $atts['fields'] = explode( ',', $atts['fields'] );
		}

		self::get_table_values( $atts );
		if ( empty( $atts['form_cols'] ) ) {
			$contents = '<div class="frm_no_entries">' . __( 'There are no matching fields. Please check your formresults shortcode to make sure you are using the correct form and field IDs.', 'formidable' ) . '</div>';
			return $contents;
		}

        $contents = '';
		self::add_delete_entry_message( $atts, $contents );
		self::setup_edit_link( $atts );
		self::setup_delete_link( $atts );

		$filename = self::set_formresults_filename( $atts );

		self::load_formresults_scripts( $atts );

        ob_start();
        include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-entries/'. $filename .'.php');
        $contents .= ob_get_contents();
        ob_end_clean();

        if ( ! $atts['google'] && $atts['clickable'] ) {
			$contents = make_clickable( $contents );
        }

        return $contents;
    }

	/**
	* Get the form for the formresults table
	*
	* @since 2.0.09
	* @param array $atts
	* @return object
	*/
	private static function get_form( $atts ) {
        if ( ! $atts['id'] ) {
            return false;
        }

		return FrmForm::getOne( $atts['id'] );
	}

	/**
	* Get entries and fields for formresults
	*
	* @since 2.0.09
	* @param array $atts
	*/
	private static function get_table_values( &$atts ) {
		// Get all fields in the form
		$atts['form_cols'] = FrmField::get_all_for_form( $atts['form']->id, '', 'include' );

		// Get all entries for the form
		$atts['entries'] = self::get_entries_for_table( $atts );

		$subforms_to_include = array();
		$field_count = 0;
		foreach( $atts['form_cols'] as $k => $f ) {
			if ( $field_count < $atts['cols'] && self::is_field_needed( $f, $atts, $subforms_to_include ) ) {
				$field_count++;
				self::get_sub_field_values( $f, $atts );
			} else {
				unset( $atts['form_cols'][ $k ] );
			}
		}
	}

	private static function get_entries_for_table( $atts ) {
		$where = array( 'it.form_id' => $atts['form']->id );

		if ( $atts['drafts'] != 'both' ) {
			$where['it.is_draft'] = (int) $atts['drafts'];
		}

		if ( $atts['user_id'] ) {
			$where['user_id'] = (int) FrmAppHelper::get_user_id_param( $atts['user_id'] );
		}

		$s = FrmAppHelper::get_param( 'frm_search', false, 'get', 'sanitize_text_field' );
		if ( $s ) {
			$new_ids = FrmProEntriesHelper::get_search_ids( $s, $atts['form']->id, array( 'is_draft' => $atts['drafts'] ) );
			$where['it.id'] = $new_ids;
		}

		if ( isset( $new_ids ) && empty( $new_ids ) ) {
			$entries = false;
		} else {
			$entries = FrmEntry::getAll( $where, '', '', true, false );
		}

		return $entries;
	}

	/**
	* Check if each field is needed in the formresults table
	*
	* @since 2.0.09
	* @param object $f - field
	* @param array $atts
	* @param array $subforms_to_include
	* @return boolean
	*/
	private static function is_field_needed( $f, $atts, &$subforms_to_include ) {
		if ( ! empty( $atts['fields'] ) ) {
			if ( FrmField::is_no_save_field( $f->type ) ) {
				if ( FrmField::is_option_true( $f, 'form_select' ) && ( in_array( $f->id, $atts['fields'] ) || in_array( $f->field_key, $atts['fields'] ) ) ) {
					$subforms_to_include[] = $f->field_options['form_select'];
				}
				return false;
			}

			if ( ! in_array( $f->form_id, $subforms_to_include ) && ! in_array( $f->id, $atts['fields'] ) && ! in_array( $f->field_key, $atts['fields'] ) ) {
				return false;
			}
		} else {
			if ( FrmField::is_no_save_field( $f->type ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	* Get values in nested forms (repeating sections and embed form)
	*
	* @since 2.0.09
	* @param object $field
	* @param array $atts
	*/
	private static function get_sub_field_values( $field, &$atts ){
		foreach ( $atts['entries'] as $key => $entry ) {
			if ( ! isset( $entry->metas[ $field->id ] ) || $entry->metas[ $field->id ] == '' ) {
				FrmProEntryMeta::add_repeating_value_to_entry( $field, $atts['entries'][ $key ] );
			}
		}
	}

	/**
	 * If delete_link is set in formresults and frm_action is set to destroy,
	 * check if entry should be deleted when page is loaded
	 */
	private static function add_delete_entry_message( $atts, &$contents ) {
		$action = FrmAppHelper::simple_get( 'frm_action', 'sanitize_title' );
		if ( $atts['delete_link'] && $action == 'destroy' ) {
			$delete_message = self::ajax_destroy( false, false, false );
			$delete_message = '<div class="' . esc_attr( $atts['style'] ? FrmFormsHelper::get_form_style_class() : '' ) . '"><div class="frm_message">' . $delete_message . '</div></div>';
			$contents = $delete_message;
		}
	}

	/**
	* If edit link is set in formresults, set up values for the edit link
	*
	* @since 2.0.09
	* @param array $atts
	*/
	private static function setup_edit_link( &$atts ) {
		if ( $atts['edit_link'] ) {
			$atts['anchor'] = '';
			if ( ! $atts['page_id'] ) {
				global $post;
				$atts['page_id'] = $post->ID;
				$atts['anchor'] = '#form_'. $atts['form']->form_key;
			}
			if ( $atts['edit_link'] === '1' ) {
				$atts['edit_link'] = __( 'Edit', 'formidable' );
			}
			$atts['permalink'] = get_permalink( $atts['page_id'] );
		}
	}

	/**
	* If delete_link is set to true in formresults, set the delete link text
	*
	* @since 2.0.09
	* @param array $atts
	*/
	private static function setup_delete_link( &$atts ) {
		if ( $atts['delete_link'] === '1' ) {
			$atts['delete_link'] = __( 'Delete', 'formidable' );
		}
	}

	/**
	* Get the filename for the formresults table
	*
	* @since 2.0.09
	* @param array $atts
	* @return string $filename
	*/
	private static function set_formresults_filename( &$atts ) {
		if ( $atts['google'] ) {
			$filename = 'google_table';
			self::prepare_google_table( $atts );
		} else {
			$atts['fields'] = (array) $atts['fields'];
			$filename = 'table';
		}
		return $filename;
	}

	private static function prepare_google_table( $atts ) {
		global $frm_vars;

		$options = array(
			'allowHtml' => true,
			'sort'      => $atts['sort'] ? 'enable' : 'disable',
		);

		if ( $atts['pagesize'] ) {
			$options['page']     = 'enable';
			$options['pageSize'] = (int) $atts['pagesize'];
		}

		if ( $atts['style'] ) {
			$options['cssClassNames'] = array( 'oddTableRow' => 'frm_even' );
		}

		$shortcode_options = $atts;
		$shortcode_options['form_id'] = $atts['form']->id;
		unset( $shortcode_options['entries'], $shortcode_options['form_cols'], $shortcode_options['form'] );
		unset( $shortcode_options['permalink'], $shortcode_options['anchor'] );

		$graph_vals = array(
			'fields'    => array(),
			'entries'   => array(),
			'options'   => $shortcode_options,
			'graphOpts' => $options,
		);

		if ( $atts['clickable'] ) {
			$graph_vals['options']['no_entries'] = make_clickable( $graph_vals['options']['no_entries'] );
		}

		$first_loop = true;
		foreach ( $atts['entries'] as $k => $entry ) {
			$this_entry = array(
				'id'    => $entry->id,
				'metas' => array(),
			);

			foreach ( $atts['form_cols'] as $col ) {
				$field_value = isset( $entry->metas[ $col->id ] ) ? $entry->metas[ $col->id ] : false;
				$val = FrmEntriesHelper::display_value( $field_value, $col, array(
					'type' => $col->type, 'post_id' => $entry->post_id,
					'entry_id' => $entry->id, 'show_filename' => false
				) );

				if ( $col->type == 'number' ) {
					$val = empty( $val ) ? '0' : $val;
				} else if (  ( $col->type == 'checkbox' || $col->type == 'select' ) && count( $col->options ) == 1 ) {
					// force boolean values
					$val = empty( $val ) ? false : true;
				} else if ( empty( $val ) ) {
					$val = '';
				} else {
					$val = ( $atts['clickable'] && $col->type != 'file' ) ? make_clickable( $val ) : $val;
				}

				$this_entry['metas'][ $col->id ] = $val;

				if ( $first_loop ) {
					// add the fields to graphs on first loop only
					$graph_vals['fields'][] = array(
						'id'        => $col->id,
						'type'      => $col->type,
						'name'      => $col->name,
						'options'   => $col->options,
						'field_options' => array( 'post_field' => isset( $col->field_options['post_field'] ) ? $col->field_options['post_field'] : '' ),
					);
				}
				unset( $col );
			}

			if ( $atts['edit_link'] && FrmProEntriesHelper::user_can_edit( $entry, $atts['form'] ) ) {
				$this_entry['editLink'] = esc_url_raw( add_query_arg( array( 'frm_action' => 'edit', 'entry' => $entry->id ), $atts['permalink'] ) ) . $atts['anchor'];
			}

			if ( $atts['delete_link'] && FrmProEntriesHelper::user_can_delete( $entry ) ){
				$this_entry['deleteLink'] = esc_url_raw( add_query_arg( array( 'frm_action' => 'destroy', 'entry' => $entry->id ) ) );
			}
			$graph_vals['entries'][] = $this_entry;

			$first_loop = false;
			unset( $k, $entry, $this_entry );
		}

		if ( ! isset( $frm_vars['google_graphs'] ) ) {
			$frm_vars['google_graphs'] = array();
		}

		if ( ! isset( $frm_vars['google_graphs']['table'] ) ) {
			$frm_vars['google_graphs']['table'] = array();
		}

		$frm_vars['google_graphs']['table'][] = $graph_vals;
	}

	/**
	* Load JS and CSS for formresults table
	*/
	private static function load_formresults_scripts( $atts ) {
		global $frm_vars;

		// Trigger CSS loading
		if ( $atts['style'] ) {
		    $frm_vars['load_css'] = true;
		}

		// Trigger the js load
		$frm_vars['forms_loaded'][] = true;
	}


	public static function get_search( $atts ) {
        $atts = shortcode_atts( array(
            'post_id' => '', 'label' => __( 'Search', 'formidable' ),
            'style' => false,
        ), $atts);

        if ( $atts['post_id'] == '' ) {
            global $post;
			if ( $post ) {
                $atts['post_id'] = $post->ID;
			}
        }

        if ( $atts['post_id'] != '' ) {
            $action_link = get_permalink($atts['post_id']);
        } else {
            $action_link = '';
        }

        if ( ! empty($atts['style']) ) {
            global $frm_vars;
            $frm_vars['forms_loaded'][] = true;

            if ( $atts['style'] == 1 || 'true' == $atts['style'] ) {
                $atts['style'] = FrmStylesController::get_form_style_class('with_frm_style', 'default');
            } else {
                $atts['style'] .= ' with_frm_style';
            }
        }

        ob_start();
        include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-entries/search.php');
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }

    public static function entry_link_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'id' => false, 'field_key' => 'created_at', 'type' => 'list', 'logged_in' => true,
            'edit' => true, 'class' => '', 'link_type' => 'page', 'blank_label' => '',
            'param_name' => 'entry', 'param_value' => 'key', 'page_id' => false, 'show_delete' => false,
            'confirm' => __( 'Are you sure you want to delete that entry?', 'formidable' ),
            'drafts' => false, 'order' => '',
        ), $atts);

        $user_ID = get_current_user_id();
        if ( ! $atts['id'] || ( $atts['logged_in'] && ! $user_ID) ) {
            return;
        }

        $atts = self::fill_entry_links_atts($atts);

        $action = ( isset($_GET) && isset($_GET['frm_action']) ) ? 'frm_action' : 'action';
		$entry_action = FrmAppHelper::simple_get( $action, 'sanitize_title' );
		$entry_key = FrmAppHelper::simple_get( 'entry', 'sanitize_title' );

        if ( $entry_action == 'destroy' ) {
            self::maybe_delete_entry($entry_key);
        }

        $entries = self::get_entry_link_entries( $atts );
        if ( empty($entries) ) {
            return;
        }

        $public_entries = array();
        $post_status_check = array();

        foreach ( $entries as $k => $entry ) {
            if ( $entry_action == 'destroy' && in_array($entry_key, array($entry->item_key, $entry->id)) ) {
                continue;
            }

            if ( $entry->post_id ) {
                $post_status_check[$entry->post_id] = $entry->id;
            }
            $public_entries[$entry->id] = $entry;
        }

        if ( ! empty($post_status_check) ) {
			global $wpdb;
			$query = array( 'post_status !' => 'publish', 'ID' => array_keys( $post_status_check ) );
			$remove_entries = FrmDb::get_col( $wpdb->posts, $query, 'ID' );
            unset($query);

            foreach ( $remove_entries as $entry_post_id ) {
                unset($public_entries[$post_status_check[$entry_post_id]]);
            }
            unset($remove_entries);
        }

        $entries = $public_entries;
        unset($public_entries);

        $content = array();
        switch ( $atts['type'] ) {
            case 'list':
                self::entry_link_list($entries, $atts, $content);
            break;
            case 'select':
                self::entry_link_select($entries, $atts, $content);
            break;
            case 'collapse':
                self::entry_link_collapse($entries, $atts, $content);
        }

        $content = implode('', $content);
        return $content;
    }

	private static function fill_entry_links_atts( $atts ) {
        $atts['id'] = (int) $atts['id'];
        if ( $atts['show_delete'] === 1 ) {
            $atts['show_delete'] = __( 'Delete', 'formidable' );
        }
        $atts['label'] = $atts['show_delete'];

        $atts['field'] = false;
        if ( $atts['field_key'] != 'created_at' ) {
            $atts['field'] = FrmField::getOne($atts['field_key']);
            if ( ! $atts['field'] ) {
                $atts['field_key'] = 'created_at';
            }
        }

        if ( ! in_array($atts['type'], array( 'list', 'collapse', 'select') ) ) {
            $atts['type'] = 'select';
        }

		if ( empty( $atts['confirm'] ) ) {
			 $atts['confirm'] = __( 'Are you sure you want to delete that entry?', 'formidable' );
		}

        global $post;
        $atts['permalink'] = get_permalink( $atts['page_id'] ? $atts['page_id'] : $post->ID );

        return $atts;
    }

	private static function get_entry_link_entries( $atts ) {
		$s = FrmAppHelper::get_param( 'frm_search', false, 'get', 'sanitize_text_field' );

        // Convert logged_in parameter to user_id for other functions
        $atts['user_id'] = false;
        if ( $atts['logged_in'] ) {
            global $wpdb;
            $atts['user_id'] = get_current_user_id();
        }

        if ( $s ) {
            $entry_ids = FrmProEntriesHelper::get_search_ids( $s, $atts['id'], array( 'is_draft' => $atts['drafts'], 'user_id' =>  $atts['user_id'] ) );
        } else {
			$entry_ids = FrmEntryMeta::getEntryIds( array( 'fi.form_id' => (int) $atts['id']), '', '', true, array( 'is_draft' => $atts['drafts'], 'user_id' =>  $atts['user_id'] ) );
        }

        if ( empty($entry_ids) ) {
            return;
        }

        $order = ( $atts['type'] == 'collapse' || $atts['order'] == 'DESC' ) ? ' ORDER BY it.created_at DESC' : '';

        $entries = FrmEntry::getAll( array( 'it.id' => $entry_ids ), $order, '', true);

        return $entries;
    }

	private static function entry_link_list( $entries, $atts, array &$content ) {
        $content[] = '<ul class="frm_entry_ul '. $atts['class'] .'">'. "\n";

        foreach ( $entries as $entry ) {
            $value = self::entry_link_meta_value($entry, $atts);
            $link = self::entry_link_href($entry, $atts);

			$content[] = '<li><a href="' . esc_url( $link ) . '">' . $value . '</a>';
            if ( ! empty( $atts['show_delete'] ) && FrmProEntriesHelper::user_can_delete( $entry ) ) {
				$content[] = ' <a href="' . esc_url( add_query_arg( array( 'frm_action' => 'destroy', 'entry' => $entry->id ), $atts['permalink'] ) ) . '" class="frm_delete_list" data-frmconfirm="' . esc_attr( $atts['confirm'] ) . '">' . $atts['show_delete'] . '</a>' . "\n";
            }
            $content[] = "</li>\n";
        }

        $content[] = "</ul>\n";
    }

	private static function entry_link_collapse( $entries, $atts, array &$content ) {
        FrmStylesHelper::enqueue_jquery_css();
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('formidable' );

        $content[] = '<div class="frm_collapse">';
        $year = $month = '';
        $prev_year = $prev_month = false;

        foreach ( $entries as $entry ) {
            $value = self::entry_link_meta_value($entry, $atts);
            $link = self::entry_link_href($entry, $atts);

            $new_year = strftime('%G', strtotime($entry->created_at));
            $new_month = strftime('%B', strtotime($entry->created_at));
            if ( $new_year != $year ) {
                if ( $prev_year ) {
                    if ( $prev_month ) {
                        $content[] = '</ul></div>';
                    }
                    $content[] = '</div>';
                    $prev_month = false;
                }
				$class = $prev_year ? ' frm_hidden' : '';
                $triangle = $prev_year ? 'e' : 's';
				$content[] = "\n" . '<div class="frm_year_heading frm_year_heading_' . esc_attr( $atts['id'] ) . '">
					<span class="ui-icon ui-icon-triangle-1-' . esc_attr( $triangle ) . '"></span>' . "\n" .
                    '<a>' . sanitize_text_field( $new_year ) . '</a></div>' . "\n" .
					'<div class="frm_toggle_container' . esc_attr( $class ) . '">' . "\n";
                $prev_year = true;
            }

            if ( $new_month != $month ) {
                if ( $prev_month ) {
                    $content[] = '</ul></div>';
                }
				$class = $prev_month ? ' frm_hidden' : '';
                $triangle = $prev_month ? 'e' : 's';
				$content[] = '<div class="frm_month_heading frm_month_heading_' . esc_attr( $atts['id'] ) . '">
					<span class="ui-icon ui-icon-triangle-1-' . esc_attr( $triangle ) . '"></span>' . "\n" .
					'<a>' . sanitize_text_field( $new_month ) . '</a>' . "\n" . '</div>' . "\n" .
					'<div class="frm_toggle_container frm_month_listing' . esc_attr( $class ) . '"><ul>' . "\n";
                $prev_month = true;
            }
			$content[] = '<li><a href="' . esc_url( $link ) . '">' . $value . '</a>';

            if ( $atts['show_delete'] && FrmProEntriesHelper::user_can_delete($entry) ) {
				$content[] = ' <a href="' . esc_url( add_query_arg( array( 'frm_action' => 'destroy', 'entry' => $entry->id ), $atts['permalink'] ) ) . '" class="frm_delete_list" data-frmconfirm="' . esc_attr( $atts['confirm'] ) . '">' . $atts['show_delete'] . '</a>' . "\n";
            }
            $content[] = "</li>\n";
            $year = $new_year;
            $month = $new_month;
        }

        if ( $prev_year ) {
            $content[] = '</div>';
        }
        if ( $prev_month ) {
            $content[] = '</ul></div>';
        }
        $content[] = '</div>';
    }

	private static function entry_link_select( $entries, $atts, array &$content ) {
        global $post;

		$content[] = '<select id="frm_select_form_' . esc_attr( $atts['id'] ) . '" name="frm_select_form_' . esc_attr( $atts['id'] ) . '" class="' . esc_attr( $atts['class'] ) . '" onchange="location=this.options[this.selectedIndex].value;">' . "\n";
		$content[] = '<option value="' . esc_attr( get_permalink( $post->ID ) ) . '">' . $atts['blank_label'] . '</option>' . "\n";
		$entry_param = FrmAppHelper::simple_get( 'entry', 'sanitize_title' );

        foreach ( $entries as $entry ) {
            $value = self::entry_link_meta_value($entry, $atts);
            $link = self::entry_link_href($entry, $atts);

			$content[] = '<option value="' . esc_url( $link ) . '" ' . selected( $entry_param, $entry->item_key, false ) . '>' . esc_attr($value) . "</option>\n";
        }

        $content[] = "</select>\n";
        if ( $atts['show_delete'] && $entry_param ) {
			$content[] = " <a href='" . esc_url( add_query_arg( array( 'frm_action' => 'destroy', 'entry' => $entry_param ), $atts['permalink'] ) ) . "' class='frm_delete_list' data-frmconfirm='" . esc_attr( $atts['confirm'] ) . "'>" . $atts['show_delete'] . "</a>\n";
        }
    }

	private static function entry_link_meta_value( $entry, $atts ) {
        $value = '';

        if ( $atts['field_key'] && $atts['field_key'] != 'created_at' ) {
            if ( $entry->post_id && ( ( $atts['field'] && $atts['field']->field_options['post_field'] ) || $atts['field']->type == 'tag' ) ) {
                $meta = false;
                $value = FrmProEntryMetaHelper::get_post_value(
                    $entry->post_id, $atts['field']->field_options['post_field'], $atts['field']->field_options['custom_field'],
                    array(
                        'type' => $atts['field']->type, 'form_id' => $atts['field']->form_id, 'field' => $atts['field']
                    )
                );
            } else {
                $meta = isset($entry->metas[$atts['field']->id]) ? $entry->metas[$atts['field']->id] : '';
            }
        } else {
            $meta = reset($entry->metas);
        }

        self::entry_link_value($entry, $atts, $meta, $value);

        return $value;
    }

	private static function entry_link_value( $entry, $atts, $meta, &$value ) {
        if ( 'created_at' != $atts['field_key'] && $meta ) {
            if ( is_object($meta) ) {
                $value = $meta->meta_value;
            } else {
                $value = $meta;
            }
        }

        if ( '' == $value ) {
            $value = date_i18n(get_option('date_format'), strtotime($entry->created_at));
            return;
        }

        $value = FrmEntriesHelper::display_value($value, $atts['field'], array(
            'type' => $atts['field']->type, 'show_filename' => false
        ));
    }

	private static function entry_link_href( $entry, $atts ) {
        $args = array(
            $atts['param_name'] => ( 'key' == $atts['param_value'] ) ? $entry->item_key : $entry->id,
        );

        if ( $atts['edit'] ) {
            $args['frm_action'] = 'edit';
        }

        if ( $atts['link_type'] == 'scroll' ) {
            $link = '#'. $entry->item_key;
        } else if ( $atts['link_type'] == 'admin' ) {
			$link = add_query_arg( $args, FrmAppHelper::get_server_value( 'REQUEST_URI' ) );
        } else {
            $link = add_query_arg($args, $atts['permalink']);
        }

        return $link;
    }

    public static function entry_edit_link($atts){
        global $post, $frm_vars, $wpdb;
        $atts = shortcode_atts( array(
            'id' => (isset($frm_vars['editing_entry']) ? $frm_vars['editing_entry'] : false),
            'label' => __( 'Edit', 'formidable' ), 'cancel' => __( 'Cancel', 'formidable' ),
            'class' => '', 'page_id' => ( $post ? $post->ID : 0 ), 'html_id' => false,
            'prefix' => '', 'form_id' => false, 'title' => '',
			'fields' => array(), 'exclude_fields' => array(),
        ), $atts);

        $link = '';
        $entry_id = ( $atts['id'] && is_numeric($atts['id']) ) ? $atts['id'] : FrmAppHelper::get_param('entry', false);

        if ( empty($entry_id) && $atts['id'] == 'current' ) {
            if ( isset($frm_vars['editing_entry']) && $frm_vars['editing_entry'] && is_numeric($frm_vars['editing_entry']) ) {
                $entry_id = $frm_vars['editing_entry'];
            } else if ( $post ) {
                $entry_id = FrmDb::get_var( $wpdb->prefix .'frm_items', array( 'post_id' => $post->ID) );
            }
        }

        if ( ! $entry_id || empty($entry_id) ) {
            return '';
        }

        if ( ! $atts['form_id'] ) {
            $atts['form_id'] = (int) FrmDb::get_var( $wpdb->prefix .'frm_items', array( 'id' => $entry_id), 'form_id' );
        }

        //if user is not allowed to edit, then don't show the link
        if ( ! FrmProEntriesHelper::user_can_edit($entry_id, $atts['form_id']) ) {
            return $link;
        }

        if ( empty($atts['prefix']) ) {
           $link = add_query_arg( array( 'frm_action' => 'edit', 'entry' => $entry_id), get_permalink($atts['page_id']));

           if ( $atts['label'] ) {
			   $link = '<a href="' . esc_url( $link ) . '" class="' . esc_attr( $atts['class'] ) . '">' . $atts['label'] . '</a>';
           }

           return $link;
        }

		$action = ( $_POST && isset( $_POST['frm_action'] ) ) ? 'frm_action' : 'action';
		$form_action = FrmAppHelper::get_post_param( $action, '', 'sanitize_title' );
		$posted_form_id = FrmAppHelper::get_post_param( 'form_id', '', 'sanitize_title' );
		$posted_entry_id = FrmAppHelper::get_post_param( 'id', '', 'sanitize_title' );

		if ( $form_action =='update' && $posted_form_id == $atts['form_id'] && $posted_entry_id == $entry_id ) {
			$errors = ( isset( $frm_vars['created_entries'][ $atts['form_id'] ] ) && isset( $frm_vars['created_entries'][ $atts['form_id'] ]['errors'] ) ) ? $frm_vars['created_entries'][ $atts['form_id'] ]['errors'] : array();

            if ( ! empty($errors) ) {
				return FrmFormsController::get_form_shortcode( array( 'id' => $atts['form_id'], 'entry_id' => $entry_id, 'fields' => $atts['fields'], 'exclude_fields' => $atts['exclude_fields'] ) );
            }

            $link .= "<script type='text/javascript'>frmFrontForm.scrollToID('". esc_js( $atts['prefix'] . $entry_id ) ."');</script>";
        }

        if ( empty($atts['title']) ) {
            $atts['title'] = $atts['label'];
        }

        if ( ! $atts['html_id'] ) {
            $atts['html_id'] = 'frm_edit_'. $entry_id;
        }

        $frm_vars['forms_loaded'][] = true;
		$data = array(
			'entryid' => $entry_id,
			'prefix'  => $atts['prefix'],
			'pageid'  => $atts['page_id'],
			'formid'  => $atts['form_id'],
			'cancel'  => $atts['cancel'],
			'edit'    => $atts['label'],
		);
		if ( ! empty( $atts['fields'] ) ) {
			$data['fields'] = implode( ',', (array) $atts['fields'] );
		}
		if ( ! empty( $atts['exclude_fields'] ) ) {
			$data['exclude_fields'] = implode( ',', (array) $atts['exclude_fields'] );
		}

		$link .= '<span class="frm_edit_link_container">';
		$link .= '<a href="#" class="frm_inplace_edit frm_edit_link ' . esc_attr( $atts['class'] ) . '" id="' . esc_attr( $atts['html_id'] ) . '" title="' . esc_attr( $atts['title'] ) . '"';
		foreach ( $data as $name => $label ) {
			$link .= ' data-' . sanitize_title( $name ) . '="' . esc_attr( $label ) .'"';
		}
		$link .= '>' . wp_kses_post( $atts['label'] ) . "</a>\n";
		$link .= '</span>';

        return $link;
    }

	public static function entry_update_field( $atts ) {
        global $frm_vars, $post, $frm_update_link, $wpdb;

        $atts = shortcode_atts( array(
            'id' => (isset($frm_vars['editing_entry']) ? $frm_vars['editing_entry'] : false),
            'field_id' => false, 'form_id' => false,
            'label' => __( 'Update', 'formidable' ), 'class' => '', 'value' => '',
            'message' => '', 'title' => '', 'allow' => '',
        ), $atts);

		$entry_id = ( $atts['id'] && is_numeric( $atts['id'] ) ) ? absint( $atts['id'] ) : FrmAppHelper::get_param( 'entry', false, 'get', 'absint' );

        if ( ! $entry_id || empty($entry_id) ) {
            return;
        }

        if ( ! $atts['form_id'] ) {
            $atts['form_id'] = (int) FrmDb::get_var($wpdb->prefix .'frm_items', array( 'id' => $entry_id), 'form_id');
        }

		if ( $atts['allow'] != 'everyone' && ! FrmProEntriesHelper::user_can_edit( $entry_id, $atts['form_id'] ) ) {
            return;
        }

        $field = FrmField::getOne($atts['field_id']);
        if ( ! $field ) {
            return;
        }

        if ( ! is_numeric($atts['field_id']) ) {
            $atts['field_id'] = $field->id;
        }

        //check if current value is equal to new value
        $current_val = FrmProEntryMetaHelper::get_post_or_meta_value($entry_id, $field);
        if ( $current_val == $atts['value'] ) {
            return;
        }

        if ( ! $frm_update_link ) {
            $frm_update_link = array();
        }

        $num = isset($frm_update_link[$entry_id .'-'. $atts['field_id']]) ? $frm_update_link[$entry_id .'-'. $atts['field_id']] : 0;
        $num = (int) $num + 1;
        $frm_update_link[$entry_id .'-'. $atts['field_id']] = $num;

        if ( empty($atts['title']) ) {
            $atts['title'] = $atts['label'];
        }

        $link = '<a href="#" onclick="frmUpdateField('. $entry_id .','. $atts['field_id'] .',\''. $atts['value'] .'\',\''. htmlspecialchars(str_replace("'", '\"', $atts['message'])) .'\','. $num .');return false;" id="frm_update_field_'. $entry_id .'_'. $atts['field_id'] .'_'. $num .'" class="frm_update_field_link '. $atts['class'] .'" title="'. esc_attr($atts['title']) .'">'. $atts['label'] .'</a>';

        return $link;
    }

	public static function entry_delete_link( $atts ) {
        global $post, $frm_vars;
        $atts = shortcode_atts( array(
            'id' => (isset($frm_vars['editing_entry']) ? $frm_vars['editing_entry'] : false), 'label' => __( 'Delete'),
            'confirm' => __( 'Are you sure you want to delete that entry?', 'formidable' ),
            'class' => '', 'page_id' => (($post) ? $post->ID : 0), 'html_id' => false, 'prefix' => '',
            'title' => '',
        ), $atts);

		$entry_id = FrmAppHelper::get_param( 'id', false, 'get', 'sanitize_text_field' );
		$entry_id = ( $atts['id'] && is_numeric( $atts['id'] ) ) ? $atts['id'] : ( FrmAppHelper::is_admin() ? $entry_id : FrmAppHelper::get_param( 'entry', false, 'get', 'sanitize_text_field' ) );

		if ( empty( $entry_id ) || ! FrmProEntriesHelper::user_can_delete( $entry_id ) ) {
			// User doesn't have permission to delete this entry
            return '';
        }

        $frm_vars['forms_loaded'][] = true;

        if ( ! empty($atts['prefix']) ) {
            if ( ! $atts['html_id'] ) {
                $atts['html_id'] = 'frm_delete_'. $entry_id;
            }

			$link = '<a href="#" class="frm_ajax_delete frm_delete_link '. esc_attr( $atts['class'] ) . '" id="' . esc_attr( $atts['html_id'] ) . '" data-deleteconfirm="' . esc_attr( $atts['confirm'] ) . '" data-entryid="' . esc_attr( $entry_id ) . '" data-prefix="' . esc_attr( $atts['prefix'] ) . '">' . $atts['label'] . "</a>\n";
            return $link;
        }

        $link = '';

        // Delete entry now
		$action = FrmAppHelper::get_param( 'frm_action', '', 'get', 'sanitize_title' );
        if ( $action == 'destroy' ) {
			$entry_key = FrmAppHelper::get_param( 'entry', '', 'get', 'absint' );
			if ( $entry_key && $entry_key == $entry_id ) {
                $link = self::ajax_destroy(false, false, false);
                if ( ! empty($link) ) {
                    $new_link = '<div class="frm_message">'. $link .'</div>';
                    if ( empty($atts['label']) ) {
                        return;
                    }

                    if ( $link == __( 'Your entry was successfully deleted', 'formidable' ) ) {
                        return $new_link;
                    } else {
                        $link = $new_link;
                    }

                    unset($new_link);
                }
            }
        }

        $delete_link = wp_nonce_url(admin_url('admin-ajax.php') . '?action=frm_entries_destroy&entry='. $entry_id .'&redirect='. $atts['page_id'], 'frm_ajax', 'nonce');
        if ( empty($atts['label']) ) {
            $link .= $delete_link;
        } else {
            if ( empty($atts['title']) ) {
                $atts['title'] = $atts['label'];
            }
			$link .= '<a href="' . esc_url( $delete_link ) . '" class="' . esc_attr( $atts['class'] ) . '" data-frmconfirm="' . esc_attr( $atts['confirm'] ) . '" title="' . esc_attr( $atts['title'] ) . '">' . $atts['label'] . '</a>' . "\n";
        }

        return $link;
    }

	public static function get_field_value_shortcode( $sc_atts ) {
		$atts = shortcode_atts( array(
			'entry' => false, 'field_id' => false, 'user_id' => false,
			'ip' => false, 'show' => '', 'format' => '', 'return_array' => false,
			'default' => '',
		), $sc_atts);

		// Include all user-defined atts as well
		$atts = (array) $atts + (array) $sc_atts;

		// For reverse compatibility
		if ( isset( $atts['entry_id'] ) && ! $atts['entry'] ) {
			$atts['entry'] = $atts['entry_id'];
		}

		if ( ! $atts['field_id']  ) {
			return __( 'You are missing options in your shortcode. field_id is required.', 'formidable' );
		}

		$field = FrmField::getOne($atts['field_id']);
		if ( ! $field ) {
			return $atts['default'];
		}

		$entry = self::get_frm_field_value_entry( $field, $atts );

		if ( ! $entry ) {
			return $atts['default'];
		}

		$value = FrmProEntryMetaHelper::get_post_or_meta_value($entry, $field, $atts);
		$atts['type'] = $field->type;
		$atts['post_id'] = $entry->post_id;
		$atts['entry_id'] = $entry->id;
		if ( ! isset($atts['show_filename']) ) {
			$atts['show_filename'] = false;
		}

		if ( $field->type == 'file' && ! isset( $atts['html'] ) ) {
			// default to show the image instead of the url
			$atts['html'] = 1;
		}

		if ( ! empty( $atts['format'] ) || ( isset($atts['show']) && ! empty($atts['show']) ) ) {
			$value = FrmFieldsHelper::get_display_value($value, $field, $atts);
		} else {
			$value = FrmEntriesHelper::display_value( $value, $field, $atts);
		}

		return $value;
    }

	/**
	* Get entry object for frm_field_value shortcode
	* Uses user_id, entry, or ip atts to fetch the entry
	*
	* @since 2.0.13
	* @param object $field
	* @param array $atts
	* @return boolean|object $entry
	*/
	private static function get_frm_field_value_entry( $field, &$atts ){
		$query = array( 'form_id' => $field->form_id );
		if ( $atts['user_id'] ) {
			// make sure we are not getting entries for logged-out users
			$query['user_id'] = (int) FrmAppHelper::get_user_id_param( $atts['user_id'] );
			$query['user_id !'] = 0;
		}

		if ( $atts['entry'] ) {
			if ( ! is_numeric($atts['entry']) ) {
				$atts['entry'] = FrmAppHelper::simple_get( $atts['entry'], 'sanitize_title', $atts['entry'] );
			}

			if ( empty( $atts['entry'] ) ) {
				return;
			}

			if ( is_numeric( $atts['entry'] ) ) {
				$query[] = array( 'or' => 1, 'id' => $atts['entry'], 'parent_item_id' => $atts['entry'] );
			} else {
				$query[] = array( 'item_key' => $atts['entry'] );
			}
		}

		if ( $atts['ip'] ) {
			$query['ip'] = ( $atts['ip'] == true ) ? FrmAppHelper::get_ip_address() : $atts['ip'];
		}

		$entry = FrmDb::get_row( 'frm_items', $query, 'post_id, id', array( 'order_by' => 'created_at DESC' ) );

		return $entry;
	}

	public static function show_entry_shortcode( $atts ) {
		return FrmEntryFormat::show_entry( $atts );
	}

	/**
     * Alternate Row Color for Default HTML
     * @return string
     */
	public static function change_row_color() {
		global $frm_email_col;

        $bg_color = 'bg_color';
		if ( $frm_email_col ) {
		    $bg_color .= '_active';
			$frm_email_col = false;
		} else {
			$frm_email_col = true;
		}

        $bg_color = FrmStylesController::get_style_val($bg_color);
        $alt_color = 'background-color:#'. $bg_color .';';
		return $alt_color;
	}

	public static function maybe_set_cookie( $entry_id, $form_id ) {
        if ( defined('WP_IMPORTING') || defined('DOING_AJAX') ) {
            return;
        }

        if ( isset($_POST) && isset($_POST['frm_skip_cookie']) ) {
            self::set_cookie($entry_id, $form_id);
            return;
        }

        include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-entries/set_cookie.php');
    }

    /* AJAX */
    public static function ajax_set_cookie(){
        check_ajax_referer( 'frm_ajax', 'nonce' );
        self::set_cookie();
        wp_die();
    }

    public static function set_cookie( $entry_id = false, $form_id = false ) {
        if ( headers_sent() ) {
            return;
        }

        if ( ! apply_filters('frm_create_cookies', true) ) {
            return;
        }

        if ( ! $entry_id ) {
			$entry_id = FrmAppHelper::get_param( 'entry_id', '', 'get', 'absint' );
        }

        if ( ! $form_id ) {
			$form_id = FrmAppHelper::get_param( 'form_id', '', 'get', 'absint' );
        }

        $form = FrmForm::getOne($form_id);
        $expiration = isset($form->options['cookie_expiration']) ? ( (float) $form->options['cookie_expiration'] *60*60 ) : 30000000;
        $expiration = apply_filters('frm_cookie_expiration', $expiration, $form_id, $entry_id);
        setcookie('frm_form'.$form_id.'_' . COOKIEHASH, current_time('mysql', 1), time() + $expiration, COOKIEPATH, COOKIE_DOMAIN);
    }

	public static function ajax_create() {
		if ( ! FrmAppHelper::doing_ajax() || ! isset( $_POST['form_id'] ) ) {
			// normally, this function would be triggered with the wp_ajax hook, but we need it fired sooner
			return;
		}

		$allowed_actions = array( 'frm_entries_create', 'frm_entries_update' );
		if ( ! in_array( FrmAppHelper::get_post_param( 'action', '', 'sanitize_title' ), $allowed_actions ) ) {
			// allow ajax creating and updating
			return;
		}

        $form = FrmForm::getOne( (int) $_POST['form_id'] );
        if ( ! $form ) {
            echo false;
            wp_die();
        }

        $no_ajax_fields = array( 'file');
        $errors = FrmEntryValidate::validate( $_POST, $no_ajax_fields );

		if ( empty( $errors ) ) {
			if ( FrmProForm::is_ajax_on( $form ) ) {
                global $frm_vars;
                $frm_vars['ajax'] = true;
                $frm_vars['css_loaded'] = true;

				// don't load scripts if we are going backwards in the form
				$going_backwards = FrmProFormsHelper::going_to_prev( $form->id );

				// save the entry if there is not another page or when saving a draft
				if ( ( ! isset( $_POST[ 'frm_page_order_' . $form->id ] ) && ! $going_backwards ) || FrmProFormsHelper::saving_draft() ) {
                    $processed = true;
                    FrmEntriesController::process_entry($errors, true);
                }

                echo FrmFormsController::show_form($form->id);

				// trigger the footer scripts if there is a form to show
				if ( $errors || ! isset( $processed ) || ! empty( $frm_vars['forms_loaded'] ) ) {
					self::print_ajax_scripts( $going_backwards ? 'none' : '' );
                }
            } else {
                echo false;
            }
        }else{
            $obj = array();
			foreach ( $errors as $field => $error ) {
                $field_id = str_replace('field', '', $field);
                $obj[$field_id] = $error;
            }
            echo json_encode($obj);
        }

        wp_die();
    }

    public static function ajax_update(){
		_deprecated_function( __FUNCTION__, '2.0.11', 'FrmProEntriesController::ajax_create' );
		if ( ! $_POST || ! isset( $_POST['nonce'] ) ) {
			// make sure the request is posted since the front-end nonce may be cached
			wp_die();
		}

        return self::ajax_create();
    }

    public static function wp_ajax_destroy(){
        check_ajax_referer( 'frm_ajax', 'nonce' );

        $echo = true;
        if ( isset($_REQUEST['redirect']) ) {
            // don't echo if redirecting
            $echo = false;
        }
        self::ajax_destroy(false, true, $echo);

        if ( ! $echo ) {
            // redirect instead of loading a blank page
            wp_redirect( esc_url_raw( get_permalink( $_REQUEST['redirect'] ) ) );
            die();
        }

        wp_die();
    }

	public static function ajax_destroy( $form_id = false, $ajax = true, $echo = true ) {
        global $wpdb, $frm_vars;

		$entry_key = FrmAppHelper::get_param( 'entry', '', 'get', 'sanitize_title' );
        if ( ! $form_id ) {
			$form_id = FrmAppHelper::get_param( 'form_id', '', 'get', 'absint' );
        }

        if ( ! $entry_key ) {
            return;
        }

        if ( isset( $frm_vars['deleted_entries'] ) && is_array( $frm_vars['deleted_entries'] ) && in_array( $entry_key, $frm_vars['deleted_entries'] ) ) {
            return;
        }

        if ( is_numeric( $entry_key ) ) {
            $where = array( 'id' => $entry_key);
        } else {
            $where = array( 'item_key' => $entry_key);
        }

        $entry = FrmDb::get_row( $wpdb->prefix .'frm_items', $where, 'id, form_id, is_draft, user_id' );
        unset( $where );

        if ( ! $entry || ( $form_id && $entry->form_id != (int) $form_id ) ) {
            return;
        }

        $message = self::maybe_delete_entry($entry);
        if ( $message && ! is_numeric($message) ) {
            if ( $echo ) {
                echo '<div class="frm_message">'. $message .'</div>';
            }
            return;
        }

        if ( ! isset( $frm_vars['deleted_entries'] ) || empty( $frm_vars['deleted_entries'] ) ) {
            $frm_vars['deleted_entries'] = array();
        }
        $frm_vars['deleted_entries'][] = $entry->id;

        if ( $ajax && $echo ) {
            echo $message = 'success';
        } else if ( ! $ajax ) {
			$message = apply_filters('frm_delete_message', __( 'Your entry was successfully deleted', 'formidable' ), $entry);

            if ( $echo ) {
                echo '<div class="frm_message">'. $message .'</div>';
            }
        } else {
            $message = '';
        }

        return $message;
    }

	public static function maybe_delete_entry( $entry ) {
		FrmEntry::maybe_get_entry( $entry );

        if ( ! $entry || ! FrmProEntriesHelper::user_can_delete($entry) ) {
            $message = __( 'There was an error deleting that entry', 'formidable' );
            return $message;
        }

        $result = FrmEntry::destroy( $entry->id );
        return $result;
    }

    public static function edit_entry_ajax(){
        //check_ajax_referer( 'frm_ajax', 'nonce' );

		$id = FrmAppHelper::get_param( 'id', '', 'post', 'absint' );
		$entry_id = FrmAppHelper::get_param('entry_id', 0, 'post', 'absint' );
		$post_id = FrmAppHelper::get_param( 'post_id', 0, 'post', 'sanitize_title' );
		$fields = FrmAppHelper::get_param( 'fields', array(), 'post', 'sanitize_title' );
		$exclude_fields = FrmAppHelper::get_param( 'exclude_fields', array(), 'post', 'sanitize_title' );

        global $frm_vars;
		$frm_vars['footer_loaded'] = true;

		if ( $entry_id ) {
			$_GET['entry'] = $entry_id;
		}

        if ( $post_id && is_numeric($post_id) ) {
            global $post;
            if ( ! $post ) {
                $post = get_post($post_id);
            }
        }

        echo "<script type='text/javascript'>
/*<![CDATA[*/
jQuery(document).ready(function($){
$('#frm_form_" . esc_attr( $id ) . "_container .frm-show-form').submit(window.frmFrontForm.submitForm);
});
/*]]>*/
</script>";

        echo FrmFormsController::get_form_shortcode( compact( 'id', 'entry_id', 'fields', 'exclude_fields' ) );

		self::print_ajax_scripts( 'all' );

        wp_die();
    }

    public static function update_field_ajax(){
        //check_ajax_referer( 'frm_ajax', 'nonce' );

		$entry_id = FrmAppHelper::get_param( 'entry_id', 0, 'post', 'absint' );
		$field_id = FrmAppHelper::get_param( 'field_id', 0, 'post', 'sanitize_title' );
        $value = FrmAppHelper::get_param('value');

		FrmField::maybe_get_field( $field_id );
		if ( $field_id && FrmProEntriesHelper::user_can_edit( $entry_id, $field_id->form_id ) ) {
			$updated = FrmProEntryMeta::update_single_field( compact( 'entry_id', 'field_id', 'value' ) );
			echo $updated;
		}

        wp_die();
    }

    public static function send_email(){
        if ( current_user_can('frm_view_forms') || current_user_can('frm_edit_forms') || current_user_can('frm_edit_entries') ) {
            if ( FrmAppHelper::doing_ajax() ) {
                check_ajax_referer( 'frm_ajax', 'nonce' );
            }
			$entry_id = FrmAppHelper::get_param( 'entry_id', '', 'get', 'absint' );
			$form_id = FrmAppHelper::get_param( 'form_id', '', 'get', 'absint' );

            printf(__( 'Resent to %s', 'formidable' ), '');

            add_filter('frm_echo_emails', '__return_true');
            FrmFormActionsController::trigger_actions('create', $form_id, $entry_id, 'email');
        }else{
            _e( 'Resent to No one! You do not have permission', 'formidable' );
        }
        wp_die();
    }

	public static function redirect_url( $url ) {
        $url = str_replace( array( ' ', '[', ']', '|', '@'), array( '%20', '%5B', '%5D', '%7C', '%40'), $url);
        return $url;
    }

	public static function setup_edit_vars( $values ) {
        if ( ! isset($values['edit_value']) ) {
            $values['edit_value'] = ($_POST && isset($_POST['options']['edit_value'])) ? $_POST['options']['edit_value'] : __( 'Update', 'formidable' );
        }

        if ( ! isset($values['edit_msg']) ) {
            if ( $_POST && isset($_POST['options']['edit_msg']) ) {
                $values['edit_msg'] = $_POST['options']['edit_msg'];
            } else {
                $frmpro_settings = new FrmProSettings();
                $values['edit_msg'] = $frmpro_settings->edit_msg;
            }
        }

        return $values;
    }

	public static function allow_form_edit( $action, $form ) {
        _deprecated_function( __FUNCTION__, '2.0', 'FrmProEntriesHelper::allow_form_edit');
        return FrmProEntriesHelper::allow_form_edit($action, $form);
    }

	public static function email_value( $value, $meta, $entry ) {
        _deprecated_function( __FUNCTION__, '2.0', 'FrmProEntryMetaHelper::email_value');
        return FrmProEntryMetaHelper::email_value($value, $meta, $entry);
    }

    /* Trigger model actions */

	public static function create_post( $entry_id, $form_id ) {
        _deprecated_function( __FUNCTION__, '2.0', 'FrmProEntriesController::trigger_post');
		FrmProPost::create_post( $entry_id, $form_id );
    }

	public static function update_post( $entry_id, $form_id ) {
        _deprecated_function( __FUNCTION__, '2.0', 'FrmProEntriesController::trigger_post');
		FrmProEntry::create_post( $entry_id, $form_id, 'update' );
    }
}
