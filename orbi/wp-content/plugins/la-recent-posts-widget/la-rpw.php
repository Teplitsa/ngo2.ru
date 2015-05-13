<?php
/*
Plugin Name: LA Recent Posts Widget
Description: Enables recent posts widget with more (sic!) advanced settings.
Version: 0.7
Author: Teplitsa
Author URI: http://te-st.ru/
License: GPLv2 or later
Contributors:
    Lev Zvyagincev aka ahaenor (ahaenor@gmail.com)
	Anna Ladoshkina aka foralien (webdev@foralien.com)


Сhangelog
0.7 - no default styles - they go into theme
0.6 - markup abstraction
0.5 - remove some overhelming CSS related fields
0.4 - tuning for filters parameters
0.3 - re-arrange field order in widget settings
0.2 - don't display selflink on singular pages
*/

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

load_plugin_textdomain('la-rpw', false, dirname(plugin_basename(__FILE__)).'/lang/');
require_once(plugin_dir_path(__FILE__).'inc/class-recent-posts-widget.php');

add_action('widgets_init', function(){
    register_widget('La_Recent_Posts_Widget');
});