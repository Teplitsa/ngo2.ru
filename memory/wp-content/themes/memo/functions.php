<?php
/**
 * bb functions and definitions
 *
 * @package bb
 */

define('MEMO_VERSION', '1.0');
 
 
if ( ! isset( $content_width ) ) {
	$content_width = 590; /* pixels */
}

if ( ! function_exists( 'memo_setup' ) ) :
function memo_setup() {

	// Inits
	load_theme_textdomain( 'memo', get_template_directory() . '/lang' );
//	add_theme_support( 'automatic-feed-links' );	
	add_theme_support( 'title-tag' );
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
	) );

	// Thumbnails
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size(320, 210, true ); // regular thumbnails
	add_image_size('portrait', 222, 280, true ); // logo thumbnail 	
	add_image_size('embed', 590, 350, true ); // fixed size for embedding
	add_image_size('marker', 220, 180, true ); // 

	// Menus
	register_nav_menus(array(
		'primary'      => __('Primary Menu', 'memo'),
		'secondary'    => __('Secondary Menu', 'memo'),		
		//'social'       => __('Social Buttons', 'memo'),		
	));

	// Editor style
	//add_editor_style(array('css/editor-style.css'));
}
endif; // memo_setup
add_action( 'after_setup_theme', 'memo_setup' );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function memo_widgets_init() {
	
	$config = array(
		'right' => array(
						'name' => 'Правая колонка',
						'description' => 'Общая боковая колонка справа'
					),
		'story' => array(
						'name' => 'Правая колонка - Записи',
						'description' => 'Боковая колонка справа на страницах записей'
					),	
		//'header' => array(
		//				'name' => 'Шапка сайта',
		//				'description' => 'Динамическая область в шапке сайта'
		//			),		
		'footer_1' => array(
						'name' => 'Футер',
						'description' => 'Динамическая нижняя область'
					),
		'footer_2' => array(
						'name' => 'Футер - 2 кол.',
						'description' => 'Динамическая нижняя область - 2 колонка'
					),
		'footer_3' => array(
						'name' => 'Футер - 3 кол.',
						'description' => 'Динамическая нижняя область - 3 колонка'
					),
		'footer_4' => array(
						'name' => 'Футер - 4 кол.',
						'description' => 'Динамическая нижняя область - 4 колонка'
					)
		
	);
	
	
	foreach($config as $id => $sb) {
		
		$before = '<div id="%1$s" class="widget %2$s">';
		
		if(false !== strpos($id, 'footer')){
			$before = '<div id="%1$s" class="widget bottom %2$s">';
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
add_action( 'widgets_init', 'memo_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function memo_scripts() {
	
	$theme_dir_url = get_template_directory_uri();
	
	// Styles
	$style_dependencies = array();
	
	//Google Fonts	
	wp_enqueue_style(
		'memo-gfont',
		'//fonts.googleapis.com/css?family=Open+Sans:400,300,400italic,600,700,700italic&subset=latin,cyrillic',
		$style_dependencies,
		false
	);
	$style_dependencies[] = 'memo-gfont';
		
	// Icons	
	wp_enqueue_style(
		'memo-fontawesome',
		'//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css',
		$style_dependencies,
		false
	);
	$style_dependencies[] = 'memo-fontawesome';
	
	// Fresco
	wp_enqueue_style(
		'memo-fresco',
		$theme_dir_url.'/css/fresco.css',
		array(),
		MEMO_VERSION
	);
	$style_dependencies[] = 'memo-fresco';
	
	if(is_page('karta')) {

        wp_enqueue_style('leaflet', 'http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.css');
        $style_dependencies[] = 'leaflet';

        wp_enqueue_style('leaflet-clustering', $theme_dir_url.'/css/MarkerCluster.css');
        $style_dependencies[] = 'leaflet-clustering';
    }
		
	// Stylesheet
	wp_enqueue_style(
		'memo-style',
		$theme_dir_url . '/css/design.css',
		$style_dependencies,
		MEMO_VERSION
	);
	
	
	// Scripts
	$script_dependencies = array();

	// jQuery
	$script_dependencies[] = 'jquery';
	
	wp_enqueue_script(
		'memo-fresco',
		$theme_dir_url . '/js/fresco.js',
		$script_dependencies,
		MEMO_VERSION,
		true
	);
	
	$script_dependencies[] = 'memo-fresco';
	
	wp_enqueue_script(
		'memo-imageloaded',
		$theme_dir_url . '/js/imagesloaded.pkgd.min.js',
		$script_dependencies,
		MEMO_VERSION,
		true
	);
	
	$script_dependencies[] = 'memo-imageloaded';
	
	//wp_enqueue_script('masonry');	
	//$script_dependencies[] = 'masonry';
	
	wp_enqueue_script(
		'memo-grids',
		$theme_dir_url . '/js/grids.min.js',
		$script_dependencies,
		MEMO_VERSION,
		true
	);
	
	$script_dependencies[] = 'memo-grids';
	
	if(is_page('karta')) {

        wp_enqueue_script('leaflet', 'http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js');
        $script_dependencies[] = 'leaflet';

        wp_enqueue_script('leaflet-clustering', $theme_dir_url.'/js/leaflet.markercluster.js', array('leaflet'));
        $script_dependencies[] = 'leaflet-clustering';
    }

	if(is_front_page()) {

		wp_enqueue_script(
			'memo-lettering',
			$theme_dir_url . '/js/jquery.lettering.js',
			$script_dependencies,
			'0.7.0',
			true
		);
	}
	
	wp_enqueue_script(
		'memo-front',
		$theme_dir_url.'/js/front.js',
		$script_dependencies,
		MEMO_VERSION,
		true
	);
	
}
add_action( 'wp_enqueue_scripts', 'memo_scripts' );

add_action( 'admin_enqueue_scripts', 'memo_admin_scripts' );
function memo_admin_scripts() {
			
	wp_enqueue_style('memo-admin', get_template_directory_uri().'/css/admin.css', array());
	
}




/**
 * Includes
 */

require get_template_directory().'/inc/aq_resizer.php';
require get_template_directory().'/inc/post-types.php';
require get_template_directory().'/inc/media.php';
require get_template_directory().'/inc/extras.php';
require get_template_directory().'/inc/template-tags.php';
require get_template_directory().'/inc/shortcodes.php';
require get_template_directory().'/inc/widgets.php'; 
require get_template_directory().'/inc/related.php';


if(is_admin()){
	require get_template_directory() . '/inc/admin.php';
}
