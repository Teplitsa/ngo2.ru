<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package bb
 */

/* CPT Filters */
//add_action('parse_query', 'tst_request_corrected');
function tst_request_corrected($query) {
	
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
	
		
	if(is_singular('post'))
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

function tst_has_bottombar(){
	
	if(is_singular('post')){
		return false;
	}
	elseif(is_home() || is_category()){
		return false;
	}
	
	return true;
}

/** Menu filter sceleton **/
add_filter('wp_nav_menu_objects', 'tst_custom_menu_items', 2, 2);
function tst_custom_menu_items($items, $args){			
	
	if(empty($items))
		return;	
	
	//var_dump($args);
	if($args->theme_location =='primary'){
		
		foreach($items as $index => $menu_item){
			if(in_array('current-menu-item', $menu_item->classes))
				$items[$index]->classes[] = 'active';
		}
	}
	
	
	return $items;
}
 


/** Display paging nav **/
function tst_paging_nav($query = null) {
	global $wp_query;
	
	if(!$query)
		$query = $wp_query;
	
	
	// Don't print empty markup if there's only one page.
	if ($query->max_num_pages < 2 ) {
		return;
	}
		
	$p = tst_load_more_link($query, false);
	//if(!$p){
	//	$p = "<a href='".get_pagenum_link(1)."'>".__('More entries', 'tst')."</a>";
	//}
	
	if(!empty($p)) {
?>
	<div class="paging-navigation"><?php echo $p;?></div>
<?php }
}

function tst_load_more_link($query = null, $echo = true) {
	global $wp_query;
	
	if(!$query)
		$query = $wp_query;
	
	$l = get_next_posts_link(__('More entries', 'tst'), $query->max_num_pages);
	
	if($echo){
		echo $l;
	}
	else {
		return $l;
	}	
}

function tst_paginate_links($query = null, $echo = true) {
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
        'prev_text' => '<i class="mdi-navigation-chevron-left"></i>',
        'next_text' => '<i class="mdi-navigation-chevron-right"></i>',
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


/** Display navigation to next/previous post when applicable. **/
function tst_post_nav() {
	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	}
?>	
	<div class="nav-links post-navigation">
	<?php
		previous_post_link( '%link', '<i class="mdi-image-navigate-before"></i>' );
		next_post_link('%link', '<i class="mdi-image-navigate-next"></i>');
	?>
	</div><!-- .nav-links -->	
<?php
}


/** HTML with meta information for the current post-date/time and author **/
function tst_posted_on($cpost = null) {
	global $post;
		
	if(!$cpost)
		$cpost = $post;
		
	$meta = array();
	
	if('post' == $cpost->post_type){
		$label = __('in the category', 'tst');
		$meta[] = "<time class='date'>".esc_html(get_the_date('d.m.Y', $cpost))."</time>";
		$meta[] = get_the_term_list(get_the_ID(), 'category', '<span class="category">'.$label.' ', ', ', '</span>');
		$sep = ' ';
	}
	
	
	
	return implode($sep, $meta);		
}


/** Separator **/
function tst_get_sep($mark = '//') {
	
	return "<span class='sep'>".$mark."</span>";
}


/** Accessable thumbnails **/
function tst_get_post_thumbnail($cpost = null, $size = 'post-thumbnail'){
	global $post;
	
	if(!$cpost)
		$cpost = $post;
		
	$thumb_id = get_post_thumbnail_id($cpost->ID);
	if(!$thumb_id)
		return '';
	
	$att = get_post($thumb_id);
	$att_label = sprintf(__('Thumbnail for - %s', 'tst'), get_the_title($cpost->ID));
	
	$attr = array(
		'alt'   => (!empty($att->post_excerpt)) ? $att->post_excerpt : $att_label,
		'class' => 'responsive-img'
	);
	
	return wp_get_attachment_image($thumb_id, $size, false, $attr);
}

function tst_single_post_thumbnail_html($cpost = null, $size = 'post-thumbnail', $caption = ''){
	global $post;
	
	if(!$cpost)
		$cpost = $post;
		
	$thumb_id = get_post_thumbnail_id($cpost->ID);
	if(!$thumb_id)
		return '';
	
	$att = get_post($thumb_id);
	$att_label = sprintf(__('Thumbnail for - %s', 'tst'), get_the_title($cpost->ID));
	
	$attr = array(
		'alt'   => (!empty($att->post_excerpt)) ? $att->post_excerpt : $att_label,
		'class' => 'responsive-img'
	);
	
	$img = wp_get_attachment_image($thumb_id, $size, false, $attr);
	
	if(empty($caption)) {
		$caption =  (!empty($att->post_excerpt)) ? $att->post_excerpt : '';
	}
	
?>
	<figure class="wp-caption alignnone entry-media" >
		<a href="<?php wp_get_attachment_url($thumb_id);?>" class=""><?php echo $img;?></a>
		<?php if(!empty($caption)) { ?>
			<figcaption class="wp-caption-text"><?php echo apply_filters('tst_the_title', $caption);?></figcaption>
		<?php } ?>
	</figure>
<?php
}



/** Breadcrumbs  **/
function tst_breadcrumbs(){
	global $post;
		
	$links = array();
	if(is_singular('post')) {
		
		$cat = wp_get_object_terms($post->ID, 'category');
		if(!empty($cat)){
			$links[] = "<a href='".get_term_link($cat[0])."'>".apply_filters('tst_the_title', $cat[0]->name)."</a>";
		}			
	}
	elseif(is_singular('event')) {
		
		$p = get_page_by_path('calendar');
		$links[] = "<a href='".get_permalink($p)."'>".get_the_title($p)."</a>";		
	}
	elseif(is_page()){
		//@to-do - if treee ?
		$links[] = get_the_title($post);
	}
	elseif(is_home()){
		$p = get_post(get_option('page_for_posts'));
		if($p)
			$links[] = "<span>".get_the_title($p)."</span>";
	}
	elseif(is_category()){
		$links[] = "<span>".single_cat_title('', false)."</span>";
	}
	
	$sep = tst_get_sep("&gt;");
	
	return "<div class='crumbs'>".implode($sep, $links)."</div>";	
}


/** CPT archive title **/
function tst_get_post_type_archive_title($post_type) {
	
	$pt_obj = get_post_type_object( $post_type );
	return $pt_obj->labels->name;
}

/** Next fallback link **/
function tst_next_fallback_link($cpost = null){
	global $post;
		
	if(!$cpost)
		$cpost = $post;
		
	$cat = wp_get_object_terms($cpost->ID, 'category');
	if(!empty($cat))
		$cat = $cat[0]->term_id;
		
	$args = array(
		'post_type' => $cpost->post_type,
		'posts_per_page' => 1,
		'orderby' => 'date',
		'order' => 'ASC'
	);
	
	if(!empty($cat)){
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'category',
				'field' => 'id',
				'terms' => $cat
			)
		);
	}
	
	$query = new WP_Query($args);
	$link = '';
	if(isset($query->posts[0]) && $query->posts[0]->ID != $cpost->ID){
		$link = "<a href='".get_permalink($query->posts[0])."' rel='next'>Следующая &raquo;</a>";
	}
	
	return $link;
}


/** Events meta **/

function tst_event_meta($cpost = null) {
	global $post;
		
	if(!$cpost)
		$cpost = $post;
		
	$date = (function_exists('get_field')) ? get_field('event_date', $cpost->ID) : $cpost->post_date;
	$time = (function_exists('get_field')) ? get_field('event_time', $cpost->ID) : '';
	$addr = (function_exists('get_field')) ? get_field('event_address', $cpost->ID) : '';

	if(!empty($date)){
		echo "<div class='em-field'>";
		echo "<span class='label'>".__('Event date', 'tst').":</span>";
		echo "<time class='e-date'>".date('d.m.Y', strtotime($date))."</time>";
		echo "</div>";
	}
	if(!empty($time)){
		echo "<div class='em-field'>";
		echo "<span class='label'>".__('Time', 'tst').":</span>";
		echo "<time class='e-time'>".apply_filters('tst_the_title', $time)."</time>";
		echo "</div>";
	}
	if(!empty($addr)){
		echo "<div class='em-field'>";
		echo "<span class='label'>".__('Address', 'tst').":</span>";
		echo "<time class='e-time'>".apply_filters('tst_the_title', $addr)."</time>";
		echo "</div>";
	}
	

}


/** Social buttons **/
function tst_social_share() {
	
	wp_enqueue_script(
		'tst-social-buttons',
		get_template_directory_uri() . '/js/social-likes.min.js',
		array('jquery'),
		'3.0.14',
		true
	);
?>
<div class="social-likes">
	<div class="facebook" title="Поделиться ссылкой на Фейсбуке"></div>
	<div class="twitter" title="Поделиться ссылкой в Твиттере"></div>
	<div class="vkontakte" title="Поделиться ссылкой во Вконтакте"></div>
</div>
<?php
}


/** Excerpt with attached date **/
function tst_get_post_excerpt($cpost = null, $l = 30){
	global $post;
		
	if(!$cpost)
		$cpost = $post;
	
	$e = (!empty($cpost->post_excerpt)) ? $cpost->post_excerpt : wp_trim_words(strip_shortcodes($cpost->post_content), $l);
	$date = get_the_date('d.m.Y', $cpost);
	
	return "<time class='entry-date'>{$date}</time> ".$e;
}

/** Default author avatar **/
function tst_get_default_author_avatar(){
	
	$theme_dir_url = get_template_directory_uri();
	$src = get_template_directory_uri().'/img/author-default.jpg';
	$alt = __('Author', 'tst');
	
	return "<img src='{$src}' alt='{$alt}'>";
}


/** Logo **/
function tst_site_logo($size = 'regular'){
	
	switch($size){
		case 'context':
			$file = 'logo-pink';
			break;
		case 'small':
			$file = 'logo-small';
			break;
		default:
			$file = 'logo';
			break;	
	}
	
	$file = get_template_directory_uri().'/img/'.$file;
	$alt = esc_attr(__('Logo', 'tst'));
?>
	<img src="<?php echo $file;?>.svg" onerror="this.onerror=null;this.src=<?php echo $file;?>.png" alt="<?php echo $alt;?>">
<?php
}


/** Author **/
function tst_get_post_author($cpost = null) {
	global $post;
		
	if(!$cpost)
		$cpost = $post;
		
	$author = wp_get_object_terms(get_the_ID(), 'auctor');
	if(!empty($author))
		$author = $author[0];
	
	return $author;
}

function tst_get_author_avatar($author_term_id){
	
	$avatar = (function_exists('get_field')) ? get_field('auctor_photo', 'auctor_'.$author_term_id) : '';
	if(empty($avatar)){
		$avatar = tst_get_default_author_avatar();
	}
	else {
		$avatar = wp_get_attachment_image($avatar);
	}
	
	return $avatar;
}


/** Compact post item **/
function tst_compact_post_item($cpost = null, $show_thumb = true){
	global $post;
		
	if(!$cpost)
		$cpost = $post;
		
		
	$author = tst_get_post_author();
?>
	<div class="tpl-related-post"><a href="<?php echo get_permalink($cpost);?>">
	
	<div class="row">
		<div class="col mf-8">
			<h4 class="entry-title"><?php the_title();?></h4>
			
		<?php if($show_thumb) { ?>	
			<div class="entry-author pictured-card-item">
			<?php
				$avatar = ($author) ? tst_get_author_avatar($author->term_id) : '';
				$name = ($author) ? $author->name : '';
			?>				
					
				<div class="author-avatar round-image pci-img"><?php echo $avatar;?></div>
					
				<div class="author-content card-footer-content pci-content">
					<h5 class="author-name pci-title"><?php echo apply_filters('tst_the_title', $name);?></h5>
					<p class="post-date pci-caption"><time><?php echo get_the_date('d.m.Y.', $cpost);?></time></p>
				</div>
				
			</div>	
		<?php } else { ?>
			<div class="entry-author plain-card-item">
				<h5 class="author-name pci-title"><?php echo apply_filters('tst_the_title', $name);?></h5>
				<p class="post-date pci-caption"><time><?php echo get_the_date('d.m.Y.', $cpost);?></time></p>				
			</div>	
		<?php } ?>
		</div>
		
		<div class="col mf-4">
			<?php echo tst_get_post_thumbnail($cpost, 'post-thumbnail'); ?>
		</div>
	</div>	
	
	</a></div>
<?php
}

function tst_compact_news_item($cpost = null){
	global $post;
		
	if(!$cpost)
		$cpost = $post;
		
?>
	<div class="tpl-related-post news"><a href="<?php echo get_permalink($cpost);?>">
	
	
		<h4 class="entry-title"><?php the_title();?></h4>
		<p class="post-date pci-caption"><i class="material-icons">today</i> <time><?php echo get_the_date('d.m.Y.', $cpost);?></time></p>				
		
	
	</a></div>
<?php
}


/** Header image **/
function tst_header_image_url(){
	
	$img = '';
	if(is_tax()){
		$qo = get_queried_object();
		$img = (function_exists('get_field')) ? get_field('header_img', $qo->taxonomy.'_'.$qo->term_id) : 0;
		$img = wp_get_attachment_url($img);
	}
	elseif(is_single()){
		$qo = get_queried_object();
		$img = (function_exists('get_field')) ? get_field('header_img', $qo->ID) : 0;
		$img = wp_get_attachment_url($img);
	}
	
	if(empty($img)){ // fallback
		$img = get_template_directory_uri().'/img/header-default.jpg';
	}
	
	return $img;
}