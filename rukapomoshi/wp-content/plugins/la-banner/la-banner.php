<?php
/*
Plugin Name: LA Banners
Description: This creates basic banner functionality for site
Version: 1.2
Author: Teplitsa
Author URI: http://te-st.ru/
Contributors:
	Lev Zvyagincev aka Ahaenor (ahaenor@gmail.com)
	Anna Ladoshkina aka foralien (webdev@foralien.com)

License: GPLv2 or later
	
Chnagelog
1.2 - common banners template refactored
1.1- bugfixes in widget
*/

if( !defined('ABSPATH') ) exit; // Exit if accessed directly
load_plugin_textdomain('la-banner', false, '/la-banner/languages');

/* Get core instance */
function la_banner_core(){
	
	if(class_exists('La_Banner_Core'))
		return La_Banner_Core::get_instance();
}

/** Init **/
require_once(plugin_dir_path(__FILE__).'includes/la-banner-core.php');
$la_banner = la_banner_core();







?>