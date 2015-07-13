<?php
/**
 * Functions and definitions
 **/

define('TST_VERSION', '1.0');
 
 
if ( ! isset( $content_width ) ) {
	$content_width = 640; /* pixels */
}

if ( ! function_exists( 'tst_setup' ) ) :
function tst_setup() {

	// Inits
	load_theme_textdomain('tst', get_template_directory() . '/lang');
//	add_theme_support( 'automatic-feed-links' );	
	add_theme_support( 'title-tag' );
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
	) );

	// Thumbnails
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size(395, 222, true ); // regular thumbnails 16:9
	add_image_size('thumbnail-extra', 600, 337, true ); // large thumbnail 16:9
	add_image_size('embed', 600, 420, true ); // fixed size for embedding 4:3
	add_image_size('embed-small', 350, 245, true ); // fixed size cards 4:3
	add_image_size('thumbnail-long', 810, 320, true ); // large thumbnail 16:9

	// Menus
	register_nav_menus(array(
		'primary'      => __('Primary Menu', 'tst'),				
		'social'       => __('Social Buttons', 'tst')
	));

	// Editor style
	//add_editor_style(array('css/editor-style.css'));
}
endif; // tst_setup
add_action( 'after_setup_theme', 'tst_setup' );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function tst_widgets_init() {
	
	$config = array(
		'right' => array(
						'name' => 'Правая колонка',
						'description' => 'Общая боковая колонка справа'
					),		
		//'header' => array(
		//				'name' => 'Шапка сайта',
		//				'description' => 'Динамическая область в шапке сайта'
		//			),		
		'footer_1' => array(
						'name' => 'Футер - 1 кол.',
						'description' => 'Динамическая нижняя область - 1 колонка'
					),
		'footer_2' => array(
						'name' => 'Футер - 2 кол.',
						'description' => 'Динамическая нижняя область - 2 колонка'
					),
		'footer_3' => array(
						'name' => 'Футер - 3 кол.',
						'description' => 'Динамическая нижняя область - 3 колонка'
					),
		'bottom' => array(
						'name' => 'Нижняя панель',
						'description' => 'Динамическая область на нижней панели'
					)
		
	);
	
	
	foreach($config as $id => $sb) {
		
		$before = '<div id="%1$s" class="widget %2$s">';
		
		if(false !== strpos($id, 'footer')){
			$before = '<div id="%1$s" class="widget-bottom %2$s">';
		}		
		
		register_sidebar(array(
			'name' => $sb['name'],
			'id' => $id.'-sidebar',
			'description' => $sb['description'],
			'before_widget' => $before,
			'after_widget' => '</div>',
			'before_title' => '<h5 class="widget-title">',
			'after_title' => '</h5>',
		));
	}
}
add_action( 'widgets_init', 'tst_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function tst_scripts() {
	
	$theme_dir_url = get_template_directory_uri();
	$style_dependencies = array();
	$script_dependencies = array();
	
	
	// Styles	
	wp_enqueue_style(
		'tst-roboto',
		'//fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic&subset=latin,cyrillic',
		$style_dependencies,
		TST_VERSION
	);
	
	$style_dependencies[] = 'tst-roboto';
	
	wp_enqueue_style(
		'tst-material-icons',
		'//fonts.googleapis.com/icon?family=Material+Icons',
		$style_dependencies,
		TST_VERSION
	);
	
	$style_dependencies[] = 'tst-material-icons';
	
	
	wp_enqueue_style(
		'tst-design',
		$theme_dir_url . '/css/design.css',
		$style_dependencies,
		TST_VERSION
	);
	
	// Scripts
	
	// MDL
	wp_enqueue_script(
		'tst-mdl',
		$theme_dir_url . '/js/material.min.js',
		array(),
		TST_VERSION,
		false
	);
	
	// jQuery
	$script_dependencies[] = 'jquery';
			
	wp_enqueue_script(
		'tst-front',
		$theme_dir_url . '/js/front.js',
		$script_dependencies,
		TST_VERSION,
		true
	);
	
	
}
add_action( 'wp_enqueue_scripts', 'tst_scripts' );

add_action( 'admin_enqueue_scripts', 'tst_admin_scripts' );
function tst_admin_scripts() {
			
	wp_enqueue_style('tst-admin', get_template_directory_uri().'/css/admin.css', array());
	
}




/**
 * Includes
 */

require get_template_directory().'/inc/aq_resizer.php';
require get_template_directory().'/inc/post-types.php';
//require get_template_directory().'/inc/media.php';
require get_template_directory().'/inc/extras.php';
require get_template_directory().'/inc/template-tags.php';
//require get_template_directory().'/inc/shortcodes.php';
require get_template_directory().'/inc/widgets.php';
require get_template_directory().'/inc/related.php';
require get_template_directory().'/inc/calendar.php';

if(is_admin()){
	require get_template_directory() . '/inc/admin.php';
}
