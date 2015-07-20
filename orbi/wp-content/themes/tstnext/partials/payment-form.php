<?php
/**
 * Copy paste whole payment form template - to alter class in favour if MDL
 * be careful on updates
 **/

$active_pm = apply_filters('leyka_form_pm_order', leyka_get_pm_list(true));

leyka_pf_submission_errors();?>

<div id="leyka-payment-form" class="leyka-tpl-toggles">
<?php
	$counter = 0;
	foreach($active_pm as $i => $pm) {
	leyka_setup_current_pm($pm); 
	$counter++;?>

<div class="leyka-payment-option toggle <?php if($counter == 1) echo 'toggled';?> <?php echo esc_attr($pm->full_id);?>">
<div class="toggle-trigger <?php echo count($active_pm) > 1 ? '' : 'toggle-inactive';?>">
    <?php echo leyka_pf_get_pm_label();?>
</div>
<div class="toggle-area">
<form class="leyka-pm-form" id="<?php echo leyka_pf_get_form_id();?>" action="<?php echo leyka_pf_get_form_action();?>" method="post">
	
	<div class="leyka-pm-fields">
<?php
	echo leyka_pf_get_amount_field();
	echo leyka_pf_get_hidden_fields();	
?>
	<input name="leyka_payment_method" value="<?php echo esc_attr($pm->full_id);?>" type="hidden" />
	<input name="leyka_ga_payment_method" value="<?php echo esc_attr($pm->label);?>" type="hidden" />
	<div class='leyka-user-data'>		
		<!-- name -->
		<div class="mdl-textfield mdl-js-textfield leyka-field name">
			<input type="text" class="required mdl-textfield__input" name="leyka_donor_name" id="leyka_donor_name" value="">
			<label for="leyka_donor_name" class="leyka-screen-reader-text mdl-textfield__label"><?php _e('Your name', 'leyka');?></label>		
			<span id="leyka_donor_name-error" class="field-error mdl-textfield__error"></span>
		</div>
		
		<!-- email -->
		<div class="mdl-textfield mdl-js-textfield leyka-field email">
			<input type="text" value="" id="leyka_donor_email" name="leyka_donor_email" class="required email">
			<label class="leyka-screen-reader-text" for="leyka_donor_email">Ваш email</label>
			<span class="field-error" id="leyka_donor_email-error"></span>
		</div>
	<?php		
		//echo leyka_pf_get_pm_fields();
	?>
	</div>
	
<?php
    //echo leyka_pf_get_recurring_field();
    echo leyka_pf_get_agree_field();
    echo leyka_pf_get_submit_field();
?>
	<div class="mdl-textfield mdl-js-textfield leyka-field">
		<input type="submit" class="" id="leyka_donation_submit" name="leyka_donation_submit" value="<?php echo esc_attr($this->get_submit_label());?>" />
	</div>
	
<?php
	$icons = leyka_pf_get_pm_icons();	
	if($icons) {

		$list = array();
		foreach($icons as $i) {
			$list[] = "<li>{$i}</li>";
		}

		echo '<ul class="leyka-pm-icons cf">'.implode('', $list).'</ul>';
	}?>
	</div> <!-- .leyka-pm-fields -->	

<?php echo "<div class='leyka-pm-desc'>".apply_filters('leyka_the_content', leyka_pf_get_pm_description())."</div>"; ?>

</form>
</div>
</div>
<?php }?>

<?php if(leyka_options()->opt('show_campaign_sharing')) {
    leyka_share_campaign_block();
}

leyka_pf_footer();?>

</div><!-- #leyka-payment-form -->