<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package bb
 */

/* CPT Filters */
//add_action('parse_query', 'bb_request_corrected');
function bb_request_corrected($query) {
	
	if(is_admin())
		return;
	
	if(is_search() && $query->is_main_query()){
		
		$per = get_option('posts_per_page');
		if($per < 25) {
			$query->query_vars['posts_per_page'] = 15; // 25
		}
	}
	
	//var_dump($query->query_vars);
	
	/*if(is_tag() && $query->is_main_query()){
		//var_dump($query->query_vars);
		
		$query->query_vars['post_type'] = array('post', 'event', 'material');
	}
	elseif((is_post_type_archive('element') ) && $query->is_main_query()){
		$query->query_vars['orderby'] = 'menu_order';
		$query->query_vars['order'] = 'ASC';
		
	}
	elseif((is_post_type_archive('member') || is_tax('membercat')) && $query->is_main_query()){
		$query->query_vars['orderby'] = 'meta_value';
		$query->query_vars['meta_key'] = 'brand_name';
		$query->query_vars['order'] = 'ASC';
		$query->query_vars['posts_per_page'] = 24;
	}*/
	
	
} 



/* Custom conditions */
function is_about(){
	global $post;
		
	if(is_page_branch('about'))
		return true;
	
	return false;
}

function is_page_branch($slug){
	global $post;
	
	if(empty($slug))
		return false;
	
		
	if(!is_page())
		return false;
	
	if(is_page($slug))
		return true;
	
	$parents = get_post_ancestors($post);
	$test = get_page_by_path($slug);
	if(in_array($test->ID, $parents))
		return true;
	
	return false;
}


function is_tax_branch($slug, $tax) {
	global $post;
	
	$test = get_term_by('slug', $slug, $tax);
	if(empty($test))
		return false;
	
	if(is_tax($tax)){
		$qobj = get_queried_object();
		if($qobj->term_id == $test->term_id || $qobj->parent == $test->term_id)
			return true;
	}
	
	if(is_singular() && is_object_in_term($post->ID, $tax, $test->term_id))
		return true;
	
	return false;
}


function is_posts() {
	
	if(is_home() || is_category())
		return true;
		
	if(is_post_type_archive('event'))
		return true;
		
	if(is_singular('post') || is_singular('event'))
		return true;
	
	return false;
}

function is_events() {
	
		
	if(is_post_type_archive('event'))
		return true;
		
	if(is_singular('event'))
		return true;
	
	return false;
}

 
 

if ( ! function_exists( 'bb_paging_nav' ) ) :
/**
 * Display paging nav
 */
function bb_paging_nav($query = null) {
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
			$p = bb_paginate_links($query, false);
			if(!empty($p)):
		?>
			<div class="pagination"><?php bb_paginate_links($query); ?></div>
		<?php endif; ?>
		
	</nav><!-- .navigation -->
	<?php
}
endif;


function bb_paginate_links($query = null, $echo = true) {
    global $wp_rewrite, $wp_query, $post;
    
	if(null == $query)
		$query = $wp_query;
	
    //var_dump($wp_query);
	$remove = array(
		's'	
	);
	
	$current = ($query->query_vars['paged'] > 1) ? $query->query_vars['paged'] : 1; 
	
		$parts = parse_url(get_pagenum_link(1));	
		$base = trailingslashit(esc_url($parts['host'].$parts['path']));
		$format = 'page/%#%/';
	
    
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


if ( ! function_exists( 'bb_post_nav' ) ) :
/**
 * Display navigation to next/previous post when applicable.
 */
function bb_post_nav() {
	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	}
	?>
	<nav class="navigation post-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'bb' ); ?></h1>
		<div class="nav-links">
			<?php
				previous_post_link( '<div class="nav-previous">%link</div>', '<span class="meta-nav">&larr;</span>' );
				next_post_link('<div class="nav-next">%link</div>', '<span class="meta-nav">&rarr;</span>');
			?>
		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;


if ( ! function_exists( 'bb_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function bb_posted_on() {
	$pt = get_post_type();
	$cat = '';
	
	
	if('post' == $pt){		
	
		$cat = get_the_term_list(get_the_ID(), 'category', '<span class="category">', ', ', '</span>');
	}
	
	$sep = bb_get_sep();
	
	if(!empty($cat))
		$cat = $sep.strip_tags($cat, '<span>');
?>
	<time class="date"><?php echo esc_html(get_the_date());?></time><?php
	echo $cat;	
}
endif;


function bb_get_sep() {
	
	return "<span class='sep'>//</span>";
}



/**
 * Accessable thumbnail
 **/
function bb_get_post_thumbnail($cpost = null, $size = 'post-thumbnail'){
	global $post;
	
	if(!$cpost)
		$cpost = $post;
		
	$thumb_id = get_post_thumbnail_id($cpost->ID);
	if(!$thumb_id)
		return '';
	
	$att = get_post($thumb_id);
	$att_label = sprintf(__('Thumbnail for - %s', 'svet'), get_the_title($cpost->ID));
	
	$attr = array(
		'alt' => (!empty($att->post_excerpt)) ? $att->post_excerpt : $att_label
	);
	
	return wp_get_attachment_image($thumb_id, $size, false, $attr);
}

/**
 * Breadcrumbs
 **/
function bb_breadcrumbs(){
	global $post;
	
	if(!is_single())
		return '';
		
	$links = array();
	if(is_singular('post')) {
		$p = get_post(get_option('page_for_posts'));
		if($p){
			$links[] = "<a href='".get_permalink($p)."'>".get_the_title($p)."</a>";
			$cat = wp_get_object_terms($post->ID, 'category');
			if(!empty($cat)){
				$links[] = "<a href='".get_term_link($cat[0])."'>".apply_filters('bb_the_title', $cat[0]->name)."</a>";
			}
		}	
	}
	elseif(is_singular('event')) {
		
		$p = get_post(get_option('page_for_posts'));
		if($p){
			$links[] = "<a href='".get_permalink($p)."'>".get_the_title($p)."</a>";
			$pt_link = get_post_type_archive_link('event');
			$pt_name = bb_get_post_type_archive_title('event');
			if(!empty($pt_name)){
				$links[] = "<a href='".$pt_link."'>".$pt_name."</a>";
			}
		}	
	}
	
	$sep = bb_get_sep();
	
	return implode($sep, $links);	
}

function bb_get_post_type_archive_title($post_type) {
	
	$pt_obj = get_post_type_object( $post_type );
	return $pt_obj->labels->name;
}

/**
 * Events meta
 **/

function bb_event_meta($cpost = null) {
	global $post;
		
	if(!$cpost)
		$cpost = $post;
		
	$date = (function_exists('get_field')) ? get_field('event_date', $cpost->ID) : $cpost->post_date;
	$time = (function_exists('get_field')) ? get_field('event_time', $cpost->ID) : '';
	$addr = (function_exists('get_field')) ? get_field('event_address', $cpost->ID) : '';

	if(!empty($date)){
		echo "<div class='em-field'>";
		echo "<span class='label'>".__('Event date', 'bb').":</span>";
		echo "<time class='e-date'>".date('d.m.Y', strtotime($date))."</time>";
		echo "</div>";
	}
	if(!empty($time)){
		echo "<div class='em-field'>";
		echo "<span class='label'>".__('Time', 'bb').":</span>";
		echo "<time class='e-time'>".apply_filters('bb_the_title', $time)."</time>";
		echo "</div>";
	}
	if(!empty($addr)){
		echo "<div class='em-field'>";
		echo "<span class='label'>".__('Address', 'bb').":</span>";
		echo "<time class='e-time'>".apply_filters('bb_the_title', $addr)."</time>";
		echo "</div>";
	}
	

}



