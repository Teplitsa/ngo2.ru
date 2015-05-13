<?php
/*
Plugin Name: LA Pretty Galleries
Description: The embed media functionality for WP site.
Version: 1.1
Author: Teplitsa
Author URI: http://te-st.ru/
License: GPLv2 or later
Contributors:
    Lev Zvyagincev aka ahaenor (ahaenor@gmail.com)
	Anna Ladoshkina aka foralien (webdev@foralien.com)

License: GPLv2 or later

Сhangelog
1.2 - CSS corrections for tstsite compatibility
1.0 - initial
*/

if( !defined('ABSPATH') ) exit; // Exit if accessed directly

/**
 * REMINDER
 * This is just a starting point.
 * After testing the use case let's make it poetic
 **/


add_action('init', 'frl_gallery_shortcodes', 1);
function frl_gallery_shortcodes(){
    remove_shortcode('gallery');
    add_shortcode('gallery', 'frl_gallery_screen');
	add_shortcode('embed_media', 'frl_embed_media_screen');
}

function frl_gallery_screen($atts) {
    extract(shortcode_atts(array('ids' => '', 'columns' => 3), $atts));

    /** @var $ids From extract */
    /** @var $columns From extract */

    $out = '';
    if(empty($ids))
        return $out; // no items

    $args = array(
        'post_type'   => 'attachment',
        'post_status' => 'inherit',
        'orderby'     => 'post__in',
        'order'       => 'ASC',
        'post_mime_type' => 'image',
        'post__in'     => explode(',', $ids),
        'posts_per_page' => -1
    );

    $query = new WP_Query($args);
    if(empty($query->posts))
        return $out; //no attachments

    return frl_gallery_output($query->posts, $columns);
}

/** Creates the markup */
function frl_gallery_output($items, $columns) {
    $columns = intval($columns);

    if($columns == 0 || $columns > 8)
        $columns = 5;
    elseif($columns == 2) // Not supported in styles, but sometimes is used
        $columns = 3;

    $out = "<div class='frl-gallery'><ul class='clearfix cols-{$columns}'>";
    $gallery_ref = uniqid('gallery-');

    foreach($items as $picture) {
		$size = apply_filters('lpg_thumbnail_size', 'thumbnail');
        $img = wp_get_attachment_image($picture->ID, $size, false, array('title' => ''));
        $url = wp_get_attachment_url($picture->ID);
        $title = esc_attr(stripslashes($picture->post_excerpt));

        // HTML for lightbox
        $out .= '<li>';
        $out .= "<a data-fresco-group='{$gallery_ref}' href='{$url}' data-fresco-caption='{$title}' rel='image-overlay' class='img-padder fresco'>{$img}</a>";
        $out .= '</li>';
    }

    $out .= '</ul></div>';

    return $out;
}

/**
 * Embed shortcode
 * it's going to support youtube, slideshare, documenta with help Google viewer
 * and and just some embed code
 **/

function frl_embed_media_screen($atts, $content){
	
	
	extract(shortcode_atts(array('source' => 'iframe', 'css' => ''), $atts));
	
	$out = '';
	$src = trim($content);	
	if(empty($src))
		return ''; // nothing to print		
	
	
	if($source == 'youtube'){
		$out = "<figure class='youtube video embed-media  {$css}'>";

		$embed = wp_oembed_get($src, array('width'=>100, 'height' => 75));
		if(!$embed)
			return ''; //we got error
		
		$embed = str_replace('100', '100%', $embed);
		$embed = str_replace('75', '100%', $embed);
		
		$out .= "<div class='embed-frame'>{$embed}</div>";	
		$out .= "</figure>";
		
	} elseif($source == 'slideshare') {
		
		$out = "<figure class='slideshare embed-media {$css}'>";
			
		$embed = wp_oembed_get($src, array('width'=>100, 'height' => 75));
		if(!$embed)
			return ''; //we got error
		
		$embed = str_replace('102', '100%', $embed);
		$embed = str_replace('76', '100%', $embed);
		
		$out .= "<div class='embed-frame'>{$embed}</div>";		
		$out .= "</figure>";
	
	} elseif($source == 'gdocs') {
		
		$src = esc_url($src);
		$label = apply_filters('gdocs_download_text', 'Скачать &raquo;'); //to-do: make this localizable
		$download = "<a href='{$src}' target='_blank'>{$label}</a>";
		if(empty($css))
			$css = 'portrait';
			
		$out = frl_get_viewer_markup($src, $download, $css);
		
	} else { //some embed code
		
		$out = "<figure class='iframe embed-media $css'>";
		$embed = do_shortcode((stripslashes($src))); //to-do: filtering for security
		$out .= "<div class='embed-frame'>{$embed}</div>";
		$out .= "</figure>";

	}
	
	
	return $out;
	
}


/**
 * Get mimes supported by Gdocs
 **/
function frl_gdocs_supported_formats() {
	
	$mimes = array(
		// ext		=>	mime_type
		"ai"		=>	"application/postscript",
		"doc"		=>	"application/msword",
		"docx"		=>	"application/vnd.openxmlformats-officedocument.wordprocessingml",
		"dxf"		=>	"application/dxf",
		"eps"		=>	"application/postscript",
		"otf"		=>	"font/opentype",
		"pages"		=>	"application/x-iwork-pages-sffpages",
		"pdf"		=>	"application/pdf",
		"pps"		=>	"application/vnd.ms-powerpoint",
		"ppt"		=>	"application/vnd.ms-powerpoint",
		"pptx"		=>	"application/vnd.openxmlformats-officedocument.presentationml",
		"ps"		=>	"application/postscript",
		"psd"		=>	"image/photoshop",
		"rar"		=>	"application/rar",
		"svg"		=>	"image/svg+xml",
		"tif"		=>	"image/tiff",
		"tiff"		=>	"image/tiff",
		"ttf"		=>	"application/x-font-ttf",
		"xls"		=>	"application/vnd.ms-excel",
		"xlsx"		=>	"application/vnd.openxmlformats-officedocument.spreadsheetml",
		"xps"		=>	"application/vnd.ms-xpsdocument",
		"zip"		=>	"application/zip"
	);
	
	return $mimes;
}


/**
 * Helper to build google viewer code
 * for cross-class usage - temp: may be moved into media module
 **/

function frl_get_viewer_markup($src, $caption, $custom_class = 'portrait'){
    
    if(empty($src))
        return $src;
    
	$src = "http://docs.google.com/viewer?url=".rawurlencode($src).'&embedded=true';
	$embed = "<iframe src='{$src}' width='100%' height='100%' frameborder='0'></iframe>";
		
	$caption = (!empty($caption)) ? "<div class='gdocs-caption'><span>{$caption}</span></div>" : '';
		
	$out = "<figure class='embed-media gdocs {$custom_class}'>";	
	$out .= "<div class='embed-frame'>{$embed}</div>";	
	$out .= "</figure>";
	$out .= $caption;
		
	return $out;
}


/**
 * Lightbox for linked images
 **/
add_filter('media_send_to_editor', 'frl_media_send_to_editor_filter', 2, 3);
function frl_media_send_to_editor_filter($html, $id, $attachment) {
		
	$post = get_post($id);		
	//var_dump($attachment);
	
	if (false !== strpos($post->post_mime_type, 'image')) { //image shortcode
		
		if(strpos($attachment['url'], '.jpg') || strpos($attachment['url'], '.png'))
			$html = str_replace('<a ', '<a class="fresco" ', $html);
							
	}
	
	//build code
	
	
	return $html;
}



/** Include all scripts and styles */
add_action('wp_enqueue_scripts', 'la_pg_scripts');
function la_pg_scripts() {
    wp_enqueue_script(
        'la-pretty-galleries-script',
        plugins_url('/js/fresco.js', __FILE__),
        array('jquery')
    );

    wp_enqueue_style(
        'la-pretty-galleries-fresco',
        plugins_url('/css/fresco.css', __FILE__),
        array()
    );
	wp_enqueue_style(
        'la-pretty-galleries-styles',
        plugins_url('/css/la-pg.css', __FILE__),
        array()
    );
}