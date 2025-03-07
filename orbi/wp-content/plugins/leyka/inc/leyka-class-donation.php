<?php if( !defined('WPINC') ) die;

/**
 * Leyka Donation History
 **/

class Leyka_Donation_Management {
	
	private static $_instance = null;

	public static $post_type = 'leyka_donation';

	private function __construct() {

//        add_action('quick_edit_custom_box', array($this, 'display_quickedit_box'), 10, 2);

        add_filter('post_row_actions', array($this, 'row_actions'), 10, 2);

        add_action('restrict_manage_posts', array($this, 'manage_filters'));
        add_action('pre_get_posts', array($this, 'do_filtering'));

        add_action('add_meta_boxes', array($this, 'set_metaboxes'));
        add_action('save_post', array($this, 'save_donation_data'));

		add_filter('manage_'.self::$post_type.'_posts_columns', array($this, 'manage_columns_names'));
		add_action('manage_'.self::$post_type.'_posts_custom_column', array($this, 'manage_columns_content'), 2, 2);

        add_filter('manage_edit-'.self::$post_type.'_sortable_columns', array($this, 'manage_sortable_columns'));
        add_filter('request', array($this, 'do_column_sorting'));

        /** Donation status transitions */
//        add_action('new_to_submitted', array($this, 'new_donation_added'));

        add_action('transition_post_status',  array($this, 'donation_status_changed'), 10, 3);

        // Leyka_donation status changed to "funded":
//        add_action('funded_'.self::$post_type, array($this, 'donation_funded'), 10, 2);

        add_action('wp_ajax_leyka_send_donor_email', array($this, 'ajax_send_donor_email'));
	}

    public function set_admin_messages($messages) {

        global $post;

        $messages[self::$post_type] = array(
            0 => '', // Unused. Messages start at index 1.
            1 => __('Donation updated.', 'leyka'),
            2 => __('Field updated.', 'leyka'),
            3 => __('Field deleted.', 'leyka'),
            4 => __('Donation updated.', 'leyka'),
            /* translators: %s: date and time of the revision */
            5 => isset($_GET['revision']) ?
                sprintf(
                    __('Donation restored to revision from %s', 'leyka'),
                    wp_post_revision_title((int)$_GET['revision'], false)
                ) : false,
            6 => __('Donation published.', 'leyka'),
            7 => __('Donation saved.', 'leyka'),
            8 => __('Donation submitted.', 'leyka'),
            9 => sprintf(
                __('Donation scheduled for: <strong>%1$s</strong>.', 'leyka'),
                // translators: Publish box date format, see http://php.net/date
                date_i18n(__( 'M j, Y @ G:i'), strtotime($post->post_date))
            ),
            10 => __('Donation draft updated.', 'leyka'),
        );

        return $messages;
    }

    public function row_actions($actions, $donation) {

        global $current_screen;

        if( !$current_screen || !is_object($current_screen) || $current_screen->post_type != self::$post_type ) {
            return $actions;
        }

		if(current_user_can('edit_donation', $donation->ID)) {
			$actions['edit'] = '<a href="'.get_edit_post_link($donation->ID, 1).'">'.__('Edit').'</a>';
		}

        unset($actions['view']);
        unset($actions['inline hide-if-no-js']);

        return $actions;
    }

    public function donation_status_changed($new, $old, WP_Post $donation) {

        if($new == $old || $donation->post_type != self::$post_type) {
            return;
        }

        if($old == 'new' && $new != 'trash') {
            $this->new_donation_added($donation);
        } elseif($new == 'funded' || $old == 'funded') {

            $donation = new Leyka_Donation($donation);

            $campaign = new Leyka_Campaign($donation->campaign_id);
            $campaign->update_total_funded_amount($donation);

            if($campaign->target) {

                if($campaign->total_funded >= $campaign->target) {
                    $campaign->target_state = 'is_reached';
                } elseif($campaign->target_state != 'in_process') {
                    $campaign->target_state = 'in_process';
                }
            }
        }
    }

    public function manage_filters() {

        global $pagenow;

        if(
            $pagenow == 'edit.php' &&
            isset($_GET['post_type']) &&
            $_GET['post_type'] == self::$post_type &&
            current_user_can('leyka_manage_donations')
        ) {?>

        <label for="payment-type-select"></label>
        <select id="payment-type-select" name="payment_type">
            <option value="" <?php echo empty($_GET['payment_type']) ? 'selected="selected"' : '';?>><?php _e('Select a payment type', 'leyka');?></option>

            <?php foreach(leyka_get_payment_types_list() as $payment_type => $label) {?>
                <option value="<?php echo $payment_type;?>" <?php echo !empty($_GET['payment_type']) && $_GET['payment_type'] == $payment_type ? 'selected="selected"' : '';?>><?php echo $label;?></option>
            <?php }?>
        </select>

        <label for="gateway-pm-select"></label>
        <select id="gateway-pm-select" name="gateway_pm">
            <option value="" <?php echo empty($_GET['gateway_pm']) ? '' : 'selected="selected"';?>><?php _e('Select a gateway or a payment method', 'leyka');?></option>
        <?php $gw_pm_list = array();
        foreach(leyka_get_gateways() as $gateway) {

            /** @var Leyka_Gateway $gateway */
            $pm_list = $gateway->get_payment_methods();
            if($pm_list)
                $gw_pm_list[] = array('gateway' => $gateway, 'pm_list' => $pm_list);
        }
        $gw_pm_list = apply_filters('leyka_donations_list_gw_pm_filter', $gw_pm_list);

        foreach($gw_pm_list as $element) {?>

            <option value="<?php echo 'gateway__'.$element['gateway']->id;?>" <?php echo !empty($_GET['gateway_pm']) && $_GET['gateway_pm'] == 'gateway__'.$element['gateway']->id ? 'selected="selected"' : '';?>><?php echo $element['gateway']->name;?></option>

            <?php foreach($element['pm_list'] as $pm) {?>

                <option value="<?php echo 'pm__'.$pm->id;?>" <?php echo !empty($_GET['gateway_pm']) && $_GET['gateway_pm'] == 'pm__'.$pm->id ? 'selected="selected"' : '';?>><?php echo '&nbsp;&nbsp;&nbsp;&nbsp;'.$pm->name;?></option>
            <?php }
        }?>

        </select>

        <?php $campaign_title = '';
            if( !empty($_GET['campaign']) && (int)$_GET['campaign'] > 0) {
                $campaign = get_post((int)$_GET['campaign']);
                if($campaign)
                    $campaign_title = $campaign->post_title;
            }?>

        <label for="campaign-select"></label>
        <input id="campaign-select"
               type="text"
               data-nonce="<?php echo wp_create_nonce('leyka_get_campaigns_list_nonce');?>"
               placeholder="<?php _e('Please, enter campaign title', 'leyka');?>"
               value="<?php echo $campaign_title;?>"
            />
        <input id="campaign-id" type="hidden" name="campaign" value="<?php echo !empty($_GET['campaign']) ? (int)$_GET['campaign'] : '';?>" />

        <?php }
    }

    public function do_filtering(WP_Query $query) {

        global $pagenow;

        if(
            $pagenow == 'edit.php' && !empty($_GET['post_type']) && $_GET['post_type'] == self::$post_type &&
            is_admin() && $query->is_main_query()
        ) {
            $meta_query = array('relation' => 'AND');

            if( !empty($_REQUEST['campaign']) )
                $meta_query[] = array('key' => 'leyka_campaign_id', 'value' => (int)$_REQUEST['campaign']);

            if( !empty($_REQUEST['payment_type']) )
                $meta_query[] = array('key' => 'leyka_payment_type', 'value' => $_REQUEST['payment_type']);

            if( !empty($_REQUEST['gateway_pm']) ) {

                if(strpos($_REQUEST['gateway_pm'], 'gateway__') !== false)
                    $meta_query[] = array(
                        'key' => 'leyka_gateway', 'value' => str_replace('gateway__', '', $_REQUEST['gateway_pm'])
                    );

                elseif(strpos($_REQUEST['gateway_pm'], 'pm__') !== false)
                    $meta_query[] = array(
                        'key' => 'leyka_payment_method', 'value' => str_replace('pm__', '', $_REQUEST['gateway_pm'])
                    );
            }

            //...

            if(count($meta_query) > 1)
                $query->set('meta_query', $meta_query);
        }
    }

    public function new_donation_added(WP_Post $donation) {

        if($donation->post_type != Leyka_Donation_Management::$post_type)
            return;
    }

    public static function send_all_emails($donation) {

        $donation = get_validated_donation($donation);

        if( !$donation ) {
            return false;
        }


        Leyka_Donation_Management::send_donor_thanking_email($donation);

        if(leyka_options()->opt('donations_managers_emails')) {

            if(
                ($donation->payment_type == 'single' && leyka_options()->opt('notify_donations_managers')) ||
                ($donation->payment_type == 'rebill' && leyka_options()->opt('notify_managers_on_recurrents'))
            ) {
                Leyka_Donation_Management::send_managers_notifications($donation);
            }
        }

        return true;
    }

    /** Ajax handler method */
    public function ajax_send_donor_email() {

        if(empty($_POST['donation_id']) || !wp_verify_nonce($_POST['nonce'], 'leyka_donor_email'))
            return;

        $donation = new Leyka_Donation((int)$_POST['donation_id']);

        if($donation && Leyka_Donation_Management::send_donor_thanking_email($donation)) {
            die(__('Grateful email has been sent to the donor', 'leyka'));
        } else {
            die(__("For some reason, we can't send this email right now :( Please, try again later.", 'leyka'));
        }
    }

    public static function send_donor_thanking_email($donation) {

        $donation = get_validated_donation($donation);

        $donor_email = $donation->donor_email;
        if( !$donor_email )
            $donor_email = leyka_pf_get_donor_email_value();

        if( !$donation || !$donor_email || $donation->donor_email_date )
            return false;

        if( !function_exists('set_html_content_type') ) {
            function set_html_content_type(){ return 'text/html'; }
        }
        add_filter('wp_mail_content_type', 'set_html_content_type');

        $campaign = new Leyka_Campaign($donation->campaign_id);

        $email_text = $donation->payment_type == 'rebill' ?
            leyka_options()->opt('email_recurrents_thanks_text') : leyka_options()->opt('email_thanks_text');
        $email_title = $donation->payment_type == 'rebill' ?
            leyka_options()->opt('email_recurrents_thanks_title') : leyka_options()->opt('email_thanks_title');

        $res = wp_mail(
            $donor_email,
            apply_filters('leyka_email_thanks_title', $email_title, $donation, $campaign),
            wpautop(str_replace(
                array(
                    '#SITE_NAME#',
                    '#SITE_EMAIL#',
                    '#ORG_NAME#',
                    '#DONATION_ID#',
                    '#DONOR_NAME#',
                    '#PAYMENT_METHOD_NAME#',
                    '#CAMPAIGN_NAME#',
                    '#PURPOSE#',
                    '#SUM#',
                    '#DATE#',
                ),
                array(
                    get_bloginfo('name'),
                    get_bloginfo('admin_email'),
                    leyka_options()->opt('org_full_name'),
                    $donation->id,
                    $donation->donor_name,
                    $donation->payment_method_label,
                    $campaign->title,
                    $campaign->payment_title,
                    $donation->amount.' '.$donation->currency_label,
                    $donation->date,
                ),
                apply_filters(
                    'leyka_email_thanks_text',
                    $email_text,
                    $donation, $campaign
                )
            )),
            array('From: '.apply_filters(
                'leyka_email_from_name',
                leyka_options()->opt_safe('email_from_name'),
                $donation,
                $campaign
            ).' <'.leyka_options()->opt_safe('email_from').'>',)
        );

        // Reset content-type to avoid conflicts (http://core.trac.wordpress.org/ticket/23578):
        remove_filter('wp_mail_content_type', 'set_html_content_type');

        if($res) {
            update_post_meta($donation->id, '_leyka_donor_email_date', time());

            return true;
        } else
            return false;
    }

    public static function send_managers_notifications($donation) {

        if( !leyka_options()->opt('donations_managers_emails') ) {
            return false;
        }

        $donation = get_validated_donation($donation);

        if( !$donation || $donation->managers_emails_date ) {
            return false;
        }

        if( !function_exists('set_html_content_type') ) {
            function set_html_content_type(){ return 'text/html'; }
        }
        add_filter('wp_mail_content_type', 'set_html_content_type');

        $res = true;
        foreach(explode(',', leyka_options()->opt('leyka_donations_managers_emails')) as $email) {

            $email = trim($email);

            if( !$email ) {
                continue;
            }

            $campaign = new Leyka_Campaign($donation->campaign_id);
            if( !wp_mail(
                $email,
                apply_filters(
                    'leyka_email_notification_title',
                    leyka_options()->opt('email_notification_title'),
                    $donation, $campaign
                ),
                wpautop(str_replace(
                    array(
                        '#SITE_NAME#',
                        '#ORG_NAME#',
                        '#DONATION_ID#',
                        '#DONOR_NAME#',
                        '#PAYMENT_METHOD_NAME#',
                        '#CAMPAIGN_NAME#',
                        '#PURPOSE#',
                        '#SUM#',
                        '#DATE#',
                    ),
                    array(
                        get_bloginfo('name'),
                        leyka_options()->opt('org_full_name'),
                        $donation->id,
                        $donation->donor_name,
                        $donation->payment_method_label,
                        $campaign->title,
                        $campaign->payment_title,
                        $donation->amount.' '.$donation->currency_label,
                        $donation->date,
                    ),
                    apply_filters(
                        'leyka_email_notification_text',
                        leyka_options()->opt('email_notification_text'),
                        $donation, $campaign
                    )
                )),
                array('From: '.apply_filters(
                    'leyka_email_from_name',
                    leyka_options()->opt_safe('email_from_name'),
                    $donation,
                    $campaign
                ).' <'.leyka_options()->opt_safe('email_from').'>',)
            ) )
                $res &= false;
        }

        if($res)
            update_post_meta($donation->id, '_leyka_managers_emails_date', time());

        // Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
        remove_filter('wp_mail_content_type', 'set_html_content_type');
        return true;
    }

	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if(null == self::$_instance) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	/** Donation metaboxes */
    public function set_metaboxes(){

		remove_meta_box('submitdiv', self::$post_type, 'side'); // Remove default status/publish metabox

        $curr_page = get_current_screen();

        if($curr_page->action == 'add') { // New donation page

            add_meta_box(self::$post_type.'_new_data', __('New donation data', 'leyka'), array($this, 'new_donation_data_metabox'), self::$post_type, 'normal', 'high');
            add_meta_box(self::$post_type.'_status', __('Donation status', 'leyka'), array($this, 'donation_status_metabox'), self::$post_type, 'side', 'high');

        } else { // View/edit donation page

            add_meta_box(self::$post_type.'_data', __('Donation data', 'leyka'), array($this, 'donation_data_metabox'), self::$post_type, 'normal', 'high');
            add_meta_box(self::$post_type.'_status', __('Donation status', 'leyka'), array($this, 'donation_status_metabox'), self::$post_type, 'side', 'high');
            add_meta_box(self::$post_type.'_emails_status', __('Emails status', 'leyka'), array($this, 'emails_status_metabox'), self::$post_type, 'normal', 'high');
            add_meta_box(self::$post_type.'_gateway_response', __('Gateway responses text', 'leyka'), array($this, 'gateway_response_metabox'), self::$post_type, 'normal', 'low');
//        add_meta_box(self::$post_type.'_recurrent_cancel', __('Cancel recurrent donations', 'leyka'), array($this, 'recurrent_cancel_metabox'), self::$post_type, 'normal', 'low');
        }
	}

    public function new_donation_data_metabox($donation) {

        $campaign_id = empty($_GET['campaign_id']) ? '' : (int)$_GET['campaign_id'];
        $campaign = new Leyka_Campaign($campaign_id);?>

	<fieldset class="leyka-set campaign">
		<legend><?php _e('Campaign Data', 'leyka');?></legend>

        <div class="leyka-ddata-string">
			<label for="campaign-select"><?php _e('Campaign', 'leyka');?>:</label>
			<div class="leyka-ddata-field">

				<input id="campaign-select"
					   type="text"
                       value="<?php echo $campaign_id ? $campaign->title : '';?>"
					   data-nonce="<?php echo wp_create_nonce('leyka_get_campaigns_list_nonce');?>"
					   placeholder="<?php _e('Please, enter campaign title', 'leyka');?>"
					/>
				<input id="campaign-id" type="hidden" name="campaign-id" value="<?php echo $campaign_id;?>" />
				<div id="campaign_id-error" class="field-error"></div>

			</div>
        </div>

        <div class="leyka-ddata-string">
            <label><?php _e('Donation purpose', 'leyka');?>:</label>
			<div class="leyka-ddata-field">
				<div id="new-donation-purpose" class="text-line"><?php echo $campaign_id ? $campaign->payment_title : '';?></div>
			</div>
        </div>

	</fieldset>

	<fieldset class="leyka-set donor">
		<legend><?php _e('Donor Data', 'leyka');?></legend>
		
        <div class="leyka-ddata-string">
            <label for="donor-name"><?php _e('Name', 'leyka');?>:</label>
            <div class="leyka-ddata-field">
                <input type="text" id="donor-name" name="donor-name" placeholder="<?php _e("Enter donor's name, or leave it empty for anonymous donation", 'leyka');?>" value="" />
			</div>
		</div>
		
		<div class="leyka-ddata-string">
            <label for="donor-email"><?php _e('Email', 'leyka');?>:</label>
			<div class="leyka-ddata-field">
                <input type="text" id="donor-email" name="donor-email" placeholder="<?php _e("Enter donor's email", 'leyka');?>" value="" />
                <div id="donor_email-error" class="field-error"></div>
            </div>
        </div>
	</fieldset>

	<fieldset class="leyka-set donation">
		<legend><?php _e('Donation Data', 'leyka');?></legend>

        <div class="leyka-ddata-string">
            <label for="donation-amount"><?php _e('Amount', 'leyka');?>:</label>

			<div class="leyka-ddata-field">
				<input type="text" id="donation-amount" name="donation-amount" placeholder="<?php _e("Enter a donation's amount", 'leyka');?>" value="" /> <?php echo leyka_options()->opt_safe('currency_rur_label');?><br>
				<small class='field-help howto'><?php _e('Amount could be negative for correctional entry.', 'leyka');?></small>
				<div id="donation_amount-error" class="field-error"></div>

<!--            <select id="new-donation-currency" name="donation_currency">-->
<!--                <option value="rur" selected="selected">--><?php //echo leyka_options()->opt_safe('currency_rur_label');?><!--</option>-->
<!--                <option value="usd">--><?php //echo leyka_options()->opt_safe('currency_usd_label');?><!--</option>-->
<!--                <option value="eur">--><?php //echo leyka_options()->opt_safe('currency_eur_label');?><!--</option>-->
<!--            </select>-->
			</div>
        </div>

        <div class="leyka-ddata-string">
            <label for="donation-pm"><?php _e('Payment method', 'leyka');?>:</label>

			<div class="leyka-ddata-field">
            <select id="donation-pm" name="donation-pm">
                <option value="" selected="selected"><?php _e('Select a payment method', 'leyka');?></option>
                <?php foreach(leyka_get_gateways() as $gateway) {

                    /** @var Leyka_Gateway $gateway */
                    $pm_list = $gateway->get_payment_methods();
                    if($pm_list) {?>

                        <optgroup label="<?php echo $gateway->name;?>">

                        <?php foreach($pm_list as $pm) {?>
                            <option value="<?php echo $pm->full_id;?>"><?php echo $pm->name;?></option>
                        <?php }?>
                        </optgroup>

                    <?php }?>

                <?php }?>
                <option value="custom"><?php _e('Custom payment info', 'leyka');?></option>
            </select>

            <input type="text" id="custom-payment-info" name="custom-payment-info" placeholder="<?php _e('Enter some info about source of a new donation', 'leyka');?>" style="display: none;" value="" />

            <div id="donation_pm-error" class="field-error"></div>
			</div>

        </div>

        <input type="hidden" id="payment-type-hidden" name="payment-type" value="correction" />

        <?php /** @todo Maybe, display divs only for "active" gateways? I.e. those with currently active PMs. */
        foreach(leyka_get_gateways() as $gateway) {?>
            <div id="<?php echo $gateway->id;?>-fields" class="leyka-ddata-string gateway-fields" style="display: none;">
                <?php $gateway->display_donation_specific_data_fields();?>
            </div>
        <?php }?>

        <div class="leyka-ddata-string">
            <label for="donation-date-view"><?php _e('Donation date', 'leyka');?>:</label>

            <div class="leyka-ddata-field">
                <input type="text" id="donation-date-view" value="<?php echo date(get_option('date_format'));?>" />
                <input type="hidden" id="donation-date" name="donation_date" value="<?php echo date('Y-m-d');?>" />
            </div>
        </div>

	</fieldset>
    <?php }

    public function donation_data_metabox(WP_Post $donation) {

        $donation = new Leyka_Donation($donation);
        $campaign = new Leyka_Campaign($donation->campaign_id);?>

	<fieldset class="leyka-set campaign">
		<legend><?php _e('Campaign Data', 'leyka');?></legend>

        <div class="leyka-ddata-string">			
			<label><?php _e('Campaign', 'leyka');?>:</label>
			<div class="leyka-ddata-field">
			<?php if($campaign->id && $campaign->status == 'publish') { ?>
			<span class="text-line">
            <span class="campaign-name"><?php echo $campaign->title;?></span> <span class="campaign-actions"><a href="<?php echo admin_url('/post.php?action=edit&post='.$campaign->id);?>"><?php _e('Edit campaign', 'leyka');?></a> <a href="<?php echo $campaign->url;?>" target="_blank"><?php _e('Preview campaign', 'leyka');?></a></span></span>

			<?php } else {
				echo '<span class="text-line">'.__('the campaign has been removed or drafted', 'leyka').'</span>';
			}?>
			</div>
		</div>

		<div class="leyka-ddata-string">
			<label><?php _e('Donation purpose', 'leyka');?>:</label>
			<div class="leyka-ddata-field"><span class="text-line">
			<?php echo $campaign->id ? $campaign->payment_title : $donation->title;?>
			</span></div>
        </div>

		<div class="set-action">
            <div id="campaign-select-trigger" class="button"><?php _e('Connect this donation to another campaign', 'leyka');?></div>

            <div id="campaign-select-fields" style="display: none;">
                <label for="campaign-select"></label>
                <input id="campaign-select"
                       type="text"
                       data-nonce="<?php echo wp_create_nonce('leyka_get_campaigns_list_nonce');?>"
                       placeholder="<?php _e('Please, enter campaign title', 'leyka');?>"
                       value="<?php echo htmlentities($campaign->title, ENT_COMPAT, 'UTF-8');?>"
                    />
                <input id="campaign-id" type="hidden" name="campaign-id" value="<?php echo $campaign->id;?>" />
                <div id="cancel-campaign-select" class="button"><?php _e('Cancel', 'leyka');?></div>
            </div>
		</div> <!-- .set-action -->
        
	</fieldset>
	
	<fieldset class="leyka-set donor">
		<legend><?php _e('Donor Data', 'leyka');?></legend>
		
		<div class="leyka-ddata-string">
            <label for="donor-name"><?php _e('Name', 'leyka');?>:</label>
			<div class="leyka-ddata-field">

            <?php if($donation->type == 'correction') {?>

                <input type="text" id="donor-name" name="donor-name" placeholder="<?php _e("Enter donor's name, or leave it empty for anonymous donation", 'leyka');?>" value="<?php echo $donation->donor_name;?>" />
            <?php } else {?>

                <span class="fake-input"><?php echo $donation->donor_name ? $donation->donor_name : __('Anonymous', 'leyka');?></span>
            <?php }?>

            </div>
        </div>
			
		<div class="leyka-ddata-string">
            <label for="donor-email"><?php _e('Email', 'leyka');?>:</label>
			<div class="leyka-ddata-field">
            <?php if($donation->type == 'correction') {?>

                <input type="text" id="donor-email" name="donor-email" placeholder="<?php _e("Enter donor's email", 'leyka');?>" value="<?php echo $donation->donor_email;?>" />
                <div id="donor_email-error" class="field-error"></div>

            <?php } else {?>

                <span class="fake-input"><?php echo $donation->donor_email ? $donation->donor_email : '&ndash;';?></span>
            <?php }?>
            </div>
        </div>
			
	</fieldset>
	
	<fieldset class="leyka-set donation">
		<legend><?php _e('Donation Data', 'leyka');?></legend>
        
        <div class="leyka-ddata-string">
            <label><?php _e('Amount', 'leyka');?>:</label>
			<div class="leyka-ddata-field">
            <?php if($donation->type == 'correction') {?>

                <input type="text" id="donation-amount" name="donation-amount" placeholder="<?php _e("Enter a donation's amount", 'leyka');?>" value="<?php echo $donation->amount;?>" /> <?php echo $donation->currency_label;?>

                <div id="donation_amount-error" class="field-error"></div>

            <?php } else {?>

                <span class="fake-input"><?php echo $donation->amount ? $donation->amount.' '.$donation->currency_label : '';?></span>
            <?php }?>
            </div>
        </div>

        <div class="leyka-ddata-string">
            <label><?php _e('Payment method', 'leyka');?>:</label>
			<div class="leyka-ddata-field">
            <?php if($donation->type == 'correction') {?>

                <select id="donation-pm" name="donation-pm">
                    <option value="" selected="selected"><?php _e('Select a payment method', 'leyka');?></option>
                    <?php foreach(leyka_get_gateways() as $gateway) {

                        /** @var Leyka_Gateway $gateway */
                        $pm_list = $gateway->get_payment_methods();
                        if($pm_list) {?>

                            <optgroup label="<?php echo $gateway->name;?>">
                            <?php foreach($pm_list as $pm) {?>
                                <option value="<?php echo $pm->full_id;?>" <?php echo $donation->gateway_id == $gateway->id && $donation->pm_id == $pm->id ? 'selected="selected"' : '';?>><?php echo $pm->name;?></option>
                            <?php }?>
                            </optgroup>

                        <?php }?>

                    <?php }?>
                    <option value="custom" <?php echo $donation->gw_id == '' && $donation->pm_id ? 'selected="selected"' : '';?>><?php _e('Custom payment info', 'leyka');?></option>
                </select>

                <input type="text" id="custom-payment-info" name="custom-payment-info" placeholder="<?php _e('Enter some info about source of a new donation', 'leyka');?>" <?php echo $donation->gw_id == '' && $donation->pm_id ? '' : 'style="display: none;"';?> value="<?php echo $donation->gw_id == '' ? $donation->pm_id : '';?>" />

            <?php } else {?>

                <span class="fake-input">
                <?php $pm = leyka_get_pm_by_id($donation->payment_method);
                $gateway = leyka_get_gateway_by_id($donation->gateway_id);

                echo ($pm ? $pm->label : __('Unknown payment method', 'leyka'))
                    .' ('.($gateway ? $gateway->label : __('unknown gateway', 'leyka')).')';
                ?>
			    </span>
            <?php }?>
            </div>
        </div>

        <div class="leyka-ddata-string">
            <?php leyka_get_gateway_by_id($donation->gateway_id)->display_donation_specific_data_fields($donation);?>
        </div>

        <div class="leyka-ddata-string">
            <label><?php _e('Payment type', 'leyka');?>:</label>
			<div class="leyka-ddata-field">
                <span class="fake-input"><?php echo leyka_get_payment_type_label($donation->payment_type); // "single", "rebill", "correction" ?></span>
            </div>
        </div>
        <?php if($donation->init_recurring_donation_id) {?>
        <div class="leyka-ddata-string">
            <label><?php _e('Initial donation of the recurring subscription', 'leyka');?>:</label>
            <div class="leyka-ddata-field">
                <a href="<?php echo admin_url('post.php?post='.$donation->init_recurring_donation_id.'&action=edit');?>">#<?php echo $donation->init_recurring_donation_id;?></a></span>
            </div>
        </div>
        <?php }?>

        <div class="leyka-ddata-string">
            <label for="donation-date-view"><?php _e('Donation date', 'leyka');?>:</label>
			<div class="leyka-ddata-field">
            <?php if($donation->type == 'correction') {?>

                <input type="text" id="donation-date-view" value="<?php echo date(get_option('date_format'), $donation->date_timestamp);?>" />
                <input type="hidden" id="donation-date" name="donation_date" value="<?php echo date('Y-m-d', $donation->date_timestamp);?>" />
            <?php } else {?>

                <span class="fake-input"><?php echo $donation->date;?></span>
            <?php }?>
            </div>
        </div>
	</fieldset>

	<?php }

    public function donation_status_metabox($donation) {

        wp_nonce_field('donation_status_metabox', '_donation_edit_nonce');

        $is_adding_page = get_current_screen()->action == 'add';?>

        <div class="leyka-status-section select">
            <label for="donation-status"><?php _e('Status', 'leyka');?></label>
            <select id="donation-status" name="donation_status">
                <?php foreach(leyka_get_donation_status_list() as $status => $label) {
                    if($status == 'trash')
                        continue;?>
                <option value="<?php echo $status;?>" <?php echo $donation->post_status == $status || ($is_adding_page && $status == 'funded') ? 'selected' : '';?>><?php echo $label;?></option>
                <?php }?>
            </select>
		</div>

        <div class="leyka-status-section actions">
            <?php if(current_user_can('delete_post', $donation->ID) && !$is_adding_page) {?>
				<div class="delete-action">
                <a class="submitdelete deletion" href="<?php echo get_delete_post_link($donation->ID); ?>"><?php echo !EMPTY_TRASH_DAYS ? __('Delete Permanently') : __('Move to Trash');?></a>
				</div>
            <?php }?>

            <?php if(current_user_can(get_post_type_object(self::$post_type)->cap->publish_posts)) {?>
				<div class="save-action">
			    <input name="original_funded" type="hidden" id="original_funded" value="<?php esc_attr_e(__('Update', 'leyka'));?>" />
                <?php submit_button(
                    $is_adding_page ? __('Add the donation', 'leyka') : __('Update', 'leyka'),
                    'primary button-large', 'funded', false,
                    array('accesskey' => 'p', 'data-is-new-donation' => $is_adding_page ? 1 : 0)
                );?>
				</div>
            <?php }?>
        </div>

        <div class="leyka-status-section log">
            <?php $status_log = get_post_meta($donation->ID, '_status_log', true);
            if($status_log) {?>
                <?php $last_status = end($status_log);
                echo str_replace(
                    array('%status', '%date'),
                    array('<i>'.$this->get_status_labels($last_status['status']).'</i>', '<time>'.date(get_option('date_format').', H:i', $last_status['date']).'</time>'),
                    '<div class="leyka-ddata-string last-log">'.__('Last status change: to&nbsp;%status (at&nbsp;%date)', 'leyka').'</div>'
                );?>
                <div id="donation-status-log-toggle"><?php _e('Show/hide full status history', 'leyka');?></div>
                <ul id="donation-status-log" style="display: none;">
                    <?php for($i=0; $i<count($status_log); $i++) {?>
                    <li><?php echo str_replace(
                        array('%status', '%date'),
                        array('<i>'.$this->get_status_labels($status_log[$i]['status']).'</i>','<time>'.date(get_option('date_format').', H:i', $status_log[$i]['date']).'</time>'),
                        __('%date - %status', 'leyka')
                    );}?></li>
                </ul>
            <?php } else
                echo '<div class="leyka-ddata-string last-log">'.__('Last status change: none logged', 'leyka').'</div>';
            ?>
        </div>
	<?php }

    public function emails_status_metabox($donation) {

        $donor_thanks_date = get_post_meta($donation->ID, '_leyka_donor_email_date', true);
        $manager_notification_date = get_post_meta($donation->ID, '_leyka_managers_emails_date', true);
		
		if($donor_thanks_date) {
			$txt = str_replace(
                '%s',
                '<time>'.date(get_option('date_format').', H:i</time>', $donor_thanks_date).'</time>',
                __('Grateful email to the donor has been sent (at %s)', 'leyka')
            );?>

			<div class="leyka-ddata-string donor has-thanks"><?php echo $txt;?></div>

		<?php } else {?>

			<div class="leyka-ddata-string donor no-thanks" data-donation-id="<?php echo $donation->ID;?>">
				<?php echo sprintf(
                    __("Grateful email hasn't been sent %s", 'leyka'),
                    "<div class='send-donor-thanks'>".__('(send it now)', 'leyka')."</div>"
                );?>

				<?php wp_nonce_field('leyka_donor_email', '_leyka_donor_email_nonce', false, true); ?>
			</div>
		<?php }

        echo $manager_notification_date ?
            str_replace(
                '%s',
                '<time>'.date(get_option('date_format').', H:i', $manager_notification_date).'</time>',
                __('Donation managers notifications has been sended (at %s)', 'leyka')
            ) :
            '<div class="leyka-ddata-string manager no-thanks">'.__("Donation managers' notification emails hasn't been sent", 'leyka').'</div>';
    }

    /**
     * @param $donation WP_Post
     */
    public function gateway_response_metabox($donation) { $donation = new Leyka_Donation($donation);?>
        
        <div>
            <?php
            if( !$donation->gateway_response_formatted )
                _e('No gateway response has been received', 'leyka');
            else {

                foreach($donation->gateway_response_formatted as $name => $value) {?>

                <div class="leyka-ddata-string">
                    <span class="label"><?php echo $name;?></span> <?php echo mb_strtolower($value);?>
                </div>

            <?php }
            } ?>
        </div>
    <?php }

    public function recurrent_cancel_metabox($donation) {

        /** @todo Uncomment this metabox in constructor when work on recurrents cancelling will begin. */

        $donation = new Leyka_Donation($donation);

        if($donation->payment_type != 'rebill' || !function_exists('curl_init')) {?>
            <div id="hide-recurrent-metabox"></div>
        <?php return; } else {

            $init_recurrent_donation = Leyka_Donation::get_init_recurrent_donation($donation);
            if($init_recurrent_donation->recurrents_cancelled) {?>

            <div class="">
                <?php print_r(
                    __('Recurrent donations subscription was cancelled at %s', 'leyka'),
                    date(get_option('date_format').', H:i', $init_recurrent_donation->recurrents_cancel_date)
                );?>
            </div>

            <?php }
        }?>

        <div class="recurrent-cancel" data-donation-id="<?php echo $donation->id;?>" data-nonce="<?php echo wp_create_nonce('leyka_recurrent_cancel');?>" onclick="return confirm('<?php _e("You are about to cancel all future donations on this recurrent subscribe for this donor!\\n\\nDo you really want to do it?", "leyka");?>');"><?php _e('Cancel recurrent donations of this donor', 'leyka');?></div>

        <div id="ajax-processing" style="display: none;">
            <img src="<?php echo LEYKA_PLUGIN_BASE_URL.'/img/ajax-loader-h.gif';?>" /> <?php _e('Recurrent cancelling in progress...', 'leyka');?>
        </div>
        <div id="ajax-response" style="display: none;"></div>
        <div id="recurrent-cancel-retry" style="display: none;"><?php _e('Try again', 'leyka');?></div>
    <?php }

	/**
     * Donations table columns.
     * @param array $columns An array of id => name pairs.
     * @return array
     */
	function manage_columns_names($columns) {

		$unsort = $columns;
		$columns = array();

		if(isset($unsort['cb'])){
			$columns['cb'] = $unsort['cb'];
			unset($unsort['cb']);
		}

		$columns['ID'] = 'ID';

		if(isset($unsort['title'])) {
			$columns['title'] = __('Campaign', 'leyka');
			unset($unsort['title']);
		}

        unset($unsort['date']);

		$columns['donor'] = __('Donor', 'leyka');
		$columns['amount'] = __('Amount', 'leyka');
		$columns['method'] = __('Method', 'leyka');
        $columns['donation_date'] = __('Donation date', 'leyka');
		$columns['status'] = __('Status', 'leyka');
		$columns['type'] = __('Payment type', 'leyka');
		$columns['emails'] = __('Letter', 'leyka');
//		$columns[''] = __('', 'leyka');

		if($unsort)
			$columns = array_merge($columns, $unsort);

		return $columns;
	}

	function manage_columns_content($column_name, $donation_id) {

		$donation = new Leyka_Donation($donation_id);
        switch($column_name) {
            case 'ID': echo $donation_id; break;
            case 'amount':
				$amount_css = ($donation->sum < 0) ? 'amount-negative' : 'amount';
				echo '<span class="'.$amount_css.'">'.$donation->amount.'&nbsp;'.$donation->currency_label.'</span>';                
                break;
            case 'donor': echo $donation->donor_name; break;
            case 'method':
                $gateway_label = $donation->gateway_id ? $donation->gateway_label : __('Custom payment info', 'leyka');
                $pm_label = $donation->gateway_id ? $donation->pm_label : $donation->pm;
                echo "<b>{$donation->payment_type_label}:</b> $pm_label <small>/ $gateway_label</small>";
                break;
            case 'donation_date':
                echo $donation->date;
                break;
            case 'status':
                echo '<i class="'.esc_attr($donation->status).'">'.$this->get_status_labels($donation->status).'</i>';
                break;
            case 'type':
                echo '<i class="'.esc_attr($donation->payment_type).'">'.$donation->payment_type_label.'</i>';
                break;
            case 'emails':
				if($donation->donor_email_date){
					echo str_replace(
						'%s',
						'<time>'.date(get_option('date_format').', H:i</time>', $donation->donor_email_date).'</time>',
						__('Sent at %s', 'leyka')
					);
				} else {?>

					<div class="leyka-no-donor-thanks" data-donation-id="<?php echo $donation_id;?>">
						<?php echo sprintf(
                            __("Not sent %s", 'leyka'),
                            "<div class='send-donor-thanks'>".__('(send it now)', 'leyka')."</div>"
                        );?>
						<?php wp_nonce_field('leyka_donor_email', '_leyka_donor_email_nonce', false, true); ?>
					</div>
				<?php }

                break;           
            default:
        }
	}

    public function manage_sortable_columns($sortable_columns) {

        $sortable_columns['ID'] = 'ID';
        $sortable_columns['donation_date'] = 'donation_date';
        $sortable_columns['donor'] = 'donor_name';
        $sortable_columns['type'] = 'payment_type';
//        $sortable_columns['status'] = 'donation_status'; // Apparently, WP can't sort posts by status

        return $sortable_columns;
    }

    public function do_column_sorting($vars) {

        if(empty($vars['orderby'])) {
            return $vars;
        }

        if($vars['orderby'] == 'donation_date') {
            $vars = array_merge($vars, array(
                'orderby' => 'date',
            ));
        } elseif($vars['orderby'] == 'donor_name') {
            $vars = array_merge($vars, array(
                'meta_key' => 'leyka_donor_name',
                'orderby' => 'meta_value',
            ));
        } elseif($vars['orderby'] == 'payment_type') {
            $vars = array_merge($vars, array(
                'meta_key' => 'leyka_payment_type',
                'orderby' => 'meta_value',
            ));
        }

        return $vars;
    }

    /** Save donation data metabox */
    public function save_donation_data($donation_id) {

        // Maybe donation is inserted trough API:
        if(empty($_POST['post_type']) || $_POST['post_type'] != Leyka_Donation_Management::$post_type) {
            return false;
        }

        if(
            empty($_POST['_donation_edit_nonce']) ||
            !wp_verify_nonce($_POST['_donation_edit_nonce'], 'donation_status_metabox')
        ) {
            return $donation_id;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $donation_id;
        }

        if( !current_user_can('edit_donation', $donation_id) ) {
            return $donation_id;
        }

        remove_action('save_post', array($this, 'save_donation_data'));

        $donation = new Leyka_Donation($donation_id);
        if($donation->status != $_POST['donation_status']) {
            $donation->status = $_POST['donation_status'];
        }

        if(isset($_POST['campaign-id']) && $donation->campaign_id != (int)$_POST['campaign-id']) {

            $old_campaign = new Leyka_Campaign($donation->campaign_id);
            $new_campaign = new Leyka_Campaign((int)$_POST['campaign-id']);

            $donation->campaign_id = (int)$_POST['campaign-id'];

            $old_campaign->update_total_funded_amount($donation)->refresh_target_state();
            $new_campaign->update_total_funded_amount($donation)->refresh_target_state();
        }

        // It's a new correction donation, set a title from it's campaign:
        $campaign = new Leyka_Campaign($donation->campaign_id);

        $donation_title = $campaign->payment_title ?
            $campaign->payment_title :
            ($campaign->title ? $campaign->title : sprintf(__('Donation #%s', 'leyka'), $donation_id));

        if($donation->title != $donation_title) {
            $donation->title = $donation_title;
        }

        if(isset($_POST['donor-name']) && $donation->donor_name != $_POST['donor-name']) {
            $donation->donor_name = $_POST['donor-name'];
        }

        if(isset($_POST['donor-email']) && $donation->donor_email != $_POST['donor-email']) {
            $donation->donor_email = $_POST['donor-email'];
        }

        if(isset($_POST['donation-amount']) && (float)$donation->amount != (float)$_POST['donation-amount']) {
            $donation->amount = (float)$_POST['donation-amount'];
        }

        if( !$donation->currency ) {
            $donation->currency = 'rur';
        }

        if(
            isset($_POST['donation-pm']) &&
            ($donation->pm != $_POST['donation-pm'] || $_POST['donation-pm'] == 'custom')
        ) {

            if($_POST['donation-pm'] == 'custom') {
                $donation->gateway_id = '';
                if($donation->pm_id != $_POST['custom-payment-info'])
                    $donation->pm_id = $_POST['custom-payment-info'];
            } else {
                $parts = explode('-', $_POST['donation-pm']);
                $donation->gateway_id = $parts[0];
                $donation->pm = $parts[1];
            }
        }

        if(isset($_POST['donation_date']) && $donation->date_timestamp != strtotime($_POST['donation_date'])) {
            $donation->date = $_POST['donation_date'];
        }

        if(isset($_POST['payment-type']) && $donation->payment_type != $_POST['payment-type']) {
            $donation->payment_type = $_POST['payment-type'];
        }

        do_action("leyka_{$donation->gateway_id}_save_donation_data", $donation);

        add_action('save_post', array($this, 'save_donation_data'));

        return true;
    }

	/** Helpers **/

	static function get_status_labels($status = false) {

        $labels = leyka()->get_donation_statuses();

        if(empty($status))
		    return $labels;
        elseif($status == 'publish')
            return $labels['funded'];
        else
		    return !empty($labels[$status]) ? $labels[$status] : false;
	}
} // class end

function leyka_donation_management() {
    return Leyka_Donation_Management::get_instance();
}


class Leyka_Donation {

	private $_id;

    /** @var WP_Post */
	private $_post_object;

    private $_donation_meta = array();

    public static function add(array $params = array()) {

        $status = empty($params['status']) ? 'submitted' : $params['status'];
        $id = wp_insert_post(array(
            'post_type' => Leyka_Donation_Management::$post_type,
            'post_status' => $status == 'submitted' ? $status : 'submitted',
            'post_date' => date('Y-m-d H:i:s'),
            'post_title' => empty($params['purpose_text']) ?
                leyka_options()->opt('donation_purpose_text') : $params['purpose_text'],
            'post_parent' => empty($params['init_recurring_donation']) ? 0 : (int)$params['init_recurring_donation'],
        ));

        $amount = empty($params['amount']) ? leyka_pf_get_amount_value() : round((float)$params['amount'], 2);
        update_post_meta($id, 'leyka_donation_amount', $amount);

        $currency = empty($params['currency']) ? leyka_pf_get_currency_value() : $params['currency'];
        update_post_meta($id, 'leyka_donation_currency', $currency);

        $currency_rate = leyka_options()->opt("currency_rur2$currency");
        if($currency == 'RUR' || !$currency_rate)
            $currency_rate = 1.0;

        update_post_meta(
            $id,
            'leyka_main_curr_amount',
            $currency == 'RUR' ? $amount : $amount*$currency_rate
        );

        update_post_meta(
            $id, 'leyka_donor_name',
            empty($params['donor_name']) ? leyka_pf_get_donor_name_value() : $params['donor_name']
        );
        update_post_meta(
            $id, 'leyka_donor_email',
            empty($params['donor_email']) ? leyka_pf_get_donor_email_value() : $params['donor_email']
        );

        $pm_data = leyka_pf_get_payment_method_value();
        $pm_data = $pm_data ? $pm_data : array('payment_method_id' => '', 'gateway_id' => '',);
        update_post_meta(
            $id, 'leyka_payment_method',
            empty($params['payment_method_id']) ? $pm_data['payment_method_id'] : $params['payment_method_id']
        );
        update_post_meta(
            $id, 'leyka_gateway',
            empty($params['gateway_id']) ? $pm_data['gateway_id'] : $params['gateway_id']
        );
        update_post_meta(
            $id, 'leyka_campaign_id',
            empty($params['campaign_id']) ? leyka_pf_get_campaign_id_value() : $params['campaign_id']
        );

        if( !get_post_meta($id, '_leyka_donor_email_date', true) )
            update_post_meta($id, '_leyka_donor_email_date', 0);
        if( !get_post_meta($id, '_leyka_managers_emails_date', true) )
            update_post_meta($id, '_leyka_managers_emails_date', 0);

        update_post_meta($id, '_status_log', array(array('date' => time(), 'status' => $status)));

        $params['payment_type'] = empty($params['payment_type']) ?
            'single' : ($params['payment_type'] == 'rebill' ? 'rebill' : 'correction');
        update_post_meta($id, 'leyka_payment_type', $params['payment_type']);

        if( !empty($params['gateway_id']) ) {
            do_action("leyka_{$params['gateway_id']}_add_donation_specific_data", $id, $params);
        }

        if( !empty($params['recurrents_cancelled']) ) {
            update_post_meta($id, 'leyka_recurrents_cancelled', $params['recurrents_cancelled']);
        }

        if( !empty($params['recurrents_cancel_date']) ) {
            update_post_meta($id, 'leyka_recurrents_cancel_date', $params['recurrents_cancel_date']);
        } elseif( !empty($params['recurrents_cancelled']) && $params['recurrents_cancelled']) {
            update_post_meta($id, 'leyka_recurrents_cancel_date', time());
        } else {
            update_post_meta($id, 'leyka_recurrents_cancel_date', 0);
        }

        return $id;
    }

    /**
     * A wrapper to access gateway's method to get init recurrent donation.
     * @param int $donation_id
     * @return Leyka_Donation or false if param is wrong or nothing foundd.
     */
    public static function get_init_recurrent_donation($donation_id) {

        if( !$donation_id || (int)$donation_id <= 0 ) {
            return false;
        }

        $donation = new Leyka_Donation($donation_id);

        return leyka_get_gateway_by_id($donation->gateway_id)->get_init_recurrent_donation($donation);
    }

	function __construct($donation) {

        if(is_int($donation) && (int)$donation > 0) {

            $this->_post_object = get_post($donation);

            if($this->_post_object) {
                $this->_id = $donation;
            } else {
                return false;
            }

        } elseif(is_a($donation, 'WP_Post')) {
            /** @var $donation WP_Post */
            $this->_id = $donation->ID;
            $this->_post_object = $donation;
        } else {
            return false;
        }

        if( !$this->_donation_meta ) {

            $meta = get_post_meta($this->_id, '', true);

            $meta['leyka_donation_amount'] = empty($meta['leyka_donation_amount']) ?
                0.0 : (float)$meta['leyka_donation_amount'][0];

            if( !empty($meta['leyka_campaign_id']) ) {

                // Don't use Leyka_Campaign here to avoid loop dependency:
                $campaign = get_post((int)$meta['leyka_campaign_id'][0]);
                $payment_title = get_post_meta($campaign->ID, 'payment_title', true);
                if( !$payment_title ) {
                    $payment_title = $campaign->post_title;
                }
            }

            $this->_donation_meta = array(
                'payment_title' => empty($payment_title) ? $this->_post_object->post_title : $payment_title,
                'payment_type' => empty($meta['leyka_payment_type']) ? 'single' : $meta['leyka_payment_type'][0],
                'payment_method' => empty($meta['leyka_payment_method']) ? '' : $meta['leyka_payment_method'][0],
                'gateway' => empty($meta['leyka_gateway']) ? '' : $meta['leyka_gateway'][0],
                'currency' => empty($meta['leyka_donation_currency']) ? 'rur' : $meta['leyka_donation_currency'][0],
                'amount' => $meta['leyka_donation_amount'],
                'main_curr_amount' => !empty($meta['leyka_main_curr_amount'][0]) ?
                    (float)$meta['leyka_main_curr_amount'][0] : $meta['leyka_donation_amount'],
                'donor_name' => empty($meta['leyka_donor_name']) ? '' : $meta['leyka_donor_name'][0],
                'donor_email' => empty($meta['leyka_donor_email']) ? '' : $meta['leyka_donor_email'][0],
                'donor_email_date' => empty($meta['_leyka_donor_email_date']) ?
                    '' : $meta['_leyka_donor_email_date'][0],
                'managers_emails_date' => empty($meta['_leyka_managers_emails_date']) ?
                    '' : $meta['_leyka_managers_emails_date'][0],
                'campaign_id' => empty($meta['leyka_campaign_id']) ? 0 : $meta['leyka_campaign_id'][0],
                'status_log' => empty($meta['_status_log']) ? '' : maybe_unserialize($meta['_status_log'][0]),
                'gateway_response' => empty($meta['leyka_gateway_response']) ? '' : $meta['leyka_gateway_response'][0],

                'recurrents_cancelled' => isset($meta['leyka_recurrents_cancelled']) ?
                    $meta['leyka_recurrents_cancelled'][0] : false,
                'recurrents_cancel_date' => isset($meta['leyka_recurrents_cancel_date']) ?
                    $meta['leyka_recurrents_cancel_date'][0] : false,
            );
        }
	}

    public function __get($field) {

        switch($field) {
            case 'id':
            case 'ID': return $this->_id;
            case 'title':
            case 'name': return $this->_post_object->post_title;
            case 'purpose':
            case 'purpose_text':
            case 'payment_title':
            case 'campaign_payment_title':
                return $this->_donation_meta['payment_title'];
            case 'status': return $this->_post_object->post_status;
            case 'status_label':
                $stati = leyka_get_donation_status_list();
                return $stati[$this->_post_object->post_status];
            case 'status_log':
                return $this->_donation_meta['status_log'];
            case 'date':
            case 'date_label':
                $date_format = get_option('date_format');
                $time_format = get_option('time_format');
                $donation_timestamp = strtotime($this->_post_object->post_date);
                return apply_filters(
                    'leyka_admin_donation_date',
                    date($date_format.' '.$time_format, $donation_timestamp),
                    $donation_timestamp, $date_format, $time_format
                );
            case 'date_timestamp': return strtotime($this->_post_object->post_date);
            case 'date_funded':
            case 'funded_date':
                $date_funded = $this->get_funded_date();
                return $date_funded ? date(get_option('date_format'), $date_funded) : 0;
            case 'payment_method':
            case 'payment_method_id':
            case 'pm':
            case 'pm_id':
                return $this->_donation_meta['payment_method'];
            case 'gateway':
            case 'gateway_id':
            case 'gw_id':
                return $this->_donation_meta['gateway'];
            case 'gateway_label':
                $gateway = leyka_get_gateway_by_id($this->_donation_meta['gateway']);
                return ($gateway ? $gateway->label : __('Unknown gateway', 'leyka'));
            case 'pm_label':
            case 'payment_method_label':
                $pm = leyka_get_pm_by_id($this->_donation_meta['payment_method']);
                return ($pm ? $pm->label : __('Unknown payment method', 'leyka'));
            case 'currency':
                return $this->_donation_meta['currency'];
            case 'currency_label':
                return leyka_options()->opt('leyka_currency_'.$this->_donation_meta['currency'].'_label');
            case 'sum':
            case 'amount':
                return $this->_donation_meta['amount'];
            case 'main_curr_amount':
            case 'amount_equiv':
                return $this->_donation_meta['main_curr_amount'];
            case 'donor_name':
                return $this->_donation_meta['donor_name'];
            case 'donor_email':
                return $this->_donation_meta['donor_email'];
            case 'donor_email_date':
                return $this->_donation_meta['donor_email_date'];
            case 'managers_emails_date':
                return $this->_donation_meta['managers_emails_date'];
            case 'campaign_id':
                return $this->_donation_meta['campaign_id'];
            case 'gateway_response':
                return $this->_donation_meta['gateway_response'];
            case 'gateway_response_formatted':
                return $this->gateway ?
                    leyka_get_gateway_by_id($this->gateway)->get_gateway_response_formatted($this) : array();

            case 'type':
            case 'payment_type': return $this->_donation_meta['payment_type'];
            case 'type_label':
            case 'payment_type_label': return __($this->_donation_meta['payment_type'], 'leyka');

            case 'init_recurring_payment_id':
            case 'init_recurring_donation_id': return $this->_post_object->post_parent;
            case 'init_recurring_payment':
            case 'init_recurring_donation': return $this->_post_object->post_parent ?
                new Leyka_Donation($this->_post_object->post_parent) : false;

            default:
                return apply_filters('leyka_get_unknown_donation_field', null, $field, $this);
        }
    }

    public function __set($field, $value) {

        switch($field) {
            case 'title':
            case 'payment_title':
            case 'purpose_text':
                if($value != $this->_post_object->post_title) {
                    wp_update_post(array('ID' => $this->_id, 'post_title' => $value));
                    $this->_post_object->post_title = $value;
                }
                break;

            case 'status':
                if( !array_key_exists($value, leyka_get_donation_status_list()) || $value == $this->status )
                    return false;

                $res = wp_update_post(array('ID' => $this->_id, 'post_status' => $value));

                $status_log = get_post_meta($this->_id, '_status_log', true);
                $status_log[] = array('date' => time(), 'status' => $value);
                update_post_meta($this->_id, '_status_log', $status_log);
                $this->_donation_meta['status_log'] = $status_log;

                break;

            case 'date':
                wp_update_post(array('ID' => $this->_id, 'post_date' => $value));
                break;
            case 'date_timestamp':
                    wp_update_post(array('ID' => $this->_id, 'post_date' => date('Y-m-d H:i:s', $value)));
                break;

            case 'donor_name':
                update_post_meta($this->_id, 'leyka_donor_name', $value);
                $this->_donation_meta['donor_name'] = $value;
                break;
            case 'donor_email':
                update_post_meta($this->_id, 'leyka_donor_email', $value);
                $this->_donation_meta['donor_email'] = $value;
                break;

            case 'sum':
            case 'amount':
            case 'donation_amount':
                update_post_meta($this->_id, 'leyka_donation_amount', $value);
                $this->_donation_meta['amount'] = $value;
                break;

            case 'currency':
            case 'donation_currency':
                update_post_meta($this->_id, 'leyka_donation_currency', $value);
                $this->_donation_meta['currency'] = $value;
                break;

            case 'gw_id':
            case 'gateway_id':
                update_post_meta($this->_id, 'leyka_gateway', $value);
                $this->_donation_meta['gateway'] = $value;
                break;

            case 'pm':
            case 'pm_id':
            case 'payment_method_id':
                update_post_meta($this->_id, 'leyka_payment_method', $value);
                $this->_donation_meta['payment_method'] = $value;
                break;

            case 'type':
            case 'payment_type':
                $value = in_array($value, array_keys(leyka_get_payment_types_list())) ? $value : 'single';
                update_post_meta($this->_id, 'leyka_payment_type', $value);
                $this->_donation_meta['payment_type'] = $value;
                break;

            case 'campaign':
            case 'campaign_id':
                $value = (int)$value > 0 ? (int)$value : $this->campaign_id;
                update_post_meta($this->_id, 'leyka_campaign_id', $value);
                $this->_donation_meta['campaign_id'] = $value;
                break;

            case 'init_recurring_payment':
            case 'init_recurring_payment_id':
            case 'init_recurring_donation':
            case 'init_recurring_donation_id':
                $value = (int)$value;
                if($value > 0 && $value != $this->_post_object->post_parent) {
                    wp_update_post(array('ID' => $this->_id, 'post_parent' => $value));
                    $this->_post_object->post_parent = $value;
                }
                break;

            case 'recurrents_cancelled':
                $value = !!$value;
                update_post_meta($this->_id, 'leyka_recurrents_cancelled', $value);
                update_post_meta($this->_id, 'leyka_recurrents_cancel_date', $value ? time() : 0);
                $this->_donation_meta['recurrents_cancelled'] = $value;
                $this->_donation_meta['recurrents_cancel_date'] = $value;
                break;

            default:
                do_action('leyka_set_unknown_donation_field', $field, $value, $this);
        }
        return true;
    }

    public function add_gateway_response($resp_text) {

        $this->_donation_meta['gateway_response'] = $resp_text;

        update_post_meta($this->_id, 'leyka_gateway_response', $this->_donation_meta['gateway_response']);
    }

    public function get_specific_data_admin_fields() {

        $data_fields = leyka_get_gateway_by_id($this->gateway_id)->get_specific_data_admin_fields($this->id);

        return $data_fields ? $data_fields : array();
    }

    /**
     * @return mixed Last date when status was changed to "funded" in sec, or false if donation was never funded.
     */
    public function get_funded_date() {

        $last_date_funded = 0;
        foreach((array)$this->status_log as $status_change) {

            if($status_change['status'] == 'funded' && $status_change['date'] > $last_date_funded)
                $last_date_funded = $status_change['date'];
        }

        return $last_date_funded ? $last_date_funded : false;
    }
}


function leyka_cancel_recurrents_action() {

    if(
        empty($_POST['nonce'])
     || !wp_verify_nonce($_POST['nonce'], 'leyka_recurrent_cancel')
     || empty($_POST['donation_id'])
    )
        die('-1');

    $_POST['donation_id'] = (int)$_POST['donation_id'];

    $donation = new Leyka_Donation($_POST['donation_id']);
    do_action('leyka_cancel_recurrents-'.$donation->gateway_id, $donation);
}
//add_action('wp_ajax_leyka_cancel_recurrents', 'leyka_cancel_recurrents_action');
//add_action('wp_ajax_nopriv_leyka_cancel_recurrents', 'leyka_cancel_recurrents_action');