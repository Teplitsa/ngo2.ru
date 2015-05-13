<?php
/**
 * Site specifictemplate tags
 **/

/**
 * Modifications
 **/

/* posts formats */
//add_action('init', 'frl_post_formats', 100);
function frl_post_formats(){
	add_post_type_support( 'your_cpt', 'post-formats' );   
	register_taxonomy_for_object_type( 'post_format', 'your_cpt' );
}
 
/* CPT Filters */
//add_filter('request', 'frl_request_corrected');
function frl_request_corrected($query_vars) {
	
	if(isset($query_vars['audience']) && !empty($query_vars['audience']))
		$query_vars['post_type'] = array('post', 'work', 'article');
	
	if(isset($query_vars['tag']) && !empty($query_vars['tag']))
		$query_vars['post_type'] =  array('post', 'work', 'article');
		
	return $query_vars;
} 


/* Admin */
add_filter('manage_posts_columns', 'frl_common_columns_names', 50, 2);
function frl_common_columns_names($columns, $post_type) {
		
	if(!in_array($post_type, array('post', 'work', 'article', 'banner', 'attachment')))
		return $columns;
	
	$columns['id'] = 'ID';
	
	if($post_type != 'attachment')
		$columns['thumbnail'] = 'Миниат.';
		
	
	return $columns;
}

add_action('manage_posts_custom_column', 'frl_common_columns_content', 2, 2);
function frl_common_columns_content($column_name, $post_id) {
	
	if($column_name == 'id'){
		echo intval(get_post($post_id)->ID);
		
	} elseif($column_name == 'thumbnail') {
		$img = get_the_post_thumbnail($post_id, 'thumbnail');
		if(empty($img))
			echo "&ndash;";
		else
			echo "<div class='admin-tmb'>{$img}</div>";
	}
}

/* admin tax columns */
/*add_filter('manage_taxonomies_for_work_columns', function($taxonomies){
	$taxonomies[] = 'pr_type';
	$taxonomies[] = 'audience';
	
    return $taxonomies;
});*/

 
/* Custom conditions */
function is_about(){
	global $post;
	
	if(!is_page())
		return false;
	
	if(is_page('about'))
		return true;
	
	$parents = get_post_ancestors($post);
	$test = get_page_by_path('about');
	if(in_array($test->ID, $parents))
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

function is_materials() {
	
	if(is_post_type_archive('article'))
		return true;
	
	if(is_tax('art_cat'))
		return true;
	
	if(is_singular('article'))
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

function is_news() {
	
	if(is_home() || is_category())
		return true;
	
	if(is_singular('post'))
		return true;
	
	return false;
}

function is_events() {
	
	if(is_post_type_archive('event'))
		return true;
	
	if(is_tax('eventcat'))
		return true;
	
	if(is_singular('event'))
		return true;
	
	return false;
}

?>