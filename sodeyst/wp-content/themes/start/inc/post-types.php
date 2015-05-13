<?php
add_action('init', 'bb_custom_content', 20);
if( !function_exists('bb_custom_content') ) {
function bb_custom_content(){

    /** Existing post types settings: */
    add_post_type_support('page', array('excerpt'));
	deregister_taxonomy_for_object_type('post_tag', 'post');
	
	
    /** Post types: */
    register_post_type('friendship', array(
        'labels' => array(
            'name'               => __('Friendship stories', 'bb'),
            'singular_name'      => __('Story', 'bb'),
            'menu_name'          => __('Stories', 'bb'),
            'name_admin_bar'     => __('Add a story', 'bb'),
            'add_new'            => __('Add new', 'bb'),
            'add_new_item'       => __('Add new story', 'bb'),
            'new_item'           => __('New story', 'bb'),
            'edit_item'          => __('Edit the story', 'bb'),
            'view_item'          => __('View the story page', 'bb'),
            'all_items'          => __('All stories', 'bb'),
            'search_items'       => __('Search for stories', 'bb'),
            'parent_item_colon'  => __('Parent story', 'bb'),
            'not_found'          => __('No stories found', 'bb'),
            'not_found_in_trash' => __('No stories found in Trash', 'bb'),
        ),
        'public'              => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_nav_menus'   => false,
        'show_in_menu'        => true,
        'show_in_admin_bar'   => true,
        //'query_var'           => true,
        'capability_type'     => 'post',
        'has_archive'         => 'friendships',
        'rewrite'             => array('slug' => 'friendship', 'with_front' => false),
        'hierarchical'        => false,
        'menu_position'       => 5,
        'supports'            => array('title', 'excerpt', 'editor', 'thumbnail'),
        'taxonomies'          => array(),
    ));

	register_post_type('event', array(
        'labels' => array(
            'name'               => __('Events', 'bb'),
            'singular_name'      => __('Event', 'bb'),
            'menu_name'          => __('Events', 'bb'),
            'name_admin_bar'     => __('Add event', 'bb'),
            'add_new'            => __('Add new event', 'bb'),
            'add_new_item'       => __('Add event', 'bb'),
            'new_item'           => __('New event', 'bb'),
            'edit_item'          => __('Edit event', 'bb'),
            'view_item'          => __('View event', 'bb'),
            'all_items'          => __('All events', 'bb'),
            'search_items'       => __('Search events', 'bb'),
            'parent_item_colon'  => __('Parent event', 'bb'),
            'not_found'          => __('No events found', 'bb'),
            'not_found_in_trash' => __('No events found in Trash', 'bb'),
        ),
        'public'             => true,
        'exclude_from_search'=> false,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_nav_menus'  => false,
        'show_in_menu'       => true,
        'show_in_admin_bar'  => true,
//        'query_var'          => true,
        'capability_type'    => 'post',
        'has_archive'        => 'events',
        'rewrite'            => array('slug' => 'event', 'with_front' => false),
        'hierarchical'       => false,
        'menu_position'      => 5,
        'supports'           => array('title', 'excerpt', 'editor', 'thumbnail'),
        'taxonomies'         => array(),
    ));

	register_post_type('programm', array(
        'labels' => array(
            'name'               => __('Programmes', 'bb'),
            'singular_name'      => __('Programm', 'bb'),
            'menu_name'          => __('Programmes', 'bb'),
            'name_admin_bar'     => __('Add programm', 'bb'),
            'add_new'            => __('Add new programm', 'bb'),
            'add_new_item'       => __('Add programm', 'bb'),
            'new_item'           => __('New programm', 'bb'),
            'edit_item'          => __('Edit programm', 'bb'),
            'view_item'          => __('View programm', 'bb'),
            'all_items'          => __('All programmes', 'bb'),
            'search_items'       => __('Search programmes', 'bb'),
            'parent_item_colon'  => __('Parent programm', 'bb'),
            'not_found'          => __('No programmes found', 'bb'),
            'not_found_in_trash' => __('No programmes found in Trash', 'bb'),
        ),
        'public'             => true,
        'exclude_from_search'=> false,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_nav_menus'  => false,
        'show_in_menu'       => true,
        'show_in_admin_bar'  => true,
//        'query_var'          => true,
        'capability_type'    => 'post',
        'has_archive'        => false,
        'rewrite'            => array('slug' => 'programm', 'with_front' => false),
        'hierarchical'       => false,
        'menu_position'      => 5,
        'supports'           => array('title', 'excerpt', 'editor', 'thumbnail'),
        'taxonomies'         => array(),
    ));
}

} // if bb_custom_content


/* Alter post labels */
//function bb_change_post_labels($post_type, $args){ /* change assigned labels */
//    global $wp_post_types;
//
//    if($post_type != 'post')
//        return;

//    $labels = new stdClass();
//
//    $labels->name               = "Статьи";
//    $labels->singular_name      = "Статья";
//    $labels->add_new            = "Добавить новую";
//    $labels->add_new_item       = "Добавить новую";
//    $labels->edit_item          = "Редактировать статью";
//    $labels->new_item           = "Новая";
//    $labels->view_item          = "Просмотреть";
//    $labels->search_items       = "Поиск";
//    $labels->not_found          = "Не найдено";
//    $labels->not_found_in_trash = "В Корзине статьи не найдены";
//    $labels->parent_item_colon  = NULL;
//    $labels->all_items          = "Все статьи";
//    $labels->menu_name          = "Статьи";
//    $labels->name_admin_bar     = "Статья";
//
//    $wp_post_types[$post_type]->labels = $labels;
//}
//add_action('registered_post_type', 'bb_change_post_labels', 2, 2);
 
//function bb_change_post_menu_labels(){ /* change adming menu labels */
//    global $menu, $submenu;
//
//    $post_type_object = get_post_type_object('post');
//    $sub_label = $post_type_object->labels->all_items;
//    $top_label = $post_type_object->labels->name;
//
//    /* find proper top menu item */
//    $post_menu_order = 0;
//    foreach($menu as $order => $item){
//
//        if($item[2] == 'edit.php'){
//            $menu[$order][0] = $top_label;
//            $post_menu_order = $order;
//            break;
//        }
//    }
//
//    /* find proper submenu */
//    $submenu['edit.php'][$post_menu_order][0] = $sub_label;
//}
//add_action('admin_menu', 'bb_change_post_menu_labels');
 
//function bb_change_post_updated_labels($messages){     /* change updated post labels */
//    global $post;
//
//    $permalink = get_permalink($post->ID);
//
//    $messages['post'] = array(
//
//    0 => '',
//    1 => sprintf( 'Статья обновлена. <a href="%s">Просмотреть</a>', esc_url($permalink)),
//    2 => "Пользовательское поле обновлено",
//    3 => "Пользовательское поле удалено",
//    4 => "Статья обновлена",
//    5 => isset($_GET['revision']) ? sprintf('Редакция статьи восстановлена из %s', wp_post_revision_title((int)$_GET['revision'], false)) : false,
//    6 => sprintf('Статья опубликована. <a href="%s">Просмотреть</a>', esc_url($permalink)),
//    7 => "Статья сохранена",
//    8 => sprintf('Статья отправлена на рассмотрение. <a target="_blank" href="%s">Просмотреть</a>', esc_url(add_query_arg('preview','true', $permalink))),
//    9 => sprintf('Статья запланирована. <a target="_blank" href="%s">Просмотреть</a>', esc_url(add_query_arg('preview','true', $permalink))),
//    10 => sprintf('Черновик статьи обновлен. <a target="_blank" href="%s">Просмотреть</a>', esc_url(add_query_arg('preview', 'true', $permalink)))
//    );
//
//    return $messages;
//}
//add_filter('post_updated_messages', 'bb_change_post_updated_labels');

//add_action( 'p2p_init', 'apl_connection_types' );
//function apl_connection_types() {
//	p2p_register_connection_type( array(
//		'name' => 'project_post',
//		'from' => 'project',
//		'to'   => 'post',
//		'sortable'   => true,
//		'reciprocal' => false,
//		'prevent_duplicates' => true,
//		'admin_box' => array(
//			'show' => 'any',
//			'context' => 'normal',
//			'can_create_post' => true
//		),
//		'admin_column' => 'to'
//	) );
//}