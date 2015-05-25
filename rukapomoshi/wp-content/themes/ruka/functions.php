<?php
/**
 * bb functions and definitions
 *
 * @package bb
 */

define('RUKA_VERSION', '1.0');
 
 
if ( ! isset( $content_width ) ) {
	$content_width = 640; /* pixels */
}

if ( ! function_exists( 'ruka_setup' ) ) :
function ruka_setup() {

	// Inits
	load_theme_textdomain( 'ruka', get_template_directory() . '/lang' );
//	add_theme_support( 'automatic-feed-links' );	
	add_theme_support( 'title-tag' );
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
	) );

	// Thumbnails
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size(350, 235, true ); // regular thumbnails
	//add_image_size('logo', 220, 210, true ); // logo thumbnail 
	//add_image_size('poster', 220, 295, true ); // poster in widget	
	add_image_size('embed', 640, 400, true ); // fixed size for embedding
	//add_image_size('long', 640, 280, true ); // long thumbnail for pages

	// Menus
	register_nav_menus(array(
		'primary'      => __('Primary Menu', 'ruka'),		
		'social'       => __('Social Buttons', 'ruka'),		
	));

	// Editor style
	//add_editor_style(array('css/editor-style.css'));
}
endif; // ruka_setup
add_action( 'after_setup_theme', 'ruka_setup' );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function ruka_widgets_init() {
	
	$config = array(
		'right' => array(
						'name' => 'Правая колонка',
						'description' => 'Общая боковая колонка справа'
					),		
		'home' => array(
						'name' => 'Главная',
						'description' => 'Динамическая область на главной странице'
					),		
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
		//'footer_4' => array(
		//				'name' => 'Футер - 4 кол.',
		//				'description' => 'Динамическая нижняя область - 4 колонка'
		//			)
		
	);
	
	
	foreach($config as $id => $sb) {
		
		$before = '<div id="%1$s" class="widget %2$s">';
		
		if(false !== strpos($id, 'footer')){
			$before = '<div id="%1$s" class="widget-bottom %2$s">';
		}
		elseif($id == 'right') {
			$before = '<div id="%1$s" class="widget bit sm-6 md-12 %2$s">';
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
add_action( 'widgets_init', 'ruka_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function ruka_scripts() {
	
	$theme_dir_url = get_template_directory_uri();
	
	// Styles
	$style_dependencies = array();
	
	//Google Fonts
	wp_enqueue_style(
		'ruka-fonts',
		'//fonts.googleapis.com/css?family=Arimo:400,700,400italic&subset=latin,cyrillic',
		$style_dependencies,
		false
	);
	$style_dependencies[] = 'ruka-fonts';	
	
	// Icons	
	wp_enqueue_style(
		'ruka-fontawesome',
		'//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css',
		$style_dependencies,
		false
	);
	$style_dependencies[] = 'ruka-fontawesome';
	
	// Fresco
	wp_enqueue_style(
		'ruka-fresco',
		$theme_dir_url . '/css/fresco.css',
		array(),
		RUKA_VERSION
	);
	$style_dependencies[] = 'ruka-fresco';
		
	// Stylesheet
	wp_enqueue_style(
		'ruka-style',
		$theme_dir_url . '/css/design.css',
		$style_dependencies,
		RUKA_VERSION
	);
	
	
	// Scripts
	$script_dependencies = array();

	// jQuery
	$script_dependencies[] = 'jquery';
	
	wp_enqueue_script(
		'ruka-fresco',
		$theme_dir_url . '/js/fresco.js',
		$script_dependencies,
		RUKA_VERSION,
		true
	);
	
	$script_dependencies[] = 'ruka-fresco';
	
	wp_enqueue_script(
		'ruka-imageloaded',
		$theme_dir_url . '/js/imagesloaded.pkgd.min.js',
		$script_dependencies,
		RUKA_VERSION,
		true
	);
	
	$script_dependencies[] = 'ruka-imageloaded';
	
	wp_enqueue_script('masonry');	
	$script_dependencies[] = 'masonry';
	
	wp_enqueue_script(
		'ruka-grids',
		$theme_dir_url . '/js/grids.min.js',
		$script_dependencies,
		RUKA_VERSION,
		true
	);
	
	$script_dependencies[] = 'ruka-grids';
	
	wp_enqueue_script(
		'ruka-front',
		$theme_dir_url . '/js/front.js',
		$script_dependencies,
		RUKA_VERSION,
		true
	);
	
}
add_action( 'wp_enqueue_scripts', 'ruka_scripts' );

add_action( 'admin_enqueue_scripts', 'ruka_admin_scripts' );
function ruka_admin_scripts() {
			
	wp_enqueue_style('ruka-admin', get_template_directory_uri().'/css/admin.css', array());
	
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


if(is_admin()){
	require get_template_directory() . '/inc/admin.php';
}
