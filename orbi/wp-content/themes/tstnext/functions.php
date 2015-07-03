<?php
/**
 * Functions and definitions
 **/

define('TST_VERSION', '1.0');
 
 
if ( ! isset( $content_width ) ) {
	$content_width = 710; /* pixels */
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
	set_post_thumbnail_size(395, 230, true ); // regular thumbnails
	add_image_size('long', 395, 210, true ); // logo thumbnail 
	//add_image_size('poster', 220, 295, true ); // poster in widget	
	add_image_size('embed', 710, 420, true ); // fixed size for embedding
	//add_image_size('long', 640, 280, true ); // long thumbnail for pages

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
		'header' => array(
						'name' => 'Шапка сайта',
						'description' => 'Динамическая область в шапке сайта'
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
	
	// Styles
	$style_dependencies = array();
	
	
	// Fresco
	wp_enqueue_style(
		'tst-fresco',
		$theme_dir_url . '/css/fresco.css',
		array(),
		TST_VERSION
	);
	$style_dependencies[] = 'tst-fresco';
	
	// Stylesheet
	wp_enqueue_style(
		'tst-materialize',
		$theme_dir_url . '/css/materialize.css',
		$style_dependencies,
		TST_VERSION
	);
	$style_dependencies[] = 'tst-materialize';
	
	// Social buttons
	wp_enqueue_style(
		'tst-social-buttons',
		$theme_dir_url . '/css/social-likes_flat.css',
		array(),
		'3.0.14'
	);
	$style_dependencies[] = 'tst-social-buttons';
	
	// Stylesheet
	wp_enqueue_style(
		'tst-style',
		$theme_dir_url . '/css/design.css',
		$style_dependencies,
		TST_VERSION
	);
	
	
	// Scripts
	$script_dependencies = array();

	// jQuery
	$script_dependencies[] = 'jquery';
	
	wp_enqueue_script(
		'tst-fresco',
		$theme_dir_url . '/js/fresco.js',
		$script_dependencies,
		TST_VERSION,
		true
	);
	
	$script_dependencies[] = 'tst-fresco';
	
	wp_enqueue_script(
		'tst-imageloaded',
		$theme_dir_url . '/js/imagesloaded.pkgd.min.js',
		$script_dependencies,
		TST_VERSION,
		true
	);
	
	$script_dependencies[] = 'tst-imageloaded';
	
	//wp_enqueue_script('masonry');	
	//$script_dependencies[] = 'masonry';
	
	wp_enqueue_script(
		'tst-grids',
		$theme_dir_url . '/js/grids.min.js',
		$script_dependencies,
		TST_VERSION,
		true
	);
	
	$script_dependencies[] = 'tst-grids';
	
	wp_enqueue_script(
		'tst-materialize',
		$theme_dir_url . '/js/materialize.min.js',
		$script_dependencies,
		TST_VERSION,
		true
	);
	
	$script_dependencies[] = 'tst-materialize';
	
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
require get_template_directory().'/inc/media.php';
require get_template_directory().'/inc/extras.php';
require get_template_directory().'/inc/template-tags.php';
require get_template_directory().'/inc/shortcodes.php';
require get_template_directory().'/inc/widgets.php';
require get_template_directory().'/inc/related.php';


if(is_admin()){
	require get_template_directory() . '/inc/admin.php';
}
