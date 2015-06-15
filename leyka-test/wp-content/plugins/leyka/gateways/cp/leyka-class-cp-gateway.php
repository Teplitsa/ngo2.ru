<?php if( !defined('WPINC') ) die;
/**
 * Leyka_CP_Gateway class
 */

class Leyka_CP_Gateway extends Leyka_Gateway {

    protected static $_instance;

    protected function _set_attributes() {

        $this->_id = 'cp';
        $this->_title = __('CloudPayments', 'leyka');
    }

    protected function _set_options_defaults() {

        if($this->_options) {
            return;
        }

        $this->_options = array(
            'cp_public_id' => array(
                'type' => 'text', // html, rich_html, select, radio, checkbox, multi_checkbox  
                'value' => '',
                'default' => '',
                'title' => __('Public ID', 'leyka'),
                'description' => __('Please, enter your CloudPayments public ID here. It can be found in your CloudPayments control panel.', 'leyka'),
                'required' => true,
                'placeholder' => __('Ex., 1234', 'leyka'),
                'list_entries' => array(), // For select, radio & checkbox fields
                'validation_rules' => array(), // List of regexp?..
            ),
            'cp_test_mode' => array(
                'type' => 'checkbox', // html, rich_html, select, radio, checkbox, multi_checkbox
                'value' => '',
                'default' => 1,
                'title' => __('Testing mode', 'leyka'),
                'description' => __('Check if CloudPayments shop account is in testing mode.', 'leyka'),
                'required' => false,
                'placeholder' => '',
                'list_entries' => array(), // For select, radio & checkbox fields
                'validation_rules' => array(), // List of regexp?..
            ),
        );
    }

    protected function _initialize_pm_list() {

        if(empty($this->_payment_methods['card'])) {
            $this->_payment_methods['card'] = Leyka_CP_Card::get_instance();
        }
    }

    public function process_form($gateway_id, $pm_id, $donation_id, $form_data) {
    }

    public function submission_redirect_url($current_url, $pm_id) {

        return leyka_options()->opt('cp_test_mode') ?
            '' : '';
    }

    public function submission_form_data($form_data_vars, $pm_id, $donation_id) {

		if( !array_key_exists($pm_id, $this->_payment_methods) )
			return $form_data_vars; // It's not our PM

//        $donation = new Leyka_Donation($donation_id);
//	    $amount = number_format((float)$donation->amount, 2, '.', '');
//        $hash = md5(leyka_options()->opt('robokassa_shop_id').":$amount:$donation_id:"
//               .leyka_options()->opt('robokassa_shop_password1').":Shp_item=1");
//
//        $pm_curr = $pm_id;
//        switch($pm_id) {
//            case 'WMR': $pm_curr .= 'M'; break;
//            case 'Other': $pm_curr = ''; break;
//            default: $pm_curr .= 'R';
//        }
//
//        $form_data_vars = array(
//            'MrchLogin' => leyka_options()->opt('robokassa_shop_id'),
//            'InvId' => $donation_id,
//            'OutSum' => $amount,
//            'Desc' => $donation->payment_title,
//            'SignatureValue' => $hash,
//            'Shp_item' => 1, // Maybe, not needed
//            'IncCurrLabel' => $pm_curr, // Default PM + Currency. "R" for "RUR", as we'll always use RUR for now
//            'Culture' => get_locale() == 'ru_RU' ? 'ru' : 'en',
//        );

		return $form_data_vars;
    }

    public function log_gateway_fields($donation_id) {
    }

    public function _handle_service_calls($call_type = '') {

//        if(empty($_REQUEST['InvId'])) {
//
//            $message = __("This message has been sent because a call to your Robokassa callback (Result URL) was made without InvId parameter given. The details of the call are below.", 'leyka')."\n\r\n\r";
//
//            $message .= "THEIR_POST:\n\r".print_r($_POST,true)."\n\r\n\r";
//            $message .= "GET:\n\r".print_r($_GET,true)."\n\r\n\r";
//            $message .= "SERVER:\n\r".print_r($_SERVER,true)."\n\r\n\r";
//
//            wp_mail(get_option('admin_email'), __('Robokassa - InvId missing!', 'leyka'), $message);
//            status_header(200);
//            die();
//        }
//
//        $donation = new Leyka_Donation((int)$_REQUEST['InvId']);
//
//		// Test for e-sign. Values from Robokassa must be used:
//
//        $sign = strtoupper(md5("{$_REQUEST['OutSum']}:{$_REQUEST['InvId']}:".leyka_options()->opt('robokassa_shop_password2').":Shp_item=1"));
//        if(empty($_REQUEST['SignatureValue']) || strtoupper($_REQUEST['SignatureValue']) != $sign) {
//
//            $message = __("This message has been sent because a call to your Robokassa callback was called with wrong digital signature. This could mean someone is trying to hack your payment website. The details of the call are below:", 'leyka')."\n\r\n\r";
//
//            $message .= "POST:\n\r".print_r($_POST,true)."\n\r\n\r";
//            $message .= "GET:\n\r".print_r($_GET,true)."\n\r\n\r";
//            $message .= "SERVER:\n\r".print_r($_SERVER,true)."\n\r\n\r";
//            $message .= "Signature from request:\n\r".print_r($_REQUEST['SignatureValue'], true)."\n\r\n\r";
//            $message .= "Signature calculated:\n\r".print_r($sign, true)."\n\r\n\r";
//
//            wp_mail(get_option('admin_email'), __('Robokassa digital signature check failed!', 'leyka'), $message);
//            die();
//        }
//
//        // Single payment:
//        if($donation->status != 'funded') {
//
//            $donation->add_gateway_response($_REQUEST);
//            $donation->status = 'funded';
//
////            $currency_letter = substr($_REQUEST['IncCurrLabel'], -1);
//            $_REQUEST['IncCurrLabel'] = empty($_REQUEST['IncCurrLabel']) ?
//                '' : substr_replace($_REQUEST['IncCurrLabel'], '', -1);
//
//            if(
//                $donation->pm_id != $_REQUEST['IncCurrLabel'] &&
//                array_key_exists($_REQUEST['IncCurrLabel'], $this->_payment_methods)
//            ) {
//                $donation->pm_id = $_REQUEST['IncCurrLabel'];
//            }
//
//            Leyka_Donation_Management::send_all_emails($donation->id);
//
//            die('OK'.$_REQUEST['InvId']);
//
//        } else {
//            die();
//        }
    }

//    protected function _get_value_if_any($arr, $key, $val = false) {
//
//        return empty($arr[$key]) ? '' : ($val ? $val : $arr[$key]);
//    }

    public function get_gateway_response_formatted(Leyka_Donation $donation) {

        if( !$donation->gateway_response )
            return array();

        $vars = maybe_unserialize($donation->gateway_response);
        if( !$vars || !is_array($vars) )
            return array();

        return array(
//            __('Outcoming sum:', 'leyka') => $this->_get_value_if_any($vars, 'OutSum', !empty($vars['OutSum']) ? round($vars['OutSum'], 2) : false),
//            __('Incoming sum:', 'leyka') => $this->_get_value_if_any($vars, 'IncSum', !empty($vars['IncSum']) ? round($vars['IncSum'], 2) : false),
//            __('Invoice ID:', 'leyka') => $this->_get_value_if_any($vars, 'InvId'),
//            __('Signature value (sent from Robokassa):', 'leyka') => $this->_get_value_if_any($vars, 'SignatureValue'),
//            __('Payment method:', 'leyka') => $this->_get_value_if_any($vars, 'PaymentMethod'),
//            __('Robokassa currency label:', 'leyka') => $this->_get_value_if_any($vars, 'IncCurrLabel'),
        );
    }
} // Gateway class end


class Leyka_CP_Card extends Leyka_Payment_Method {

    protected static $_instance = null;

    public function _set_attributes() {

        $this->_id = 'card';
        $this->_gateway_id = 'cp';

        $this->_label_backend = __('Payment with Banking Card', 'leyka');
        $this->_label = __('Banking Card', 'leyka');

        $this->_icons = apply_filters('leyka_icons_'.$this->_gateway_id.'_'.$this->_id, array(
            LEYKA_PLUGIN_BASE_URL.'gateways/cp/icons/visa.png',
            LEYKA_PLUGIN_BASE_URL.'gateways/cp/icons/master.png',
        ));

        $this->_supported_currencies[] = 'rur';

        $this->_default_currency = 'rur';
    }

    protected function _set_options_defaults() {

        if($this->_options) {
            return;
        }

        $this->_options = array(
            $this->full_id.'_description' => array(
                'type' => 'html',
                'default' => __('<a href="//cloudpayments.ru/">CloudPayments</a> is a Designer IT-solutions for the e-commerce market. Every partner receives the most comprehensive set of key technical options allowing to create a customer-centric payment system on site or in mobile application. Partners are allowed to receive payments in roubles and in other world currencies.', 'leyka'),
                'title' => __('CloudPayments bank card payment description', 'leyka'),
                'description' => __('Please, enter CloudPayments gateway description that will be shown to the donor when this payment method will be selected for using.', 'leyka'),
                'required' => 0,
                'validation_rules' => array(), // List of regexp?..
            ),
        );
    }
}

function leyka_add_gateway_cp() {
    leyka()->add_gateway(Leyka_CP_Gateway::get_instance());
}
add_action('leyka_init_actions', 'leyka_add_gateway_cp');

add_action('wp_enqueue_scripts', 'leyka_enqueue_scripts_cp');
function leyka_enqueue_scripts_cp() {

    if(Leyka_CP_Card::get_instance()->active && leyka_form_is_screening()) {
        wp_enqueue_script('leyka-cp-widget', 'https://widget.cloudpayments.ru/bundles/cloudpayments');
        wp_enqueue_script(
            'leyka-cp',
            LEYKA_PLUGIN_BASE_URL.'gateways/'.Leyka_CP_Gateway::get_instance()->id.'/js/leyka.cp.js',
            array('jquery', 'leyka-cp-widget'),
            LEYKA_VERSION,
            true
        );
    }
}