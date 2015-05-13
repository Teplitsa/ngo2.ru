<?php
/**
 * Blank functions and definitions
 *
 * @package Blank
 */

/**
 * Initials
 **/
if(!isset($content_width))
	$content_width = 640; /* pixels */

if(!isset($tst_main_w)){ //setting of main content wrappers
	
	$tst_nav_w = 0;
	$tst_main_w = 8;
	$tst_side_w = 4;
}


if ( ! function_exists( 'tst_setup' ) ) :
function tst_setup() {

	/**
	 * Make theme available for translation
	 * Translations can be filed in the /languages/ directory
	 * If you're building a theme based on Blank, use a find and replace
	 * to change 'blank' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'tst', get_template_directory() . '/languages' );

	
	//add_theme_support( 'automatic-feed-links' );

	/**
	 * Images
	 **/
	add_theme_support( 'post-thumbnails' );
	
	/* image sizes */
	set_post_thumbnail_size(390, 244, true ); // regular thumbnails
	add_image_size('logo', 220, 140, true ); // logo thumbnail 
	//add_image_size('poster', 220, 295, true ); // poster in widget	
	add_image_size('embed', 640, 400, true ); // fixed size for embending
	add_image_size('long', 640, 280, true ); // long thumbnail for pages

	/**
	 * This theme uses wp_nav_menu() in one location.
	 */
	register_nav_menus( array(
		'primary'   => 'Главное меню',
		'auxiliary' => 'Вспомогательное меню',		
		'social'    => 'Социальные кнопки'
	));

	/**
	 * Enable support for Post Formats
	 */
	add_theme_support( 'post-formats', array('image', 'video', 'gallery'));

	
}
endif; // blank_setup
add_action( 'after_setup_theme', 'tst_setup' );


/**
 * Register widgetized area and update sidebar with default widgets
 */
function tst_widgets_init() {
	
	$config = array(
		'common' => array(
						'name' => 'Боковая колонка',
						'description' => 'Общая боковая колонка справа'
					),
		'helper' => array(
						'name' => 'Навигационная колонка',
						'description' => 'Навигационная боковая колонка слева'
					),
		'header' => array(
						'name' => 'Шапка',
						'description' => 'Динамическая область в шапке сайта'
					),				
		'footer_one' => array(
						'name' => 'Футер - 1/3',
						'description' => 'Динамическая нижняя область - 1 колонка'
					),
		'footer_two' => array(
						'name' => 'Футер - 2/3',
						'description' => 'Динамическая нижняя область - 2 колонка'
					),
		'footer_three' => array(
						'name' => 'Футер - 3/3',
						'description' => 'Динамическая нижняя область - 3 колонка'
					),
		'home_one' => array(
						'name' => 'Главная - 1/3',
						'description' => 'Динамическая нижняя область - 1 колонка'
					),
		'home_two' => array(
						'name' => 'Главная - 2/3',
						'description' => 'Динамическая нижняя область - 2 колонка'
					),
		'home_three' => array(
						'name' => 'Главная - 3/3',
						'description' => 'Динамическая нижняя область - 3 колонка'
					)	
	);
		
	
	foreach($config as $id => $sb) {
		
		$before = '<div id="%1$s" class="widget %2$s">';
		
		if(false !== strpos($id, 'footer')){
			$before = '<div id="%1$s" class="widget %2$s bottom">';
		}
		elseif(false !== strpos($id, 'header')) {
			$before = '<div id="%1$s" class="header-block">';
		}
		
		register_sidebar(array(
			'name' => $sb['name'],
			'id' => $id.'-sidebar',
			'description' => $sb['description'],
			'before_widget' => $before,
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		));
	}
}
add_action( 'widgets_init', 'tst_widgets_init' );


/**
 * Enqueue scripts and styles
 */
function tst_scripts() {
	
	$url = get_template_directory_uri();
		
	wp_enqueue_style('gfonts', 'http://fonts.googleapis.com/css?family=Open+Sans|PT+Serif&subset=latin,cyrillic', array());	
	wp_enqueue_style('defaults', $url.'/css/defaults.css', array());	
	wp_enqueue_style('design', $url.'/css/design.css', array('dashicons'));
	
	
	wp_enqueue_script('front', $url.'/js/front.js', array('jquery'), '1.0', true);
	
}
add_action( 'wp_enqueue_scripts', 'tst_scripts' );

function tst_admin_scripts() {
	
	$url = get_template_directory_uri();	
	
	wp_enqueue_style('tst-admin', $url.'/css/admin.css', array());
	
}
add_action( 'admin_enqueue_scripts', 'tst_admin_scripts' );

/**
 * Custom additions.
 */
require get_template_directory().'/inc/template-tags.php';
require get_template_directory().'/inc/extras.php';
require get_template_directory().'/inc/related.php';
require get_template_directory().'/inc/events.php';
require get_template_directory().'/inc/home.php';

