<?php
/** Child Theme functions **/
define('GRTCH_VERSION', '1.0');



/** Child Scripts and styles **/

// Front:
add_action('wp_enqueue_scripts', 'grtch_front_scripts', 15);
function grtch_front_scripts() {

    $theme_dir_url = get_stylesheet_directory_uri();
	
	// Scripts
	$script_dependencies = array('grt-front');
	wp_enqueue_script(
        'grtch-front',
        $theme_dir_url.'/js/front.js',
        $script_dependencies,
		GRTCH_VERSION,
		true
    );
	
   
	// Styles
	$style_dependencies = array('grt-style');	
	wp_enqueue_style(
		'grtch-style',
		$theme_dir_url.'/css/design.css',
		$style_dependencies,
		GRTCH_VERSION
	);
	$style_dependencies = array('grtch-style');	
	
	wp_enqueue_style(
		'grtch-fixes',
		$theme_dir_url.'/css/fixes.css',
		$style_dependencies,
		GRTCH_VERSION
	);
}

/** Widget area **/
add_action('widgets_init', 'grtch_register_sidebar', 15);
function grtch_register_sidebar() {
	
	register_sidebar(array(
	  'name' => __( 'Footer - 1/3', 'grtch' ),
	  'id' => 'footer_one',
	  'description' => __( 'Bottom widgetized area', 'grtch' ),
	  'before_title' => '<h3 class="widget-title">',
	  'after_title' => '</h3>',
	  'before_widget' => '<div class="widget %2$s"><div class="widget-content">',
	  'after_widget' => '</div></div>'
	));
	
	register_sidebar(array(
	  'name' => __( 'Footer - 2/3', 'grtch' ),
	  'id' => 'footer_two',
	  'description' => __( 'Bottom widgetized area', 'grtch' ),
	  'before_title' => '<h3 class="widget-title">',
	  'after_title' => '</h3>',
	  'before_widget' => '<div class="widget %2$s"><div class="widget-content">',
	  'after_widget' => '</div></div>'
	));
	
	register_sidebar(array(
	  'name' => __( 'Footer - 3/3', 'grtch' ),
	  'id' => 'footer_three',
	  'description' => __( 'Bottom widgetized area', 'grtch' ),
	  'before_title' => '<h3 class="widget-title">',
	  'after_title' => '</h3>',
	  'before_widget' => '<div class="widget %2$s"><div class="widget-content">',
	  'after_widget' => '</div></div>'
	));
}


add_action( 'wp_footer', 'tst_ga_script', 100 );
function tst_ga_script(){
	
?>
	<script src="<?php echo get_stylesheet_directory_uri();?>/js/ga-events.js" type="text/javascript"></script>
<?php
}






/** Includes **/
require get_stylesheet_directory().'/inc/post-types.php';
require get_stylesheet_directory().'/inc/child-extras.php';