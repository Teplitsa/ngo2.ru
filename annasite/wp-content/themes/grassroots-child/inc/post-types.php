<?php
add_action('init', 'grtch_custom_content', 20);

function grtch_custom_content(){
	
	
	register_post_type('document', array(
        'labels' => array(
            'name'               => 'Статьи',
            'singular_name'      => 'Статья',
            'menu_name'          => 'Документация',
            'name_admin_bar'     => 'Добавить статью',
            'add_new'            => 'Добавить новую',
            'add_new_item'       => 'Добавить статью',
            'new_item'           => 'Новая статья',
            'edit_item'          => 'Редактировать статью',
            'view_item'          => 'Просмотр статьи',
            'all_items'          => 'Все статьи',
            'search_items'       => 'Искать статьи',
            'parent_item_colon'  => 'Родительская статья:',
            'not_found'          => 'Статьи не найдены',
            'not_found_in_trash' => 'В корзине статьи не найдены'
       ),
        'public'             => true,
        'exclude_from_search'=> false,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_nav_menus'  => false,
        'show_in_menu'       => true,
        'show_in_admin_bar'  => true,
        //'query_var'          => true,        
        'capability_type'    => 'post',
        'has_archive'        => false,
        'rewrite'            => array('slug' => 'docs', 'with_front' => false),
        'hierarchical'       => true,
        'menu_position'      => 5,
        'supports'           => array('title', 'editor', 'excerpt', 'page-attributes'),
        'taxonomies'         => array('post_tag'),
   ));
	
	
}


/* Admin columns */
if(!function_exists('frl_common_columns_names')):
function frl_common_columns_names($columns, $post_type) {
		
	if(!in_array($post_type, array('post', 'document')))
		return $columns;

	$columns['id'] = 'ID';
	
	if($post_type == 'document')
		$columns['menu_order'] = 'Порядок';
	
	return $columns;
}
endif;
add_filter('manage_posts_columns', 'frl_common_columns_names', 50, 2);


if(!function_exists('frl_common_page_columns_names')):
function frl_common_page_columns_names($columns, $post_type) {
	
	
	$columns['id'] = 'ID';	
	$columns['menu_order'] = 'Порядок';
	
	return $columns;
}
endif;
add_filter('manage_pages_columns', 'frl_common_page_columns_names', 50, 2);


add_action('manage_pages_custom_column', 'frl_common_columns_content', 2, 2);
add_action('manage_posts_custom_column', 'frl_common_columns_content', 2, 2);
function frl_common_columns_content($column_name, $post_id) {
	
	$cpost = get_post($post_id);
	if($column_name == 'id'){
		echo intval($cpost->ID);
		
	}	
	elseif($column_name == 'menu_order') {
		echo intval($cpost->menu_order);
	}
	
}