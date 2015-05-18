<?php if( !defined('WPINC') ) die; // If this file is called directly, abort

function leyka_add_gateway_metabox($post, $args) {

    // $post is always null

    /** @var Leyka_Gateway $gateway */
    $gateway = $args['args']['gateway'];

    $pm_active = leyka_options()->opt('pm_available');?>

    <div>

    <?php foreach($gateway->get_payment_methods() as $pm) {?>
        <div>
            <input type="checkbox" name="leyka_pm_available[]" value="<?php echo $pm->full_id;?>" class="pm-active" id="<?php echo $pm->full_id;?>" data-pm-label="<?php echo $pm->title_backend;?>" <?php echo in_array($pm->full_id, $pm_active) ? 'checked="checked"' : '';?>>
            <label for="<?php echo $pm->full_id;?>"><?php echo $pm->title_backend;?></label>
        </div>
    <?php }?>

    </div>
<?php
}

$count = 0;
foreach(leyka_get_gateways() as $gateway) { //add metaboxes

    $count++;
    $count = $count > 3 ? 1 : $count;

    add_meta_box(
        'leyka_payment_settings_gateway_'.$gateway->id,
        $gateway->title,
        'leyka_add_gateway_metabox',
        'leyka_settings_payment_'.$count,
        'normal',
        'high',
        array('gateway' => $gateway,)
    );
}
?>

<h3><?php _e('Payment methods', 'leyka');?></h3>

<div class="metabox-holder" id="leyka-pm-selectors">
    <div class="postbox-container" id="postbox-container-1">
        <?php do_meta_boxes('leyka_settings_payment_1', 'normal', null);?>
    </div>
    
    <div class="postbox-container" id="postbox-container-2">
        <?php do_meta_boxes('leyka_settings_payment_2', 'normal', null);?>
    </div>
    
     <div class="postbox-container" id="postbox-container-3">
        <?php do_meta_boxes('leyka_settings_payment_3', 'normal', null);?>
    </div>
</div>

<div id="payment-settings-area">
    
    <div class="pm-active-panel">
    
        <div id="active-pm-settings" class="panel-content">
            <h3 class="panel-title"><?php _e('Active payment methods', 'leyka');?></h3>
            <p class="panel-desc"><?php _e('Please, set your gateways parameters', 'leyka');?></p>
    
            <?php $pm_available = leyka_options()->opt('pm_available');
    
                $active_gateways = array();
                foreach($pm_available as $pm_full_id) {
    
                    $gateway_id = explode('-', $pm_full_id);
                    $gateway_id = reset($gateway_id); // Strict standards
    
                    if( !in_array($gateway_id, $active_gateways) )
                        $active_gateways[] = $gateway_id;
                }?>
    
            <div id="pm-settings-wrapper">
            <?php foreach(leyka_get_gateways() as $gateway) { /** @var $gateway Leyka_Gateway */ ?>
                <div id="gateway-<?php echo $gateway->id;?>" class="gateway-settings" <?php echo in_array($gateway->id, $active_gateways) ? '' : 'style="display:none;"'?>>
                    <h3 class="accordion-section-title"><?php echo $gateway->title;?></h3>
                    <div class="accordion-section-content">
                        <?php foreach($gateway->get_options_names() as $option_id) {
    
                            $option = leyka_options()->get_info_of($option_id);
                            do_action("leyka_render_{$option['type']}", $option_id, $option);
                        }
    
                        foreach($gateway->get_payment_methods() as $pm) { /** @var $pm Leyka_Payment_Method */ ?>
    
                            <div id="pm-<?php echo $pm->full_id;?>" class="pm-settings" <?php echo in_array($pm->full_id, $pm_available) ? '' : 'style="display:none;"';?>>
                            <?php foreach($pm->get_pm_options_names() as $option_id) {
    
                                $option = leyka_options()->get_info_of($option_id);
                                do_action("leyka_render_{$option['type']}", $option_id, $option);
                            }?>
                            </div>
                        <?php }?>
                    </div>
                </div>
            <?php }?>
            </div><!-- #pm-settings-wrapper -->
           
        </div><!-- #active-pm-settings -->
    </div><!-- .active-pm-panel -->

    <div class="pm-order-panel"><div class="panel-content">

        <h3 class="panel-title"><?php _e('Payment methods order', 'leyka');?></h3>
        <p class="panel-desc"><?php _e('Drag the elements up or down to change their order in donation forms', 'leyka');?></p>
        <ul id="pm-order-settings">
            <?php $pm_order = explode('pm_order[]=', leyka_options()->opt('pm_order'));
            array_shift($pm_order);

            foreach($pm_order as $pm) { $pm = leyka_get_pm_by_id(str_replace('&amp;', '', $pm), true);?>

                <li data-pm-id="<?php echo $pm->full_id;?>" class="pm-order">
                    <?php echo $pm->label_backend == $pm->label ? '' : $pm->label_backend.'<br>';?>
                    <span class="pm-label" id="pm-label-<?php echo $pm->full_id;?>"><?php echo $pm->label;?></span>
                    <span class="pm-label-fields" style="display:none;">
                        <label for="pm_labels[<?php echo $pm->full_id;?>]"><?php _e('New label:', 'leyka');?></label>
                        <input type="text" id="pm_labels[<?php echo $pm->full_id;?>]" value="<?php echo $pm->label;?>" placeholder="<?php _e('Enter some title for this payment method', 'leyka');?>">
                        <input type="hidden" class="pm-label-field" name="leyka_<?php echo $pm->full_id;?>_label" value="<?php echo $pm->label;?>">
                        <span class="new-pm-label-ok"><? _e('OK', 'leyka');?></span>
                        <span class="new-pm-label-cancel"><? _e('Cancel', 'leyka');?></span>
                    </span>
                    <span class="pm-change-label" data-pm-id="<?php echo $pm->full_id;?>">Переим.</span>
                </li>
            <?php }?>
        </ul>
        <input type="hidden" name="leyka_pm_order" value="<?php echo leyka_options()->opt('pm_order');?>">

    </div></div>

</div><!-- #payment-settings-area -->