<?php
/**
 * Admin customization
 **/

add_filter('manage_posts_columns', 'ruka_common_columns_names', 50, 2);
function ruka_common_columns_names($columns, $post_type) {
		
	if(in_array($post_type, array('post', 'event', 'programm', 'friendship', 'attachment'))){
		
		
		if(!in_array($post_type, array('attachment')))
			$columns['thumbnail'] = 'Миниат.';
		
		if(isset($columns['author'])){
			$columns['author'] = 'Создал';
		}
		
		if($post_type == 'event'){
			$columns['event_date'] = 'Дата анонса';
		}
			
		$columns['id'] = 'ID';
	}
	
	
	return $columns;
}


add_action('manage_posts_custom_column', 'ruka_common_columns_content', 2, 2);
function ruka_common_columns_content($column_name, $post_id) {
	
	$cpost = get_post($post_id);
	if($column_name == 'id'){
		echo intval($cpost->ID);
		
	}
	elseif($column_name == 'thumbnail') {
		$img = get_the_post_thumbnail($post_id, 'thumbnail');
		if(empty($img))
			echo "&ndash;";
		else
			echo "<div class='admin-tmb'>{$img}</div>";			
		
	}
	elseif($column_name == 'event_date') {
		
		$e_date = (function_exists('get_field')) ? get_field('event_date', $post_id) : '';
		if(!empty($e_date)){
			echo date('d.m.Y', strtotime($e_date));
		}
	}
}


add_filter('manage_pages_columns', 'ruka_pages_columns_names', 50);
function ruka_pages_columns_names($columns) {		
		
	if(isset($columns['author'])){
		$columns['author'] = 'Создал';
	}
	
	$columns['menu_order'] = 'Порядок';	
	$columns['id'] = 'ID';
		
	return $columns;
}


add_action('manage_pages_custom_column', 'ruka_pages_columns_content', 2, 2);
function ruka_pages_columns_content($column_name, $post_id) {
	
	$cpost = get_post($post_id);
	if($column_name == 'id'){
		echo intval($cpost->ID);
		
	}
	elseif($column_name == 'menu_order') {
			
		echo intval($cpost->menu_order);
	}
	
}



//manage_edit-topics_columns
add_filter( "manage_edit-category_columns", 'ruka_common_tax_columns_names', 10);
add_filter( "manage_edit-post_tag_columns", 'ruka_common_tax_columns_names', 10);
function ruka_common_tax_columns_names($columns){
	
	$columns['id'] = 'ID';
	
	return $columns;	
}

add_filter( "manage_category_custom_column", 'ruka_common_tax_columns_content', 10, 3);
add_filter( "manage_post_tag_custom_column", 'ruka_common_tax_columns_content', 10, 3);
function ruka_common_tax_columns_content($content, $column_name, $term_id){
	
	if($column_name == 'id')
		return intval($term_id);
}


/* admin tax columns */
/*add_filter('manage_taxonomies_for_material_columns', function($taxonomies){
	$taxonomies[] = 'pr_type';
	$taxonomies[] = 'audience';
	
    return $taxonomies;
});*/



/**
* SEO UI cleaning
**/
add_action('admin_init', function(){
	foreach(get_post_types(array('public' => true), 'names') as $pt) {
		add_filter('manage_' . $pt . '_posts_columns', 'ruka_clear_seo_columns', 100);
	}	
}, 100);

function ruka_clear_seo_columns($columns){

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

add_filter('wpseo_use_page_analysis', '__return_false');


/* Excerpt metabox */
add_action('add_meta_boxes', 'ruka_correct_metaboxes', 2, 2);
function ruka_correct_metaboxes($post_type, $post ){
	
	if(post_type_supports($post_type, 'excerpt')){
		remove_meta_box('postexcerpt', null, 'normal');
		
		$label = 'Аннотация';
		add_meta_box('ruka_postexcerpt', $label, 'ruka_excerpt_meta_box', null, 'normal', 'core');
	}
	//add_meta_box('postexcerpt', __('Excerpt'), 'post_excerpt_meta_box', null, 'normal', 'core');
}

function ruka_excerpt_meta_box($post){

$label =  'Аннотация';
$help =  'Аннотация для списков, на странице основного текста добавляется в начало.';
?>
<label class="screen-reader-text" for="excerpt"><?php echo $label;?></label>
<textarea rows="1" cols="40" name="excerpt" id="excerpt"><?php echo $post->post_excerpt; // textarea_escaped ?></textarea>
<p><?php echo $help;?></p>
<?php	
}


/* Menu Labeles */
add_action('admin_menu', 'ruka_admin_menu_labels');
function ruka_admin_menu_labels(){ /* change adming menu labels */
    global $menu, $submenu;
	
    /* find proper top menu item */    
    foreach($menu as $order => $item){
         
        if($item[2] == 'edit.php?post_type=acf-field-group'){
            $menu[$order][0] = __('Custom fields', 'ruka');
            break;
        }
    }   
    
}


/** Visual editor **/
add_filter('tiny_mce_before_init', 'tst_format_TinyMCE');
function tst_format_TinyMCE($in){

    $in['block_formats'] = "Абзац=p; Выделенный=pre; Заголовок 3=h3; Заголовок 4=h4; Заголовок 5=h5; Заголовок 6=h6";
	$in['toolbar1'] = 'bold,italic,strikethrough,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,link,unlink,wp_fullscreen,wp_adv ';
	$in['toolbar2'] = 'formatselect,underline,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help ';
	$in['toolbar3'] = '';
	$in['toolbar4'] = '';

	return $in;
}




