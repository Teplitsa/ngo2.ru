<?php
/**
 * General theme snippets
 */

/**
 * Filters
 **/


/** body classes **/
add_filter('body_class', 'apl_body_classes');
function apl_body_classes( $classes ) {
	global $post;
	
	if(is_admin())
		return $classes;
	
	$classes[] = (function_exists('pll_current_language')) ? pll_current_language() : 'ru';
	
	return $classes;
}




/** wp_title **/
//add_filter( 'wp_title', 'apl_wp_title', 10, 2 );
function apl_wp_title( $title, $sep ) {
	global $page, $paged;
	

	return $title;
}


/* menu filter sceleton */
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
	elseif($args->menu_class =='subnav-menu' && $args->theme_location != 'topics') {
		//current item for single news and speaker
		if(is_singular('material')){
			
			foreach($items as $index => $menu_item){
				if($menu_item->title == __('News', 'apl'))
					$items[$index]->classes[] = 'current-menu-item';
			}
		}
		elseif(is_singular('speaker')){
			foreach($items as $index => $menu_item){
				if($menu_item->title == __('Experts', 'apl'))
					$items[$index]->classes[] = 'current-menu-item';
			}
		}
	}
	elseif($args->menu_class == 'page-menu'){ //current item in in-page nav
		$root = reset($items); //find root		
		
		if(in_array('filer-page-menu', $root->classes)){			
			foreach($items as $index => $menu_item){ 
				if($menu_item->ID == $root->ID){ // is root current
					if(isset($_GET['event_speaker_cat'])) //no way to make it abstract
						$items[$index]->classes = apl_remove_current_class($menu_item->classes);
				}
				
				if($menu_item->type == 'taxonomy'){
					$tax = $menu_item->object;	//taxonomy			
					parse_str(parse_url($menu_item->url, PHP_URL_QUERY), $test); //slug
					$query_key = 'event_'.$tax;
					
					if(isset($test[$tax])) {
						//filter URL
						$menu_item->url = add_query_arg(array( $query_key => esc_attr($test[$tax])), trailingslashit($root->url));
						if(isset($_GET[$query_key]) && $_GET[$query_key] == esc_attr($test[$tax])) //current item
							$items[$index]->classes[] = 'current-menu-item';
					}					
					
				} // type					
			}
			
		}// endif in_array
		
	}
	
	return $items;
}

function apl_remove_current_class($classes){
	
	if(empty($classes))
		return $classes;
	
	foreach($classes as $i => $class){
		if(false !== strpos($class, 'current')){
			unset($classes[$i]);
		}
	}
	
	return $classes;
}


add_filter('wp_nav_menu', 'apl_submenu_classes', 2, 2);
function apl_submenu_classes($output, $args) {
	
	if($args->theme_location == 'primary'){
		
		//$output	= str_replace('sub-menu', 'sub-menu vypadushka', $output);
	}
	
	return $output;
}




/** Custom excerpts  **/

/** more link */
function apl_get_excerpt_with_link($cpost) {
	$more = __('More', 'apl');
	$exerpt = $cpost->post_excerpt;
	if(empty($exerpt))
		return '';
	
	$url = get_permalink($cpost);
	return $exerpt.'&nbsp;<a href="'. esc_url($url) . '"><span class="meta-nav">['.$more.']</span></a>';
}

function alv_continue_reading_link() {
	$more = __('More', 'apl');
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
add_filter( 'get_the_excerpt', 'apl_custom_excerpt_more' );
function apl_custom_excerpt_more( $output ) {
	global $post;
	
	if(is_singular())
		return $output;
	
	$output .= '&nbsp;&rarr;';
	
	return $output;
}


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




/**
 * Helpers and commnot template tags
 **/

if ( ! function_exists( 'apl_paging_nav' ) ) :
/**
 * Display paging nav
 */
function apl_paging_nav($query = null) {
	global $wp_query;
	
	if(!$query)
		$query = $wp_query;
		
	// Don't print empty markup if there's only one page.
	if ($query->max_num_pages < 2 ) {
		return;
	}
?>
	<nav class="navigation paging-navigation" role="navigation">
		
		<?php
			$p = alv_paginate_links($query, false);
			if(!empty($p)):
		?>
			<div class="pagination"><?php alv_paginate_links($query); ?></div>
		<?php endif; ?>
		
	</nav><!-- .navigation -->
	<?php
}
endif;


if ( ! function_exists( 'apl_post_nav' ) ) :
/**
 * Display navigation to next/previous post when applicable.
 */
function apl_post_nav() {

	if('event' == get_post_type())
		return; 
	
	if('speaker' == get_post_type()) {
		
		$next_link = get_next_post_link(
			'<div class="nav-next">%link</div>',
			__('Next', 'apl'), 
			true ,
			'',
			'speaker_cat');
		
		$previous_link = get_previous_post_link(
			'<div class="nav-previous">%link</div>',
			__('Previous', 'apl'), 
			true ,
			'',
			'speaker_cat');
	}
	else {
		$next_link = get_next_post_link(
			'<div class="nav-next">%link</div>',
			'%title'
		);
		
		$previous_link = get_previous_post_link(
			'<div class="nav-previous">%link</div>',
			'%title'
		);

	}

if ( '' !== $next_link || '' !== $previous_link ) : ?>
<nav class="navigation post-navigation" role="navigation">	
	<div class="nav-links">
		<?php
		echo $previous_link;
		echo $next_link;
		?>
	</div>
</nav>
<?php endif;
}
endif;



if ( ! function_exists( 'apl_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function apl_posted_on() {
	
	$pt = get_post_type();
	$cat = '';
	
	
	if('post' == $pt){		
	
		$cat = get_the_term_list(get_the_ID(), 'avtor', '<span class="author">', ', ', '</span>');
	}
	
	$sep = alv_get_sep();
	
	if(!empty($cat))
		$cat = $sep.$cat;
?>
	<time class="date"><?php echo esc_html(get_the_date('d / m / Y'));?></time><?php
	echo $cat;	
}
endif;



function alv_get_sep() {
	
	return "<span class='sep'>&#8226;</span>";
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

function alv_paginate_links($query = null, $echo = true) {
    global $wp_rewrite, $wp_query, $post;
    
	if(null == $query)
		$query = $wp_query;
	
    //var_dump($wp_query);
	$remove = array(
		's',
		'event_speaker_cat'		
	);
	
	$current = ($query->query_vars['paged'] > 1) ? $query->query_vars['paged'] : 1; 
	if(is_singular('event')) {
		$format = '%#%/';
		$base = trailingslashit(get_permalink($post));
	}
	else {
		$parts = parse_url(get_pagenum_link(1));	
		$base = trailingslashit(esc_url($parts['host'].$parts['path']));
		$format = 'page/%#%/';
	}
    
	$pagination = array(
        'base' => $base.'%_%',
        'format' => $format,
        'total' => $query->max_num_pages,
        'current' => $current,
        'prev_text' => '&laquo;',
        'next_text' => '&raquo;',
        'end_size' => 4,
        'mid_size' => 4,
        'show_all' => false,
        'type' => 'plain', //list
		'add_args' => array()
    );
    
	
    if(!empty($query->query_vars['s']))
        $pagination['add_args'] = array('s' => str_replace(' ', '+', get_search_query()));
	
	foreach($remove as $param){
		if($param == 's')
			continue;
		
		if(isset($_GET[$param]) && !empty($_GET[$param]))
			$pagination['add_args'] = array_merge($pagination['add_args'], array($param => esc_attr(trim($_GET[$param]))));
	}
		
		    
    if($echo)
		echo paginate_links($pagination);
	return
		paginate_links($pagination);
}


/**
 * Sharing buttons
 **/
function apl_page_actions(){


?>
<div class="sharing">
<script type="text/javascript">(function() {
  if (window.pluso)if (typeof window.pluso.start == "function") return;
  if (window.ifpluso==undefined) { window.ifpluso = 1;
    var d = document, s = d.createElement('script'), g = 'getElementsByTagName';
    s.type = 'text/javascript'; s.charset='UTF-8'; s.async = true;
    s.src = ('https:' == window.location.protocol ? 'https' : 'http')  + '://share.pluso.ru/pluso-like.js';
    var h=d[g]('body')[0];
    h.appendChild(s);
  }})();</script>
<div class="pluso" data-background="transparent" data-options="medium,square,line,horizontal,counter,theme=04" data-services="facebook,twitter,vkontakte,google,livejournal"></div>
</div>
<?php
}


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




/**
 * Support for social icons in menu
 **/
add_filter( 'pre_wp_nav_menu', 'alv_pre_wp_nav_menu_social', 10, 2 );
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

/** extract posts IDs from query **/
function apl_get_posts_ids_from_query($query){
	
	$ids = array();
	if(!$query->have_posts())
		return $ids;
	
	foreach($query->posts as $qp){
		$ids[] = $qp->ID;
	}
	
	return $ids;
}

