<?php
/** Child theme tags */

function grt_change_pm_labels($label, Leyka_Payment_Method $pm) {

    if($pm->gateway_id == 'quittance') {
        return $label;
    }

//    if($pm->id == 'chronopay_card_rebill') {
//        $label .= ' - регулярные платежи';
//    }

    foreach(leyka_get_gateways() as $gateway) {
        if($gateway->id == $pm->gateway_id) {
            return leyka_options()->opt_safe($this->full_id.'_label') != $label ?
                leyka_options()->opt_safe($this->full_id.'_label') : $label.' (через '.$gateway->name.')';
        }
    }

    return $label;
}
add_filter('leyka_get_pm_label', 'grt_change_pm_labels', 10, 2);

/** Logo (to support svg) **/
function grt_logo() {

	$src_base = get_stylesheet_directory_uri().'/img/';?>

<a class="blog-logo" href='<?php echo esc_url(home_url('/'));?>' title='<?php echo esc_attr(get_bloginfo('title'));?> &mdash; <?php echo esc_attr(get_bloginfo('description'));?>' rel='home'>

	<div class="sign"><img src="<?php echo $src_base.'logo.svg';?>" alt="<?php echo esc_attr(get_bloginfo('title'));?>" onerror="this.onerror=null;this.src=<?php echo $src_base.'logo.png';?>"></div>
	
	<div class="text"><img src="<?php echo $src_base.'logo-text.svg';?>" alt="<?php echo esc_attr(get_bloginfo('title'));?>" onerror="this.onerror=null;this.src=<?php echo $src_base.'logo-text.png';?>">
	<span class="tagline"><?php bloginfo('description');?></span>
	</div>
</a>
<?php }


/** display footer **/
add_action('grt_content_bottom', function(){
	get_template_part('partials/sidebar', 'bottom');
});


/** Shortcodes **/
add_shortcode('embed_post', 'embed_post_screen');
function embed_post_screen($attr){
	extract( shortcode_atts( array(
	      'id' => 0
     ), $attr ) );
	
	$epost = get_post(intval($id));
	if(!empty($epost))
		return apply_filters('the_content', $epost->post_content);
	else	
		return '';
}


add_shortcode('posts_by_tag', 'posts_by_tag_screen');
function posts_by_tag_screen($attr){
	extract( shortcode_atts( array(
	      'tag'     => '',
		  'orderby' => 'date',
		  'order'   => 'DESC'
     ), $attr ) );
	
	$args = array(
		'post_type' => 'post',
		'posts_per_page' => -1,
		'tax_query' => array(
			array(
				'taxonomy' => 'post_tag',
				'field' => 'slug',
				'terms' => $tag
			)
		),
		'orderby' => $orderby,
		'order' => $order
	);
	
	$query = new WP_Query($args);
	if(!$query->have_posts())
		return '';
	
	$out .= "<div class='embed-posts-list'><ul>";
	while($query->have_posts()): $query->the_post();
	$out .= "<li><a href='".get_the_permalink()."'>".get_the_title()."</a></li>";
	endwhile; wp_reset_postdata();
	$out .= "</ul></div>";
	
	return $out;
}


/**  Redirects **/

add_action('template_redirect', 'grtch_old_redirects', 1);
function grtch_old_redirects() {


    if( !is_404() ) {
        return;
    }

    $str_to_lookup = $_SERVER['REQUEST_URI']; 	
	$str_to_lookup = str_replace('/leyka-test', '', $str_to_lookup); //on dev
	
    $redirects = array(
        '/about/'     => '/leyka-standalone/',
        '/functions/' => '/leyka-standalone/',
        '/docs/'      => '/instruction/',
		'/faq/'       => '/instruction/',
		'/download/'  => '/leyka-standalone/'
    );
//
	
    if( !empty($redirects[$str_to_lookup])) {		
        wp_redirect(site_url($redirects[$str_to_lookup]), 301);
        die();
    }
	
	
}


/**
 * Inject  top link  HTML
 * require <body> tag to have id ='top'
 **/
function frl_print_top_link(){

	if(!is_admin()):
 ?>	
	<div id="top-link">
		<a href="#top">К началу</a>
	</div>
	
<?php endif; 
}
add_action('wp_footer', 'frl_print_top_link');