<?php
/*
Plugin Name: LA relevant search
Description: This plugin extends WP site's default search to use stemmer class.
Version: 1.1
Author: Teplitsa
Author URI: http://te-st.ru/
License: GPLv2 or later
Contributors:
    Lev Zvyagincev aka ahaenor (ahaenor@gmail.com)
	Anna Ladoshkina aka foralien (webdev@foralien.com)

License: GPLv2 or later

Сhangelog
1.1 - some minor improvements in code style
1.0 - initial 
*/

if( !defined('ABSPATH') ) exit; // Exit if accessed directly

load_plugin_textdomain('la-rs', false, '/la-relevant-search/lang');

require(plugin_dir_path(__FILE__).'stemmer.php');

$stemmer = new La_Search_Stemmer(array());
$stemmer->plug();

/**
 * This function should be in a loop
 **/
function la_rs_loop_entry() {
    global $post;

    if(empty($post))
        return;
    $permalink = get_permalink($post->ID);
	$pt = esc_attr($post->post_type);
	
	$meta = apply_filters('la_rs_search_item_meta', '', $post);
	if(!empty($meta))
		$meta = ' | '.$meta;?>

    <article class="clearfix search-item <?php echo $pt;?>">
        <h4 class="item-title">
            <a href="<?php echo $permalink;?>" rel="bookmark"><?php the_title();?></a>
			<?php echo $meta; ?>
        </h4>
        <cite><?php echo $permalink;?></cite>
        <div class="item-summary"><?php the_excerpt();?></div>
    </article>
<?php }

function la_rs_search_results_form($query) {?>
    <div class="search-holder">
        <?php get_search_form();?>
        <div class="res-count">
            <?php echo __('Results found:', 'la-rs');?> <b><?php echo (int)$query->found_posts;?></b>
        </div>
    </div>
<?php }




