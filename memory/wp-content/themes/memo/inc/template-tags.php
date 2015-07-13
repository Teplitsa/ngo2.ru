<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package bb
 */

/* CPT Filters */
add_action('parse_query', 'memo_request_corrected');
function memo_request_corrected($query) {
	
	if(is_admin() || !$query->is_main_query())
		return;
	

	if(is_search()){
		$query->set('post_type', array('document', 'book', 'post', 'page'));
		
		$per = get_option('posts_per_page');
		if($per < 25) {			
			$query->set('posts_per_page', 5); // 25
		}
	}
	elseif(is_home()){
		$query->set('orderby', 'title');
		$query->set('order', 'ASC');
		//$query->set('posts_per_page', 99); // 25
	}
	elseif(is_tag()){
		$query->set('post_type', array('document', 'book'));
	}
	
	//var_dump($query->query_vars);
	
	
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

 
 

if ( ! function_exists( 'memo_paging_nav' ) ) :
/**
 * Display paging nav
 */
function memo_paging_nav($query = null) {
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
			$p = memo_paginate_links($query, false);
			if(!empty($p)):
		?>
			<div class="pagination"><?php memo_paginate_links($query); ?></div>
		<?php endif; ?>
		
	</nav><!-- .navigation -->
	<?php
}
endif;


function memo_paginate_links($query = null, $echo = true) {
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


if ( ! function_exists( 'memo_post_nav' ) ) :
/**
 * Display navigation to next/previous post when applicable.
 */
function memo_post_nav() {
	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	}
	?>
	<nav class="navigation post-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'memo' ); ?></h1>
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


if ( ! function_exists( 'memo_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function memo_posted_on() {
	$pt = get_post_type();
	$cat = '';
	
	
	if('post' == $pt){		
	
		$cat = get_the_term_list(get_the_ID(), 'category', '<span class="category">', ', ', '</span>');
	}
	
	$sep = memo_get_sep();
	
	if(!empty($cat))
		$cat = $sep.strip_tags($cat, '<span>');
?>
	<time class="date"><?php echo esc_html(get_the_date());?></time><?php
	echo $cat;	
}
endif;


function memo_get_sep() {
	
	return "<span class='sep'>//</span>";
}



/**
 * Accessable thumbnail
 **/
function memo_get_post_thumbnail($cpost = null, $size = 'post-thumbnail'){
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
function memo_breadcrumbs(){
	global $post;
	
	if(!is_single())
		return '';
	
	$links = array();
	if(is_singular('post')) {
		$p = get_post(get_option('page_for_posts'));
		if($p){
			$links[] = "&lt; <a href='".get_permalink($p)."'>".get_the_title($p)."</a>";
		}	
	}
	elseif(is_singular('document')){
		
		$pt_link = get_post_type_archive_link('document');
		$pt_name = memo_get_post_type_archive_title('document');
		if(!empty($pt_name)){
			$links[] = "&lt; <a href='".$pt_link."'>".$pt_name."</a>";
		}
			
	}
	elseif(is_singular('book')){
		
		$pt_link = get_post_type_archive_link('book');
		$pt_name = memo_get_post_type_archive_title('book');
		if(!empty($pt_name)){
			$links[] = "&lt; <a href='".$pt_link."'>".$pt_name."</a>";
		}
	}
	elseif(is_singular('marker')){
		
		$p = get_page_by_path('karta'); 
		if($p){
			$links[] = "&lt; <a href='".get_permalink($p)."'>".get_the_title($p)."</a>";
		}	
	}
	
	$sep = memo_get_sep();
	
	return implode($sep, $links);	
}

function memo_get_post_type_archive_title($post_type) {
	
	if($post_type == 'document'){
		return "Раритет";
	}
	
	if($post_type == 'book'){
		return "Книжная полка";
	}
	
	$pt_obj = get_post_type_object( $post_type );
	return $pt_obj->labels->name;
}

function memo_tags_widget(){
	
	$css = (is_page('stories')) ? 'bit md-4' : '';
?>
	<div class="tagscloud-widget card <?php echo $css;?>">
		<div class="post-inner">
			<div class="widget-icon"><i class="fa fa-map-marker"></i></div>
			<div class="widget-cloud-content">
			<?php
				$args = array(
					'taxonomy' => 'place',
					'smallest' => 11, 'largest' => 18, 'unit' => 'pх'
				);
				wp_tag_cloud($args);
			?>
			</div>
		</div>	
	</div>
<?php	
}

function memo_post_attached_gallery($post_id, $columns) {
	
	$gallery = (function_exists('get_field')) ? get_field('entry_gallery', $post_id) : '';
	if(empty($gallery))
		return '';
	
	$ids = array();
	foreach($gallery as $g){
		$ids[] = $g['id'];
	}
	
	$args = array(
        'post_type'   => 'attachment',
        'post_status' => 'inherit',
        'orderby'     => 'post__in',
        'order'       => 'ASC',
        'post_mime_type' => 'image',
        'post__in'     => $ids,
        'posts_per_page' => -1
    );
	
	$columns = intval($columns);

    if($columns == 0 || $columns > 8)
        $columns = 5;
		
	$query = new WP_Query($args);
	
    if(empty($query->posts))
        return ''; //no attachments
	
	return lam_lightbox_gallery_output($query->posts, $columns);
}

function memo_document_attached_img($post_id) {
	
	$img = (function_exists('get_field')) ? get_field('document_scan', $post_id) : 0;
	if($img == 0){
		$img = get_post_thumbnail_id($post_id);
	}
	
	if(!$img)
		return '';
	
	$att = get_post($img);
	
	$url = wp_get_attachment_url($att->ID);
	$img = wp_get_attachment_image($att->ID, 'full');
	$caption = esc_attr(trim($att->post_excerpt.' '.$att->post_content));
	
	return "<a href='{$url}' data-fresco-caption='{$caption}' rel='image-overlay' class='img-padder fresco'>{$img}</a>";
}


function memo_document_attached_gallery($post_id, $columns){
		
	$gallery = (function_exists('get_field')) ? get_field('doc_gallery', $post_id) : '';
	if(empty($gallery))
		return '';
	
	$ids = array();
	foreach($gallery as $g){
		$ids[] = $g['id'];
	}
	
	$args = array(
        'post_type'   => 'attachment',
        'post_status' => 'inherit',
        'orderby'     => 'post__in',
        'order'       => 'ASC',
        'post_mime_type' => 'image',
        'post__in'     => $ids,
        'posts_per_page' => -1
    );
	
	$columns = intval($columns);

    if($columns == 0 || $columns > 8)
        $columns = 5;
		
	$query = new WP_Query($args);
	
    if(empty($query->posts))
        return $out; //no attachments
	
	$html = '';
	
	$att = get_post($query->posts[0]);
	unset($query->posts[0]);
	$items = $query->posts;	
	$gallery_ref = uniqid('gallery-');

	$url = wp_get_attachment_url($att->ID);
	$img = wp_get_attachment_image($att->ID, 'full');
	$caption = esc_attr(trim($att->post_excerpt.' '.$att->post_content));

	$html = "<div class='full-img'><a data-fresco-group='{$gallery_ref}' href='{$url}' data-fresco-caption='{$caption}' rel='image-overlay' class='img-padder fresco'>{$img}</a></div>";

	$html .= "<div class='gallery-img lam-gallery'><ul class='lam-clearfix cols-{$columns}'>";

	foreach($items as $picture) {

		$caption = esc_attr(trim($picture->post_excerpt.' '.$picture->post_content));

        $url = wp_get_attachment_url($picture->ID);
        //$img = wp_get_attachment_image($picture->ID, 'post-thumbnail', false, $attr);

        // HTML for lightbox:
        $html .= "<li><a data-fresco-group='{$gallery_ref}' href='{$url}' data-fresco-caption='{$caption}' rel='image-overlay' class='img-padder fresco'><img src='".aq_resize($url, 320, 210, true, true, true)."' width='320' height='210' alt=''></a></li>";
    }

	$html .= "</ul></div>";

	return $html;
}

function memo_book_read_link($post_id){
	
	if(!function_exists('get_field'))
		return '';
	
	$type = get_field('publication_type', $post_id);
	$html = '';
	
	if($type == 'file'){
		$file = get_field('publication_file', $post_id);
		if(empty($file))
			return '';
		
		$file = wp_get_attachment_url($file);
		$html = "<a href='{$file}' target='_blank'>Читать полностью (скачать)&raquo;</a>";
	}
	elseif($type == 'link') {
		$link = get_field('publication_link', $post_id);
		if(empty($link))
			return '';
		
		$link = esc_url($link);
		$html = "<a href='{$link}' target='_blank'>Читать полностью (ссылка)&raquo;</a>";
	}
		
	return $html;
}