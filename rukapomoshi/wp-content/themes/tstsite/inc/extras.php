<?php
/**
 * General theme snippets
 */



/**
 * Adds custom classes to the array of body classes.
 */

add_filter('body_class', 'tst_body_classes');
function tst_body_classes( $classes ) {
	

	return $classes;
}



/**
 * Filters wp_title to print a neat <title> tag based on what is being viewed.
 * remove filter when Yoas SEO is active
 */

add_filter( 'wp_title', 'tst_wp_title', 10, 2 );
function tst_wp_title( $title, $sep ) {
	global $page, $paged;

	if ( is_feed() )
		return $title;

	// Add the blog name
	$title .= get_bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title .= " $sep $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		$title .= " $sep " . sprintf('Стр. %s', max( $paged, $page ) );

	return $title;
}


/* menu filter sceleton */
//add_filter('wp_nav_menu_objects', 'frl_clear_menu_item_classes', 2, 2);
function frl_clear_menu_item_classes($items, $args){		
	global $sections;
	
	if(empty($items))
		return;	
	
	$tops = array(); 
	if($args->theme_location == 'primary' && empty($sections['labels'])){
		foreach($items as $index => $menu_item){
			//clear mess - remove everythind except any current mark
			
			if(!empty($menu_item->classes)){
				
				foreach($menu_item->classes as $i => $iclass){
					
					
				}
				
			}
			
		}		
		
	}
		
	return $items;
}



/**
 * Thumbnails &metas  filters sceletons
 **/
add_filter('lpg_thumbnail_size', 'tst_lpg_thumbnail_size');
function tst_lpg_thumbnail_size($size){
	
	return 'post-thumbnail'; 
}

add_filter('la_rpw_thumbnail_size', 'tst_la_rpw_thumbnail_size', 2, 3);
function tst_la_rpw_thumbnail_size($size,  $post, $instance){
	
	
	return $size; 
}

add_filter('la_rpw_post_meta', 'tst_la_rpw_post_meta', 2, 2);
function tst_la_rpw_post_meta($meta, $post) {
	
	if(!empty($meta)){
		$meta = tst_related_item_meta($post);
	}
	
	return $meta;
}

add_filter('la_rs_search_item_meta', 'tst_search_meta', 2, 2);
function tst_search_meta($meta, $post){
	
	return $meta;
	
}




/**
 * Custom excerpts
 **/

/** more link */
function frl_continue_reading_link() {
	return '&nbsp;<a href="'. esc_url( get_permalink() ) . '"><span class="meta-nav">[&raquo;]</span></a>';
}

/** excerpt filters  */
add_filter( 'excerpt_more', 'frl_auto_excerpt_more' );
function frl_auto_excerpt_more( $more ) {
	return '&hellip;';
}

add_filter( 'excerpt_length', 'frl_custom_excerpt_length' );
function frl_custom_excerpt_length( $l ) {
	return 35;
}

/** inject */
add_filter( 'get_the_excerpt', 'frl_custom_excerpt_more' );
function frl_custom_excerpt_more( $output ) {
	if (is_search())
		$output .= '&nbsp;[&raquo;]';
	else
		$output .= frl_continue_reading_link();
	
	return $output;
}


if ( ! function_exists( 'tst_content_nav' ) ) :
/**
 * Display navigation to next/previous pages when applicable
 */
function tst_content_nav( $nav_id, $query = null ) {
	global $wp_query, $post;

	$nav_class = ( is_single() ) ? 'navigation-post cf' : 'navigation-paging';
	?>
	<nav role="navigation" id="<?php echo esc_attr( $nav_id ); ?>" class="<?php echo $nav_class; ?>">		

	<?php if ( is_single() ) : // navigation links for single posts ?>
	
	<?php frl_page_actions();?>
	
	<div class="nextprev">
		<?php
			if($post->post_type == 'event'){
				tst_event_nextprev();
			}
			else {
				
				previous_post_link( '<span class="nav-previous">%link</span>', __('&laquo; prev.', 'tst') );
				next_post_link('<span class="nav-next">%link</span>', __('next. &raquo;', 'tst') );
			}
		 ?>		
	</div>
	<?php else : // pagination ?>

		<?php
			$p = frl_paginate_links($query, false);
			if(!empty($p)):
		?>
			<div class="pagination"><?php frl_paginate_links($query); ?></div>
		<?php endif; ?>

	<?php endif; ?>

	</nav>
	<?php
}
endif; 


if ( ! function_exists( 'tst_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */

function tst_posted_on() {
	
	$pt = get_post_type();
	
	if('event' == $pt){
		tst_event_undertitle_meta();
		return;
	}
	/*elseif('media' == $pt) {
		$cat = get_the_term_list(get_the_ID(), 'mediatype', '<span class="category">', ', ', '</span>');
	}*/
	else {
		$cat = get_the_term_list(get_the_ID(), 'category', '<span class="category">', ', ', '</span>');
	}
	
	$sep = frl_get_sep();
	
	if(!empty($cat))
		$cat = ' '.$sep.' '.$cat;
?>	
	<time class="entry-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html( get_the_date() );?></time>
	<?php echo $cat;

}
endif;


function frl_get_sep() {
	
	return "<span class='sep'>//</span>";
}

/**
 * Inject  top link  HTML
 * require <body> tag to have id ='top'
 **/
function frl_print_top_link(){

	if(!is_admin()):
 ?>	
	<div id="top-link">
		<a href="#top"><?php _e('Top', 'tst');?></a>
	</div>
	
<?php endif; 
}
add_action('wp_footer', 'frl_print_top_link');


function frl_dynamic_copyright($start_year, $name){
	
	$start = intval($start_year);
	if($start == 0 || empty($name))
		return;
		
	$end = date('Y', time());
	$copy = "&copy; $start";
	
	if($end != $start)
		$copy .= " - $end";
	
	$copy .= ". <strong>".$name."</strong>";
	
	return $copy;
}


function frl_paginate_links($query = null, $echo = true) {
    global $wp_rewrite, $wp_query;
    
	if(null == $query)
		$query = $wp_query;
	
    //var_dump($wp_query);
	$remove = array(
		's'		
	);
	
	$current = ($query->query_vars['paged'] > 1) ? $query->query_vars['paged'] : 1;
	$parts = parse_url(get_pagenum_link(1));	
	$base = trailingslashit(esc_url($parts['host'].$parts['path']));
	
    
	$pagination = array(
        'base' => $base.'%_%',
        'format' => 'page/%#%/',
        'total' => $query->max_num_pages,
        'current' => $current,
        'prev_text' => __('&laquo; prev.', 'tst'),
        'next_text' => __('next. &raquo;', 'tst'),
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
function frl_page_actions(){
?>
<div class="share-buttons">
<script type="text/javascript">(function() {
  if (window.pluso) if (typeof window.pluso.start == "function") return;
  var d = document, s = d.createElement('script'), g = 'getElementsByTagName';
  s.type = 'text/javascript'; s.charset='UTF-8'; s.async = true;
  s.src = ('https:' == window.location.protocol ? 'https' : 'http')  + '://share.pluso.ru/pluso-like.js';
  var h=d[g]('head')[0] || d[g]('body')[0];
  h.appendChild(s);
})()
</script>
<div class="pluso" data-background="transparent" data-options="small,square,line,horizontal,nocounter,theme=01" data-services="facebook,vkontakte,twitter,google,email"></div>
</div>
<?php
}


/**
 * Favicon
 **/
function frl_favicon(){
	
	$favicon_test = WP_CONTENT_DIR. '/favicon.ico'; //in the root not working don't know why
    if(!file_exists($favicon_test))
        return;
        
    $favicon = content_url('favicon.ico');
	echo "<link href='{$favicon}' rel='shortcut icon' type='image/x-icon' >";
}
add_action('wp_enqueue_scripts', 'frl_favicon', 1);
add_action('admin_enqueue_scripts', 'frl_favicon', 1);
add_action('login_enqueue_scripts', 'frl_favicon', 1);


/**
 * cycloneslider output
 **/
function frl_cycloneslider($slider_id) {
	
	if(function_exists('cyclone_slider'))
		cyclone_slider($slider_id); 	
}

/**
 * Breadcrumbs
 **/
function frl_breadcrumbs(){

	if ( function_exists('yoast_breadcrumb') ) 
		yoast_breadcrumb('<div class="breadcrumbs">','</div>');
}



/**
 * TRanslit in filenames
 **/

add_action('sanitize_file_name', 'frl_translit_sanitize', 0);
function frl_translit_sanitize($title){
		
	$rtl_translit = array (
		"Є"=>"YE","І"=>"I","Ѓ"=>"G","і"=>"i","№"=>"#","є"=>"ye","ѓ"=>"g",
		"А"=>"A","Б"=>"B","В"=>"V","Г"=>"G","Д"=>"D",
		"Е"=>"E","Ё"=>"YO","Ж"=>"ZH",
		"З"=>"Z","И"=>"I","Й"=>"J","К"=>"K","Л"=>"L",
		"М"=>"M","Н"=>"N","О"=>"O","П"=>"P","Р"=>"R",
		"С"=>"S","Т"=>"T","У"=>"U","Ф"=>"F","Х"=>"KH",
		"Ц"=>"TS","Ч"=>"CH","Ш"=>"SH","Щ"=>"SHH","Ъ"=>"'",
		"Ы"=>"Y","Ь"=>"","Э"=>"E","Ю"=>"YU","Я"=>"YA",
		"а"=>"a","б"=>"b","в"=>"v","г"=>"g","д"=>"d",
		"е"=>"e","ё"=>"yo","ж"=>"zh",
		"з"=>"z","и"=>"i","й"=>"j","к"=>"k","л"=>"l",
		"м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
		"с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"kh",
		"ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"shh","ъ"=>"",
		"ы"=>"y","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya","«"=>"","»"=>"","—"=>"-"
	);

	return strtr($title, $rtl_translit);	
}

/**
 * Annotation for post formats
 **/
 
function frl_single_split_content($post) {
	
	$parts = array();
	if(!empty($post->post_excerpt)){
		$parts['intro'] = $post->post_excerpt;
		$parts['content'] = $post->post_content;
		
	}
	else {
		$parts['intro'] = frl_trim_2_phrases($post->post_content);
		$parts['content'] = trim(str_replace($parts['intro'], '', $post->post_content));
	}
	
	return $parts;
}

function frl_trim_2_phrases($text){
		
	$sentences = preg_split('/(?<=[.?!])\s+(?=[a-я])/i', $text);
	if(empty($sentences) || count($sentences) < 2 )
		return $text;
	
	return $sentences[0].' '.$sentences[1];
}

/**
 * Default filters
 **/

add_filter( 'frl_the_content', 'wptexturize'        );
add_filter( 'frl_the_content', 'convert_smilies'    );
add_filter( 'frl_the_content', 'convert_chars'      );
add_filter( 'frl_the_content', 'wpautop'            );
add_filter( 'frl_the_content', 'shortcode_unautop'  );
add_filter( 'frl_the_content', 'do_shortcode' );

/* jpeg compression */
add_filter( 'jpeg_quality', create_function( '', 'return 85;' ) );

/**
 * Plugins compatibility
 **/

/**
 * Compatibility with Members plugin to build correct caps list
 **/
add_filter( 'members_get_capabilities', 'frl_cabalilities_list' );
function frl_cabalilities_list($caps){
	
	$cpt_caps = frl_get_default_cpt_capabilities();
	$ct_caps = frl_get_default_tax_capabilities();
	
	$full = array_merge($cpt_caps, $ct_caps, $caps);
	
	
	return array_unique($full);
}

function frl_get_default_cpt_capabilities(){
		
	$caps = array();
	$post_types = get_post_types(array(), 'objects');
	
	if(empty($post_types))
		return;
	
	foreach($post_types as $post_type => $post_object){ 
		if($post_object->capability_type != 'post' && $post_object->capability_type != 'page'){
				
			
			if(!empty($post_object->cap)){ foreach($post_object->cap as $cap){
				
				$caps[] = $cap;
			}}
			
			sort($caps);
		}
	}
	
	return $caps;
}
	    
    
function frl_get_default_tax_capabilities(){
	
	
	$ct_caps = array();
	$taxes = get_taxonomies(array(), 'objects');
	if(empty($taxes))
		return $ct_caps;
	
	foreach($taxes as $tax_name => $tax_object){
		
		$tax_cap = $tax_object->cap;
		if(!empty($tax_cap)){ foreach($tax_cap as $cap) {			
			$ct_caps[] = $cap;
		}}
	}
	
	sort($ct_caps);
	return array_unique($ct_caps);
}


/**
* remove SEO columns
**/
add_action('admin_init', function(){
	foreach(get_post_types(array('public' => true), 'names') as $pt) {
		add_filter('manage_' . $pt . '_posts_columns', 'frl_clear_seo_columns', 100);
	}	
}, 100);

function frl_clear_seo_columns($columns){

	if(isset($columns['wpseo-score']))
		unset($columns['wpseo-score']);
	
	if(isset($columns['wpseo-title']))
		unset($columns['wpseo-title']);
	
	if(isset($columns['wpseo-metadesc']))
		unset($columns['wpseo-metadesc']);
	
	if(isset($columns['wpseo-focuskw']))
		unset($columns['wpseo-focuskw']);
	
	return $columns;
}

/**
 * No default thumbnails on pages
 **/
add_filter( 'dfi_thumbnail_html', 'tst_dfi_on_pages', 2, 3);
function tst_dfi_on_pages($html, $post_id, $default_thumbnail_id) {
	
	$pt = get_post_type($post_id); 
	if($pt != 'page' && $pt != 'leyka_campaign')
		return $html;
	
	$thumb_id = get_post_thumbnail_id($post_id); 
	if($thumb_id == $default_thumbnail_id)
		$html = '';
	
	return $html;
}


/**
 * Common shortcodes and widgets
 **/


//* attachments shortcodes */
add_shortcode('frl_better_attachments', 'frl_better_attachments_screen');
function frl_better_attachments_screen($atts){
	global $post;
	
	extract(shortcode_atts( array(
		'format' => 'download',
		'num' => -1
	), $atts ));
	
	if(!function_exists('wpba_get_attachments'))
		return '';
	
	$attachments = frl_get_attachments($post->ID, $num);
	
	if(empty($attachments))
		return '';
	
	$callback = 'frl_attachments_'.$format;
	if(is_callable($callback))
		$out = call_user_func($callback, $attachments);
		
	return "<div class='frl-batts'>{$out}</div>";
}

function frl_get_attachments($post_id, $num = -1){
	
	if($num == 0)
		$num = -1;
	
	$att = new WP_Query(array(
		'post_type' => 'attachment',
		'post_status' => 'inherit',
		'posts_per_page' => intval($num),
		'post_parent' => $post_id
	));
	
	return $att->posts;
}

function frl_attachments_image($attachments){
	
	$list = array();
	$gid = uniqid();
	
	foreach($attachments as $i => $att){
		$img = wp_get_attachment_image($att->ID, 'cover');
		$link = wp_get_attachment_url($att->ID);
		$title = apply_filters('the_title', $att->post_title);
		$title_attr = esc_attr($title);
		$desc = apply_filters('the_content', $att->post_excerpt);
		
		$list[$i] = "<article class='frl-batt image cf'>";
		$list[$i] .= "<div class='batt-preview'>";
		$list[$i] .= "<a data-fresco-group='{$gid}' href='{$link}' data-fresco-caption='{$title_attr}' rel='image-overlay' class='img-padder fresco'>{$img}</a></div>";
		$list[$i] .= "<div class='batt-data'><h4>{$title}</h4>{$desc}</div>";
		$list[$i] .= "</article>";		
	}
	
	return implode('', $list);
}

function frl_attachments_download($attachments){
	
	$list = array();
	$gid = uniqid();
	
	foreach($attachments as $i => $att){		
		$img = "<span class='dashicons dashicons-format-aside'></span>";
		$link = wp_get_attachment_url($att->ID);
		$title = apply_filters('the_title', $att->post_title);
		$title_attr = esc_attr($title);
		$desc = apply_filters('frl_the_content', $att->post_excerpt);
		
		$list[$i] = "<article class='frl-batt downlaod cf'>";
		$list[$i] .= "<div class='batt-preview'>{$img}</div>";
		$list[$i] .= "<div class='batt-data'>";
		$list[$i] .= "<h4>{$title}</h4>{$desc}";
		$list[$i] .= "<p class='download'><a href='{$link}' target='_blank'>Скачать &raquo;</a></p></div>";
		$list[$i] .= "</article>";		
	}
	
	return implode('', $list);
}


/**
 * Custom query shortcode
 **/
add_shortcode('frl_query', 'frl_query_screen');
function frl_query_screen($atts){
	global $wp_query;
	
	extract( shortcode_atts( array(
		'q' => '',
		'paging' => 0,
		'format' => 'content',
		'css' => ''
	), $atts ) );
	
	$q = str_replace('+', '&', $q);
		
	//on singlural pages page=2 qv detects paging
	if(isset($wp_query->query_vars['paged']) && $wp_query->query_vars['paged'] > 0)
		$q .= "&paged=".$wp_query->query_vars['paged'];
	elseif(isset($wp_query->query_vars['page']) && $wp_query->query_vars['page'] > 0)
		$q .= "&page=".$wp_query->query_vars['page']."&paged=".$wp_query->query_vars['page'];
	
	
	
	$query = new WP_Query($q);
	if(!$query->have_posts())
		return '';
	
	$out = "";
	
	ob_start();
	echo "<div class='query-loop cf'>";
	while($query->have_posts()): $query->the_post();
		get_template_part($format);
	endwhile; wp_reset_postdata();
	
	if($paging){
		echo "<div class='pagination'>";
		frl_paginate_links($query);
		echo "</div>";
	}
	
	echo "</div>";
	$out = ob_get_contents();
	ob_end_clean();
	
	$css = esc_attr($css);
	return  "<div class='frl-query {$css}'>{$out}</div>";
}


/**
 * Widgets
 **/
function tst_custom_widgets(){

	unregister_widget('WP_Widget_Pages');
	unregister_widget('WP_Widget_Archives');
	unregister_widget('WP_Widget_Calendar');
	unregister_widget('WP_Widget_Meta');
	unregister_widget('WP_Widget_Categories');
	unregister_widget('WP_Widget_Recent_Posts');
	unregister_widget('WP_Widget_Tag_Cloud');
	//unregister_widget('WP_Widget_Search');
	unregister_widget('FrmListEntries');
	
	register_widget('TST_Related_Widget');
	register_widget('TST_Upcoming_Widget');
}
add_action('widgets_init', 'tst_custom_widgets', 11);

