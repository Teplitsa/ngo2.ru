<?php
/** Formidable filters **/

// @to_do (no correction for repeatable section)
add_filter('frm_submit_button_class', 'tst_formidable_submit_classes', 2, 2);
function tst_formidable_submit_classes($class, $form){
	
	
	$class[] = 'mdl-button mdl-js-button mdl-button--raised mdl-button--colored mdl-js-ripple-effect';
	
	return $class;
}

add_filter('frm_field_classes', 'tst_formidable_field_classes', 2, 2);
function tst_formidable_field_classes($class, $field){
	
	if(in_array($field['type'], array('text', 'email', 'textarea', 'url', 'number'))) {
		$class = 'mdl-textfield__input';
	}
	elseif($field['type'] == 'checkbox'){
		
		if(isset($field['classes']) && false !== strpos($field['classes'], 'switch')){			
			$class .= " mdl-switch__input";
		}
		else {
			$class = "mdl-checkbox__input";
		}
	}
	elseif($field['type'] == 'radio'){
		$class = "mdl-radio__button";
	}
	return $class;
}

add_filter('frm_replace_shortcodes', 'tst_formidable_default_html', 2, 3);
function tst_formidable_default_html($html, $field, $params) {
	
	if(in_array($field['type'], array('text', 'email', 'textarea', 'url')))  {
			
		$html = str_replace('frm_form_field', 'mdl-textfield mdl-js-textfield mdl-textfield--floating-label mdl-textfield--full-width frm_form_field', $html);
		$html = str_replace('frm_primary_label', 'mdl-textfield__label frm_primary_label', $html);
		$html = str_replace('frm_error', 'mdl-textfield__error frm_error', $html);
		
		if((int)$field['read_only'] == 1){			
			$html = str_replace('<input', '<input disabled="disabled" ', $html);
		}		
	}
	elseif(in_array($field['type'], array('number')))  {
			
		$html = str_replace('frm_form_field', 'mdl-textfield mdl-js-textfield mdl-textfield--floating-label frm_form_field', $html);
		$html = str_replace('frm_primary_label', 'mdl-textfield__label frm_primary_label', $html);
		$html = str_replace('frm_error', 'mdl-textfield__error frm_error', $html);
		
		if((int)$field['read_only'] == 1){			
			$html = str_replace('<input', '<input disabled="disabled" ', $html);
		}		
	}
	elseif($field['type'] == 'checkbox'){
		if(isset($field['classes']) && false !== strpos($field['classes'], 'switch')){			
			$html = str_replace('<label for=', '<label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for=', $html);
		}
		else {
			$html = str_replace('<label for=', '<label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for=', $html);
		}
		
		$html = str_replace('frm_form_field', 'tst-inputfix frm_form_field', $html);
		$html = str_replace('frm_primary_label', 'tst-inputfix__label frm_primary_label', $html);
		
	}
	elseif($field['type'] == 'radio'){
		
		$html = str_replace('<label for=', '<label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for=', $html);
		$html = str_replace('frm_primary_label', 'tst-inputfix__label frm_primary_label', $html);
		$html = str_replace('frm_form_field', 'tst-inputfix frm_form_field', $html);
	}
	elseif($field['type'] == 'select'){
		
		$html = str_replace('frm_form_field', 'tst-select frm_form_field', $html);
		$html = str_replace('frm_primary_label', 'tst-inputfix__label frm_primary_label', $html);		
	}
	
	return $html;
}


add_filter('frm_field_label_seen', 'tst_formidable_input_options_html', 2, 3);
function tst_formidable_input_options_html($opt, $opt_key, $field) {
	
	if(is_admin())
		return $opt;
	
	if($field['type'] == 'checkbox') {
		if(isset($field['classes']) && false !== strpos($field['classes'], 'switch')){
			$opt = "<span class='mdl-switch__label'>{$opt}</span>";
		}
		else {
			$opt = "<span class='mdl-checkbox__label'>{$opt}</span>";
		}
		
	}
	elseif($field['type'] == 'radio') {
		$opt = "<span class='mdl-radio__label'>{$opt}</span>";
	}
	
	return $opt;
}
