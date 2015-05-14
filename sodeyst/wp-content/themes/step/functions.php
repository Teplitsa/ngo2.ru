<?php
/**
 * bb functions and definitions
 *
 * @package bb
 */

define('STEP_VERSION', '1.0');
 
 
if ( ! isset( $content_width ) ) {
	$content_width = 640; /* pixels */
}

if ( ! function_exists( 'step_setup' ) ) :
function step_setup() {

	// Inits
	load_theme_textdomain( 'step', get_template_directory() . '/lang' );
//	add_theme_support( 'automatic-feed-links' );	
	add_theme_support( 'title-tag' );
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
	) );

	// Thumbnails
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size(395, 230, true ); // regular thumbnails
	add_image_size('long', 395, 210, true ); // logo thumbnail 
	//add_image_size('poster', 220, 295, true ); // poster in widget	
	add_image_size('embed', 640, 400, true ); // fixed size for embedding
	//add_image_size('long', 640, 280, true ); // long thumbnail for pages

	// Menus
	register_nav_menus(array(
		'primary'      => __('Primary Menu', 'step'),
		'secondary'    => __('Secondary Menu', 'step'),
		'footer_left'  => __('Left Menu in footer', 'step'),
		'footer_right' => __('Right Menu in footer', 'step'),
		'social'       => __('Social Buttons', 'step'),
		'langs'        => __('Languages', 'step')
	));

	// Editor style
	//add_editor_style(array('css/editor-style.css'));
}
endif; // step_setup
add_action( 'after_setup_theme', 'step_setup' );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function step_widgets_init() {
	
	$config = array(
		'right' => array(
						'name' => 'Правая колонка',
						'description' => 'Общая боковая колонка справа'
					),		
		'header' => array(
						'name' => 'Шапка сайта',
						'description' => 'Динамическая область в шапке сайта'
					),		
		'footer_1' => array(
						'name' => 'Футер',
						'description' => 'Динамическая нижняя область'
					),
		//'footer_2' => array(
		//				'name' => 'Футер - 2 кол.',
		//				'description' => 'Динамическая нижняя область - 2 колонка'
		//			),
		//'footer_3' => array(
		//				'name' => 'Футер - 3 кол.',
		//				'description' => 'Динамическая нижняя область - 3 колонка'
		//			)
		
	);
	
	
	foreach($config as $id => $sb) {
		
		$before = '<div id="%1$s" class="widget %2$s">';
		
		if(false !== strpos($id, 'footer')){
			$before = '<div id="%1$s" class="widget %2$s">';
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
add_action( 'widgets_init', 'step_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function step_scripts() {
	
	$theme_dir_url = get_template_directory_uri();
	
	// Styles
	$style_dependencies = array();
	
	// Icons	
	wp_enqueue_style(
		'step-fontawesome',
		'//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css',
		$style_dependencies,
		false
	);
	$style_dependencies[] = 'step-fontawesome';
	
	// Fresco
	wp_enqueue_style(
		'step-fresco',
		$theme_dir_url . '/css/fresco.css',
		array(),
		STEP_VERSION
	);
	$style_dependencies[] = 'step-fresco';
		
	// Stylesheet
	wp_enqueue_style(
		'step-style',
		$theme_dir_url . '/css/design.css',
		$style_dependencies,
		STEP_VERSION
	);
	
	
	// Scripts
	$script_dependencies = array();

	// jQuery
	$script_dependencies[] = 'jquery';
	
	wp_enqueue_script(
		'step-fresco',
		$theme_dir_url . '/js/fresco.js',
		$script_dependencies,
		STEP_VERSION,
		true
	);
	
	$script_dependencies[] = 'step-fresco';
	
	wp_enqueue_script(
		'step-imageloaded',
		$theme_dir_url . '/js/imagesloaded.pkgd.min.js',
		$script_dependencies,
		STEP_VERSION,
		true
	);
	
	$script_dependencies[] = 'step-imageloaded';
	
	//wp_enqueue_script('masonry');	
	//$script_dependencies[] = 'masonry';
	
	wp_enqueue_script(
		'step-grids',
		$theme_dir_url . '/js/grids.min.js',
		$script_dependencies,
		STEP_VERSION,
		true
	);
	
	$script_dependencies[] = 'step-grids';
	
	wp_enqueue_script(
		'step-front',
		$theme_dir_url . '/js/front.js',
		$script_dependencies,
		STEP_VERSION,
		true
	);
	
}
add_action( 'wp_enqueue_scripts', 'step_scripts' );

add_action( 'admin_enqueue_scripts', 'step_admin_scripts' );
function step_admin_scripts() {
			
	wp_enqueue_style('step-admin', get_template_directory_uri().'/css/admin.css', array());
	
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
