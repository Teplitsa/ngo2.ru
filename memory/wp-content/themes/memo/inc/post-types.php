<?php
add_action('init', 'memo_custom_content', 20);
if( !function_exists('memo_custom_content') ) {
function memo_custom_content(){

    /** Existing post types settings: */
    //add_post_type_support('page', array('excerpt'));
	
    /** Post PT & taxonomies */
    register_taxonomy('place', 'post', array(
        'hierarchical'          => true,
        'labels'                => array(
            'name'                       => 'Места',
            'singular_name'              => 'Место',
            'search_items'               => 'Поиск мест',
            'popular_items'              => 'Популярные места',
            'all_items'                  => 'Все места',
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'edit_item'                  => 'Редактировать место',
            'update_item'                => 'Обновить место',
            'add_new_item'               => 'Добавить новое место',
            'new_item_name'              => 'Название нового места',
            'separate_items_with_commas' => 'Введите места, разделяя их запятыми',
            'add_or_remove_items'        => 'Добавить/удалить места',
            'choose_from_most_used'      => 'Выбрать из часто используемых',
            'not_found'                  => 'Места не найдены',
            'menu_name'                  => 'Места',
        ),
        'show_ui'               => true,
        'show_admin_column'     => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var'             => true,
        'rewrite'               => array('slug' => 'place'),
    ));
	
	register_taxonomy('type', 'document', array(
        'hierarchical'          => false,
        'labels'                => array(
            'name'                       => 'Типы документов',
            'singular_name'              => 'Тип документа',
            'search_items'               => 'Поиск типов',
            'popular_items'              => 'Популярные типы',
            'all_items'                  => 'Все типы документов',
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'edit_item'                  => 'Редактировать тип',
            'update_item'                => 'Обновить тип',
            'add_new_item'               => 'Добавить новый тип',
            'new_item_name'              => 'Название нового типа',
            'separate_items_with_commas' => 'Введите типы, разделяя их запятыми',
            'add_or_remove_items'        => 'Добавить/удалить типы',
            'choose_from_most_used'      => 'Выбрать из часто используемых',
            'not_found'                  => 'Типы не найдены',
            'menu_name'                  => 'Типы документов',
        ),
        'show_ui'               => true,
        'show_admin_column'     => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var'             => true,
        'rewrite'               => array('slug' => 'type'),
    ));


    // Remove category taxonomy for Posts PT:
   deregister_taxonomy_for_object_type('category', 'post');
   deregister_taxonomy_for_object_type('post_tag', 'post');
	
	/** Marker PT */
    register_post_type('marker', array(
        'labels'             => array(
            'name'               => 'Маркеры',
            'singular_name'      => 'Маркер',
            'menu_name'          => 'Маркеры',
            'name_admin_bar'     => 'Маркер',
            'add_new'            => 'Добавить новый',
            'add_new_item'       => 'Добавить новый маркер',
            'new_item'           => 'Новый маркер',
            'edit_item'          => 'Редактировать маркер',
            'view_item'          => 'Просмотреть маркер',
            'all_items'          => 'Все маркеры',
            'search_items'       => 'Искать маркеры',
            'parent_item_colon'  => 'Родительский маркер:',
            'not_found'          => 'Маркеров не найдено',
            'not_found_in_trash' => 'В корзине маркеров не найдено'
        ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'marker'),
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 5,
		'menu_icon'          => 'dashicons-location-alt',
        //        'taxonomies' => array('post_tag'),
        'supports'           => array('title', 'editor',)
    ));
	
	
    /** Book PT: */
    register_post_type('book', array(
        'labels'             => array(
            'name'               => 'Книги',
            'singular_name'      => 'Книга',
            'menu_name'          => 'Книги',
            'name_admin_bar'     => 'Книга',
            'add_new'            => 'Добавить новую',
            'add_new_item'       => 'Добавить новую книгу',
            'new_item'           => 'Новая книга',
            'edit_item'          => 'Редактировать книгу',
            'view_item'          => 'Просмотреть книгу',
            'all_items'          => 'Все книги',
            'search_items'       => 'Искать книги',
            'parent_item_colon'  => 'Родительская книга:',
            'not_found'          => 'Книг не найдено',
            'not_found_in_trash' => 'В корзине книг не найдено'
        ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'book'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
		'menu_icon'          => 'dashicons-book',
        'taxonomies'         => array('post_tag'),
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt',)
    ));

    

    /** Document PT: */
    register_post_type('document', array(
        'labels'             => array(
            'name'               => 'Документы',
            'singular_name'      => 'Документ',
            'menu_name'          => 'Документы',
            'name_admin_bar'     => 'Документ',
            'add_new'            => 'Добавить новый',
            'add_new_item'       => 'Добавить новый документ',
            'new_item'           => 'Новый документ',
            'edit_item'          => 'Редактировать документ',
            'view_item'          => 'Просмотреть документ',
            'all_items'          => 'Все документы',
            'search_items'       => 'Искать документы',
            'parent_item_colon'  => 'Родительский документ:',
            'not_found'          => 'Документов не найдено',
            'not_found_in_trash' => 'В корзине документов не найдено'
        ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'document'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
		'menu_icon'          => 'dashicons-media-default',
        'taxonomies'         => array('post_tag', 'type'),
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt',)
    ));

    
	
}

} // if memo_custom_content


/* Alter post labels */
//function memo_change_post_labels($post_type, $args){ /* change assigned labels */
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
//add_action('registered_post_type', 'memo_change_post_labels', 2, 2);
 
//function memo_change_post_menu_labels(){ /* change adming menu labels */
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
//add_action('admin_menu', 'memo_change_post_menu_labels');
 
//function memo_change_post_updated_labels($messages){     /* change updated post labels */
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
//add_filter('post_updated_messages', 'memo_change_post_updated_labels');

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