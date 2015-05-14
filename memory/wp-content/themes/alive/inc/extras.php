<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package alv
 */



/** Default filters **/
add_filter( 'alv_the_content', 'wptexturize'        );
add_filter( 'alv_the_content', 'convert_smilies'    );
add_filter( 'alv_the_content', 'convert_chars'      );
add_filter( 'alv_the_content', 'wpautop'            );
add_filter( 'alv_the_content', 'shortcode_unautop'  );
add_filter( 'alv_the_content', 'do_shortcode' );

add_filter( 'alv_the_title', 'wptexturize'   );
add_filter( 'alv_the_title', 'convert_chars' );
add_filter( 'alv_the_title', 'trim'          );

/* jpeg compression */
add_filter( 'jpeg_quality', create_function( '', 'return 90;' ) );

 
/** Custom excerpts  **/

/** more link */
function alv_get_excerpt_with_link($cpost) {
	$more = __('More', 'alv');
	$exerpt = $cpost->post_excerpt;
	if(empty($exerpt))
		return '';
	
	$url = get_permalink($cpost);
	return $exerpt.'&nbsp;<a href="'. esc_url($url) . '"><span class="meta-nav">['.$more.']</span></a>';
}

function alv_continue_reading_link() {
	$more = '&rarr;';
	return '&nbsp;<a href="'. esc_url( get_permalink() ) . '"><span class="meta-nav">['.$more.']</span></a>';
}

/** excerpt filters  */
add_filter( 'excerpt_more', 'alv_auto_excerpt_more' );
function alv_auto_excerpt_more( $more ) {
	return '&hellip;';
}

add_filter( 'excerpt_length', 'alv_custom_excerpt_length' );
function alv_custom_excerpt_length( $l ) {
	return 30;
}

/** inject */
//add_filter( 'get_the_excerpt', 'alv_custom_excerpt_more' );
function alv_custom_excerpt_more( $output ) {
	global $post;
	
	if(is_singular())
		return $output;
	
	$output .= alv_continue_reading_link();	
	return $output;
}

/**
 * Current URL
 **/
if(!function_exists('alv_current_url')){
function alv_current_url() {
   
    $pageURL = 'http';
   
    if (isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] == "on")) {$pageURL .= "s";}
    $pageURL .= "://";
   
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
   
    return $pageURL;
}
}

/** extract posts IDs from query **/
function alv_get_posts_ids_from_query($query){
	
	$ids = array();
	if(!$query->have_posts())
		return $ids;
	
	foreach($query->posts as $qp){
		$ids[] = $qp->ID;
	}
	
	return $ids;
}


/** Copyright string */
function alv_dynamic_copyright($start_year, $name){
	
	$start = intval($start_year);
	if($start == 0 || empty($name))
		return '';
		
	$end = date('Y', time());
	$copy = "&copy; $start";
	
	if($end != $start)
		$copy .= " - $end";
	
	$copy .= ". <span>".$name."</span>";
	
	return $copy;
}

function alv_design_by(){
	
	$designby = "<span>@<a href='http://www.foralien.com/en/' target='_blank'>foralien bureau</a></span>";
	printf(__('Made by %s', 'alv'), $designby);
	
}


/**
 * Inject  top link  HTML
 * require <body> tag to have id ='top'
 **/
function alv_print_top_link(){

	if(!is_admin()):
 ?>	
	<div id="top-link">
		<a href="#top"><?php _e('On top', 'apl');?></a>
	</div>
	
<?php endif; 
}
add_action('wp_footer', 'alv_print_top_link');


/**
 * Favicon
 **/
function alv_favicon(){
	
	$favicon_test = WP_CONTENT_DIR. '/favicon.ico'; //in the root not working don't know why
    if(!file_exists($favicon_test))
        return;
        
    $favicon = content_url('favicon.ico');
	echo "<link href='{$favicon}' rel='shortcut icon' type='image/x-icon' >";
}
add_action('wp_enqueue_scripts', 'alv_favicon', 1);
add_action('admin_enqueue_scripts', 'alv_favicon', 1);
add_action('login_enqueue_scripts', 'alv_favicon', 1);


/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function alv_body_classes( $classes ) {
	

	return $classes;
}
//add_filter( 'body_class', 'alv_body_classes' );


/**
 * Menu filter sceleton
 **/
//add_filter('wp_nav_menu_objects', 'apl_clear_menu_item_classes', 2, 2);
function apl_clear_menu_item_classes($items, $args){			
	
	if(empty($items))
		return;	
	
	//var_dump($args);
	if($args->menu_class =='primary-menu'){
		if(is_conference()) { //current position for conference items
			foreach($items as $index => $menu_item){
				if(in_array('conference-item', $menu_item->classes))
					$items[$index]->classes[] = 'current-menu-item';
			}
		}
	}
	
	
	return $items;
}


/**
 * Support for social icons in menu
 * needs font awesome
 * 
 **/
//add_filter( 'pre_wp_nav_menu', 'alv_pre_wp_nav_menu_social', 10, 2 );
function alv_pre_wp_nav_menu_social( $output, $args ) {
	if ( ! $args->theme_location || 'social' !== $args->theme_location ) {
		return $output;
	}

	// Get the menu object
	$locations = get_nav_menu_locations(); 
	$menu      = (isset($locations[ $args->theme_location ])) ? wp_get_nav_menu_object( $locations[ $args->theme_location ] ) : false;

	if ( ! $menu || is_wp_error( $menu ) ) {
		return $output;
	}

	$output = '';

	// Get the menu items
	$menu_items = wp_get_nav_menu_items( $menu->term_id, array( 'update_post_term_cache' => false ) );

	// Set up the $menu_item variables
	_wp_menu_item_classes_by_context( $menu_items );

	// Sort the menu items
	$sorted_menu_items = array();
	foreach ( (array) $menu_items as $menu_item ) {
		$sorted_menu_items[ $menu_item->menu_order ] = $menu_item;
	}

	unset( $menu_items, $menu_item );

	// Supported social icons (filterable); [url pattern] => [css class]
	$supported_icons = apply_filters( 'alv_supported_social_icons', array(
		'app.net'            => 'fa-adn',
		'behance.net'        => 'fa-behance',
		'bitbucket.org'      => 'fa-bitbucket',
		'codepen.io'         => 'fa-codepen',
		'delicious.com'      => 'fa-delicious',
		'deviantart.com'     => 'fa-deviantart',
		'digg.com'           => 'fa-digg',
		'dribbble.com'       => 'fa-dribbble',
		'facebook.com'       => 'fa-facebook',
		'flickr.com'         => 'fa-flickr',
		'foursquare.com'     => 'fa-foursquare',
		'github.com'         => 'fa-github',
		'gittip.com'         => 'fa-gittip',
		'plus.google.com'    => 'fa-google-plus-square',
		'instagram.com'      => 'fa-instagram',
		'jsfiddle.net'       => 'fa-jsfiddle',
		'linkedin.com'       => 'fa-linkedin',
		'pinterest.com'      => 'fa-pinterest',
		'qzone.qq.com'       => 'fa-qq',
		'reddit.com'         => 'fa-reddit',
		'renren.com'         => 'fa-renren',
		'soundcloud.com'     => 'fa-soundcloud',
		'spotify.com'        => 'fa-spotify',
		'stackexchange.com'  => 'fa-stack-exchange',
		'stackoverflow.com'  => 'fa-stack-overflow',
		'steamcommunity.com' => 'fa-steam',
		'stumbleupon.com'    => 'fa-stumbleupon',
		't.qq.com'           => 'fa-tencent-weibo',
		'trello.com'         => 'fa-trello',
		'tumblr.com'         => 'fa-tumblr',
		'twitter.com'        => 'fa-twitter',
		'vimeo.com'          => 'fa-vimeo-square',
		'vine.co'            => 'fa-vine',
		'vk.com'             => 'fa-vk',
		'weibo.com'          => 'fa-weibo',
		'weixin.qq.com'      => 'fa-weixin',
		'wordpress.com'      => 'fa-wordpress',
		'xing.com'           => 'fa-xing',
		'yahoo.com'          => 'fa-yahoo',
		'youtube.com'        => 'fa-youtube',
		'feed'               => 'fa-rss',
		'odnoklassniki.ru'   => 'icon-odnoklassniki'
	));

	// Process each menu item
	foreach ( $sorted_menu_items as $item ) {
		$item_output = '';

		// Look for matching icons
		foreach ( $supported_icons as $pattern => $class ) {
			if ( false !== strpos( $item->url, $pattern ) ) {
				$item_output .= '<li class="' . esc_attr( str_replace( array('fa-', 'icon-'), '', $class ) ) . '">';
				$item_output .= '<a href="' . esc_url( $item->url ) . '">';				
				$item_output .= '<i class="fa fa-fw ' .esc_attr($class). '">';
				$item_output .= '<span>' . esc_html( $item->title ) . '</span>';
				$item_output .= '</i></a></li>';
				break;
			}
		}

		// No matching icons
		if ( '' === $item_output ) {
			$item_output .= '<li class="external-link-square">';
			$item_output .= '<a href="' . esc_url( $item->url ) . '">';
			$item_output .= '<i class="fa fa-fw fa-external-link-square">';
			$item_output .= '<span>' . esc_html( $item->title ) . '</span>';
			$item_output .= '</i></a></li>';
		}

		// Add item to list
		$output .= $item_output;
		unset( $item_output );
	}

	// If there are menu items, add a wrapper
	if ( '' !== $output ) {
		$output = '<ul class="' . esc_attr( $args->menu_class ) . '">' . $output . '</ul>';
	}

	return $output;
}


/* deregister taxonomy for object */
if(!function_exists('deregister_taxonomy_for_object_type')):
function deregister_taxonomy_for_object_type( $taxonomy, $object_type) {
	global $wp_taxonomies;

	if ( !isset($wp_taxonomies[$taxonomy]) )
		return false;

	if ( ! get_post_type_object($object_type) )
		return false;
	
	foreach($wp_taxonomies[$taxonomy]->object_type as $index => $object){
		
		if($object == $object_type)
			unset($wp_taxonomies[$taxonomy]->object_type[$index]);
	}
	
	return true;
}
endif;


add_filter('wpseo_title', 'hmap_wpseo_title_correction');
function hmap_wpseo_title_correction($title){
	
	//if(is_singular('attack')){		
	//	$a_title = hmap_attack_title();
	//	$title = str_replace('[[attack_title]]', $a_title, $title);
	//}
	
	return $title;
}

/** settings **/
add_action('init', 'alv_setup_inits', 100);
function alv_setup_inits(){
	
	deregister_taxonomy_for_object_type('post_tag', 'post'); 
}

