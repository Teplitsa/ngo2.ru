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
	
	if(is_home() || is_category() || is_tax('auctor'))
		return true;
	
		
	if(is_singular('post'))
		return true;
	
	return false;
}

function is_events() {
	
		
	if(is_post_type_archive('event') || is_page('calendar'))
		return true;
		
	if(is_singular('event'))
		return true;
	
	return false;
}

function is_products(){
	
	if(is_post_type_archive('product'))
		return true;
	
	if(is_singular('product'))
		return true;
	
	return false;
	
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
		$p = "<div class='paging-navigation'>$p</div>";
	}

	return $p;
}


add_filter('next_posts_link_attributes', 'tst_load_more_link_css');
function tst_load_more_link_css($attr){
	
	$attr = " class='mdl-button mdl-js-button mdl-js-ripple-effect'";
	
	return $attr;
}
function tst_load_more_link($query = null, $echo = true) {
	global $wp_query;
	
	if(!$query)
		$query = $wp_query;
	
	$label = (is_search()) ? __('More results', 'tst') : __('More entries', 'tst');
	$l = get_next_posts_link($label, $query->max_num_pages);
	
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
	$sep = '';
	
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

function tst_get_post_thumbnail_src($cpost = null, $size = 'post-thumbnail'){
	if(!$cpost)
		$cpost = $post;
		
	$thumb_id = get_post_thumbnail_id($cpost->ID);
	if(!$thumb_id)
		return '';
	
	$img = wp_get_attachment_image_src($thumb_id, $size);
	return $img[0];
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
		<a href="<?php echo wp_get_attachment_url($thumb_id);?>" class=""><?php echo $img;?></a>
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
	if(is_front_page()){
		$links[] = "<span class='crumb-name'>".get_bloginfo('name')."</span>";
	}
	elseif(is_singular('post')) {
		
		$cat = wp_get_object_terms($post->ID, 'category');
		if(!empty($cat)){
			$links[] = "<a href='".get_term_link($cat[0])."' class='crumb-link'>".apply_filters('tst_the_title', $cat[0]->name)."</a>";
		}			
	}
	elseif(is_singular('event')) {
		
		$p = get_page_by_path('calendar');
		$links[] = "<a href='".get_permalink($p)."' class='crumb-link'>".get_the_title($p)."</a>";		
	}
	elseif(is_page() || is_singular('leyka_campaign')){
		//@to-do - if treee ?
		$links[] = "<span class='crumb-name'>".get_the_title($post)."</span>";
	}
	elseif(is_home()){
		$p = get_post(get_option('page_for_posts'));
		if($p)
			$links[] = "<span class='crumb-name'>".get_the_title($p)."</span>";
	}
	elseif(is_category()){
		$links[] = "<span class='crumb-name'>".single_cat_title('', false)."</span>";
	}
	elseif(is_post_type_archive('product')) {
		$links[] = "<span class='crumb-name'>".tst_get_post_type_archive_title('product')."</span>";
		
	}
	elseif(is_singular('product')) {		
		$links[] = "<a href='".get_post_type_archive_link('product')."' class='crumb-link'>".tst_get_post_type_archive_title('product')."</a>";
		
	}
	
	$sep = tst_get_sep("&gt;");
	
	return "<div class='crumbs'>".implode($sep, $links)."</div>";	
}


/** CPT archive title **/
function tst_get_post_type_archive_title($post_type) {
	
	$pt_obj = get_post_type_object( $post_type );
	
	if($post_type == 'product'){
		$name = $pt_obj->labels->menu_name;
	}
	else {
		$name = $pt_obj->labels->name;
	}
	
	return $name;
}

/** Next link **/
function tst_next_link($cpost = null){
	global $post;
		
	if(!$cpost)
		$cpost = $post;
		
	if($cpost->post_type == 'event'){
		
		//get next event
		$news_query = new WP_Query(array(
			'post_type' => 'event',			
			'meta_key' => 'event_date',
			'orderby' => 'meta_value',
			'order' => 'ASC', 
			'posts_per_page' => 1,
			'meta_query' => array(
				array(
					'key'     => 'event_date',
					'value'   => get_post_meta($cpost->ID, 'event_date', true),
					'compare' => '>', // '>' to get a chronologically next event
				),
			),
		));
		
		if(!$news_query->have_posts()){
			$news_query = new WP_Query(array(
				'post_type' => 'event',			
				'meta_key' => 'event_date',
				'orderby' => 'meta_value',
				'order' => 'ASC', 
				'posts_per_page' => 1				
			));
		}
		$next = '';
		
		if(isset($news_query->posts[0]) && $news_query->posts[0]->ID != $cpost->ID){
			$next = "<a href='".get_permalink($news_query->posts[0])."' rel='next'>Следующий &raquo;</a>";
		}
	}
	else {
		$next =  get_next_post_link('%link', 'Следующая &raquo;', true);
		if(empty($next)) {
			$next = tst_next_fallback_link($post);
		}
	}
		
	return $next;				
}

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
		
	$date = (function_exists('get_field')) ? get_field('event_date', $cpost->ID) : '' ;
	$time = (function_exists('get_field')) ? get_field('event_time', $cpost->ID) : '';
	$lacation = (function_exists('get_field')) ? get_field('event_location', $cpost->ID) : '';
	$addr = (function_exists('get_field')) ? get_field('event_address', $cpost->ID) : '';

	if(!empty($date)){
		echo "<div class='em-field date-field'>";
		echo "<div class='em-label mdl-typography--caption'>".__('Event date', 'tst').":</div>";
		echo "<div class='em-date mdl-typography--body-1'>".date('d.m.Y', strtotime($date))."</div>";
		tst_add_to_calendar_link($cpost);
		echo "</div>";
	}
	if(!empty($time)){
		echo "<div class='em-field'>";
		echo "<div class='em-label mdl-typography--caption'>".__('Time', 'tst').":</div>";
		echo "<div class='em-time mdl-typography--body-1'>".apply_filters('tst_the_title', $time)."</div>";
		echo "</div>";
	}
	if(!empty($lacation)){
		echo "<div class='em-field'>";
		echo "<div class='em-label mdl-typography--caption'>".__('Location', 'tst').":</div>";
		echo "<div class='em-location mdl-typography--body-1'>".apply_filters('tst_the_title', $lacation)."</div>";
		echo "</div>";
	}
	if(!empty($addr)){
		echo "<div class='em-field'>";
		echo "<div class='em-label mdl-typography--caption'>".__('Address', 'tst').":</div>";
		echo "<div class='em-addr mdl-typography--body-1'>".apply_filters('tst_the_title', $addr)."</div>";
		echo "</div>";
	}
	

}

function tst_is_future_event($date) {
	
	$today_exact = strtotime(sprintf('now %s hours', get_option('gmt_offset')));
	$today_mark = date('Y', $today_exact).'-'.date('m', $today_exact).'-'.date('d', $today_exact);
	$stamp = date('Y-m-d', strtotime($date));
		
	
	if((string)$stamp >= (string)$today_exact) {
		return true;
	}
	else {
		return false;
	}
}

function tst_events_card_content($event){
	
	$img = tst_get_post_thumbnail_src($event, 'post-thumbnail');
	$date = (function_exists('get_field')) ? get_field('event_date', $event->ID) : $event->post_date;
	$time = (function_exists('get_field')) ? get_field('event_time', $event->ID) : '';
	$lacation = (function_exists('get_field')) ? get_field('event_location', $event->ID) : '';
	$addr = (function_exists('get_field')) ? get_field('event_address', $event->ID) : '';
	
	$e = (!empty($event->post_excerpt)) ? $event->post_excerpt : wp_trim_words(strip_shortcodes($event->post_content), 20);
	
	ob_start();
?>	
	<div class="mdl-card__media" style="background-image: url(<?php echo $img;?>);?>">
		<?php //echo tst_get_post_thumbnail($event, 'post-thumbnail'); ?>
	</div>
	
	<div class="event-content-frame">
		<div class="mdl-card__title">
			<h4 class="mdl-card__title-text"><a href="<?php echo get_permalink($event);?>"><?php echo get_the_title($event);?></a></h4>
		</div>	
		<div class="event-meta">
			<div class="pictured-card-item event-date">
				<div class="em-icon pci-img"><?php echo tst_material_icon('schedule'); ?></div>				
				<div class="em-content pci-content">
					<h5 class="mdl-typography--body-1"><?php echo date('d.m.Y', strtotime($date));?></h5>
					<p class="mdl-typography--caption"><?php echo apply_filters('tst_the_title', $time); ?></p>
					<?php
						if(tst_is_future_event($date)){
							tst_add_to_calendar_link($event, true, 'in-modal-add-tip');
						}
					?>
				</div>
			</div>
			<div class="pictured-card-item event-location">
				<div class="em-icon pci-img"><?php echo tst_material_icon('room'); ?></div>				
				<div class="em-content pci-content">
					<h5 class="mdl-typography--body-1"><?php echo apply_filters('tst_the_title', $lacation); ?></h5>
					<p class="mdl-typography--caption"><?php echo apply_filters('tst_the_title', $addr); ?></p>
				</div>
			</div>	
		</div>	
		<div class="mdl-card__supporting-text"><?php echo apply_filters('tst_the_title', $e);?></div>
	</div>	
	
	<div class="mdl-card__actions mdl-card--border">		
		<a href="<?php echo get_permalink($event);?>" class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect">Принять участие</a>
	</div>
	
<?php
	$out = ob_get_contents();
	ob_end_clean();
	
	return $out;
}

// loader panel
function tst_loader_panel(){

	return '<div class="loader-panel"><div class="spinner"></div></div>';

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

function tst_card_summary($cpost = null, $l = 30){
	
	$text = tst_get_post_excerpt($cpost, $l);
		
	$text = apply_filters('tst_the_content', $text);
	$text = str_replace('<p>', '<div class="mdl-card__supporting-text">', $text);	
	$text = str_replace('</p>', '</div>', $text);
	
	return $text;
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
		
	$author = wp_get_object_terms($cpost->ID, 'auctor');
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
		$avatar = wp_get_attachment_image($avatar, 'avatar');
	}
	
	return $avatar;
}


/** Compact post item **/
function tst_compact_post_item($cpost = null, $show_thumb = true, $thumb_size = 'thumbnail-landscape'){
	global $post;
		
	if(!$cpost)
		$cpost = $post;
		
		
	$author = tst_get_post_author();
	$name = ($author) ? $author->name : '';
?>
	<div class="tpl-related-post"><a href="<?php echo get_permalink($cpost);?>">
	
	<div class="mdl-grid mdl-grid--no-spacing">
		<div class="mdl-cell mdl-cell--8-col mdl-cell--5-col-tablet mdl-cell--2-col-phone">
			<h4 class="entry-title"><?php echo get_the_title($cpost);?></h4>
			
		<?php if($show_thumb) { ?>	
			<div class="entry-author pictured-card-item">
			<?php $avatar = ($author) ? tst_get_author_avatar($author->term_id) : ''; ?>				
					
				<div class="author-avatar round-image pci-img"><?php echo $avatar;?></div>
					
				<div class="author-content card-footer-content pci-content">
					<h5 class="author-name mdl-typography--body-1"><?php echo apply_filters('tst_the_title', $name);?></h5>
					<p class="post-date mdl-typography--caption"><time><?php echo get_the_date('d.m.Y.', $cpost);?></time></p>
				</div>
				
			</div>	
		<?php } else { ?>
			<div class="entry-author plain-card-item">
				<h5 class="author-name mdl-typography--body-1"><?php echo apply_filters('tst_the_title', $name);?></h5>
				<p class="post-date mdl-typography--caption"><time><?php echo get_the_date('d.m.Y.', $cpost);?></time></p>				
			</div>	
		<?php } ?>
		</div>
		
		<div class="mdl-cell mdl-cell--4-col mdl-cell--3-col-tablet mdl-cell--2-col-phone">
		<?php
			$thumb = tst_get_post_thumbnail($cpost, $thumb_size);
			if(empty($thumb)){
				$thumb = tst_get_default_post_thumbnail($thumb_size);
			}
			
			echo $thumb;
		?>
		</div>
	</div>	
	
	</a></div>
<?php
}

// deafult thumbnail for posts
function tst_get_default_post_thumbnail($size){
		
	$default_thumb_id = attachment_url_to_postid(get_theme_mod('default_thumbnail'));
	$img = '';
	if($default_thumb_id){
		$img = wp_get_attachment_image($default_thumb_id, $size);	
	}
	
	return $img;
}


function tst_compact_news_item($cpost = null, $show_thumb = true){
	global $post;
		
	if(!$cpost)
		$cpost = $post;
	
	$author = tst_get_post_author();
	$name = ($author) ? $author->name : '';
?>
<div class="tpl-related-post news"><a href="<?php echo get_permalink($cpost);?>">	

	<h4 class="entry-title"><?php echo get_the_title($cpost);?></h4>
	<?php if($show_thumb) { ?>	
		<div class="entry-author pictured-card-item">
		<?php $avatar = ($author) ? tst_get_author_avatar($author->term_id) : ''; ?>				
				
			<div class="author-avatar round-image pci-img"><?php echo $avatar;?></div>
				
			<div class="author-content card-footer-content pci-content">
				<h5 class="author-name mdl-typography--body-1"><?php echo apply_filters('tst_the_title', $name);?></h5>
				<p class="post-date mdl-typography--caption"><time><?php echo get_the_date('d.m.Y.', $cpost);?></time></p>
			</div>
			
		</div>	
	<?php } else { ?>
		<div class="entry-author plain-card-item">
			<h5 class="author-name mdl-typography--body-1"><?php echo apply_filters('tst_the_title', $name);?></h5>
			<p class="post-date mdl-typography--caption"><time><?php echo get_the_date('d.m.Y.', $cpost);?></time></p>				
		</div>	
	<?php } ?>
	

</a></div>
<?php
}

function tst_compact_product_item($cpost = null){
	global $post;
		
	if(!$cpost)
		$cpost = $post;
	
	$price = (function_exists('get_field')) ? get_field('product_price', $cpost->ID) : '';
	$thumb = get_the_post_thumbnail($cpost->ID, 'avatar');
	if(empty($thumb))
		$thumb = tst_get_default_post_thumbnail('avatar');
?>
<div class="tpl-compact-product">	
	<div class="pictured-card-item">
		<div class="pr-avatar round-image pci-img">
			<?php echo $thumb; ?>
		</div>
			
		<div class="pr-content pci-content">
			<h5 class="pr-title mdl-typography--body-1">
				<a href="<?php echo get_permalink($cpost);?>">
					<?php echo get_the_title($cpost);?>
				</a>
			</h5>
			<p class="pr-price mdl-typography--caption"><?php echo number_format ((int)$price , 0 , "." , " " );?> руб.</p>
			<div class="buy">
				<a href="<?php echo get_the_permalink($cpost);?>" class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored mdl-js-ripple-effect">Купить</a>
			</div>
		</div>
		
	</div>	
</div>
<?php
}

function tst_compact_event_item($cpost = null){
	global $post;
		
	if(!$cpost)
		$cpost = $post;
	
	$e_date = get_post_meta($cpost->ID, 'event_date', true);
	$thumb = get_the_post_thumbnail($cpost->ID, 'avatar');
	if(empty($thumb))
		$thumb = tst_get_default_post_thumbnail('avatar');
?>
<div class="tpl-compact-event">	
	<div class="pictured-card-item">
		<div class="event-avatar round-image pci-img">
			<?php echo $thumb; ?>
		</div>
			
		<div class="event-content pci-content">
			<h5 class="event-title mdl-typography--body-1"><a href="<?php echo get_permalink($cpost);?>">
				<?php echo get_the_title($cpost);?>
			</a></h5>
			<p class="event-date mdl-typography--caption"><time><?php echo date_i18n('d.m.Y', strtotime($e_date));?></time></p>
			<div class="add-to-calendar">
				<?php tst_add_to_calendar_link($cpost);?>
			</div>
		</div>		
	</div>	
</div>
<?php
}

function tst_add_to_calendar_url($event){
//
//
//<a href="http://www.google.com/calendar/event?
//action=TEMPLATE
//&text=[event-title]
//&dates=[start-custom format='Ymd\\THi00\\Z']/[end-custom format='Ymd\\THi00\\Z']
//&details=[description]
//&location=[location]
//&trp=false
//&sprop=
//&sprop=name:"
//target="_blank" rel="nofollow">Add to my calendar</a>

	$date = (function_exists('get_field')) ? get_field('event_date', $event->ID) : $event->post_date;
	$time = (function_exists('get_field')) ? get_field('event_time', $event->ID) : '';
	$lacation = (function_exists('get_field')) ? get_field('event_location', $event->ID) : '';
	$addr = (function_exists('get_field')) ? get_field('event_address', $event->ID) : '';
	
	if(empty($time))
		$time = '12.00';
		
	$start_mark = date_i18n('YmdHi00', strtotime($date.' '.$time));
	$end_mark = date_i18n('YmdHi00', strtotime('+2 hours '.$date.' '.$time));
	$e = (!empty($event->post_excerpt)) ? wp_trim_words($event->post_excerpt, 20) : wp_trim_words(strip_shortcodes($event->post_content), 20);
	
	$tst = "https://www.google.com/calendar/event?";
	$tst .= "action=TEMPLATE";
	$tst .= "&text=".urlencode($event->post_title);
	$tst .= "&dates={$start_mark}/{$end_mark}&czt=Europe/Moscow";
	$tst .= "&location=".urlencode($lacation.' '.$addr);
	$tst .= "&details=".urlencode($e);
	
	return $tst;

}

add_action('wp_footer', 'tst_add_to_calendar_scripts');
function tst_add_to_calendar_scripts(){
	
?>
<script type="text/javascript">(function () {
	if (window.addtocalendar)if(typeof window.addtocalendar.start == "function")return;
	if (window.ifaddtocalendar == undefined) { window.ifaddtocalendar = 1;
		var d = document, s = d.createElement('script'), g = 'getElementsByTagName';
		s.type = 'text/javascript';s.charset = 'UTF-8';s.async = true;
		s.src = ('https:' == window.location.protocol ? 'https' : 'http')+'://addtocalendar.com/atc/1.5/atc.min.js';
		var h = d[g]('body')[0];h.appendChild(s); }})();
</script>
<?php	
}

function tst_add_to_calendar_link($event, $echo = true, $container_class = 'tst-add-calendar') {
	
	$date = (function_exists('get_field')) ? get_field('event_date', $event->ID) : $event->post_date;
	$time = (function_exists('get_field')) ? get_field('event_time', $event->ID) : '';
	$location = (function_exists('get_field')) ? get_field('event_location', $event->ID) : '';
	$addr = (function_exists('get_field')) ? get_field('event_address', $event->ID) : '';
	
	if(empty($time))
		$time = '12.00';
		
	$start_mark = date_i18n('Y-m-d H:i:00', strtotime($date.' '.$time));
	$end_mark = date_i18n('Y-m-d H:i:00', strtotime('+2 hours '.$date.' '.$time));
	$e = (!empty($event->post_excerpt)) ? wp_trim_words($event->post_excerpt, 20) : wp_trim_words(strip_shortcodes($event->post_content), 20);
	$id = 'tst-'.uniqid();
?>
	<span id="<?php echo esc_attr($id);?>"  class="<?php echo esc_attr($container_class);?>">
		<span class="addtocalendar">
			<var class="atc_event">
				<var class="atc_date_start"><?php echo $start_mark;?></var>
				<var class="atc_date_end"><?php echo $end_mark;?></var>
				<var class="atc_timezone">Europe/Moscow</var>
				<var class="atc_title"><?php echo esc_attr($event->post_title);?></var>
				<var class="atc_description"><?php echo apply_filters('tst_the_title', $e);?></var>
				<var class="atc_location"><?php echo esc_attr($location).' '.esc_attr($addr);?></var>          
			</var>		
		</span>			
	</span>
	<span class="tst-tooltip" for="<?php echo esc_attr($id);?>">Добавить в календарь</span>
<?php	
}

function tst_material_icon($icon){
	
	$icon = esc_attr($icon);
	return "<i class='material-icons'>{$icon}</i>";
}


/** Header image **/
function tst_header_image_url(){
	
	$img = '';
	if(is_tax()){
		$qo = get_queried_object();
		$img = (function_exists('get_field')) ? get_field('header_img', $qo->taxonomy.'_'.$qo->term_id) : 0;
		$img = wp_get_attachment_url($img);
	}
	elseif(is_single() || is_page()){
		$qo = get_queried_object();
		$img = (function_exists('get_field')) ? get_field('header_img', $qo->ID) : 0;
		$img = wp_get_attachment_url($img);
	}
	
	if(empty($img)){ // fallback
		$img = get_template_directory_uri().'/img/header-default.jpg';
	}
	
	return $img;
}


/** Post card content **/
function tst_post_card_content($cpost = null){
	global $post;
		
	if(!$cpost)
		$cpost = $post;
		
	$author = tst_get_post_author($cpost); 
?>
	<?php if(has_post_thumbnail($cpost->ID)){ ?>
	<div class="mdl-card__media">
		<?php echo tst_get_post_thumbnail($cpost, 'embed'); ?>		
	</div>			
	<?php } ?>
	
	<?php if(!empty($author)) { ?>
		<div class="entry-author mdl-card__supporting-text">
		<?php $avatar = tst_get_author_avatar($author->term_id) ; ?>				
			
			<div class="pictured-card-item">
				<div class="author-avatar round-image pci-img"><?php echo $avatar;?></div>
					
				<div class="author-content card-footer-content pci-content">
					<h5 class="author-name mdl-typography--body-1"><?php echo apply_filters('tst_the_title', $author->name);?></h5>
					<p class="author-role mdl-typography--caption"><?php echo apply_filters('tst_the_title', $author->description);?></p>
				</div>
			</div>
		</div>
	<?php } ?>
	
	<div class="mdl-card__title">
		<h4 class="mdl-card__title-text"><a href="<?php echo get_permalink($cpost);?>"><?php echo get_the_title($cpost->ID);?></a></h4>
	</div>
	
	<?php echo tst_card_summary($cpost); ?>
	<div class="mdl-card--expand"></div>
	<div class="mdl-card__actions mdl-card--border">
		<a href="<?php echo get_permalink($cpost);?>" class="mdl-button mdl-js-button">Подробнее</a>
	</div>
<?php
}