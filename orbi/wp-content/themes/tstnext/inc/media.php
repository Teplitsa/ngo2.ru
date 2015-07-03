<?php
/**
 * Common media functions
 **/

/** Custom image size for medialib **/
add_filter('image_size_names_choose', 'tst_medialib_custom_image_sizes');
function tst_medialib_custom_image_sizes($sizes) {
	
	$addsizes = apply_filters('tst_medialib_custom_image_sizes', array(
		"embed" => 'Средний - Фикс.'
	));	
	
	return array_merge($sizes, $addsizes);
}


/** Captions - contain post_excerpt and post_content fields **/
add_filter('image_add_caption_text', 'tst_image_caption', 2,2);
function tst_image_caption($caption, $id){
	
	$post = get_post($id);
	if(!empty($post->post_excerpt) || !empty($post->post_content)){
		$cap[] = $post->post_excerpt;
		$cap[] = $post->post_content;
		$caption = implode(' ', $cap);				
	}
	
	return $caption;
}


/** Lightbox for linked images (fresco) **/
add_filter('media_send_to_editor', 'tst_media_send_to_editor_filter', 2, 3);
function tst_media_send_to_editor_filter($html, $id, $attachment) {
		
	$post = get_post($id);		
		
	if (false !== strpos($post->post_mime_type, 'image')) { //image shortcode
		
		if(strpos($html, '<a') && (strpos($attachment['url'], '.jpg') || strpos($attachment['url'], '.png'))){
			//$html = str_replace('<a ', '<a class="fresco" ', $html);
			$pattern = "/<a(.*?)href=('|\")(.*?).(bmp|gif|jpeg|jpg|png)('|\")(.*?)>/i";
			$caption = '';
			if(!empty($post->post_excerpt) || !empty($post->post_content)){
				$caption[] = $post->post_excerpt;
				$caption[] = $post->post_content;
				$caption = esc_attr(strip_tags(implode(' ', $caption)));				
			}
			
			$replacement = '<a$1class="fresco" data-fresco-caption="'.$caption.'" href=$2$3.$4$5$6</a>';			
			$html = preg_replace($pattern, $replacement, $html);
			
		}
	}
		
	return $html;
}


/** Gallery templates **/
add_action('init', 'lam_gallery_shortcodes', 1);
function lam_gallery_shortcodes(){
    
	remove_shortcode('gallery');
    add_shortcode('gallery', 'lam_gallery_screen');
	
}

function lam_gallery_screen($atts){	
	
	extract(shortcode_atts(array('ids' => '', 'columns' => 3, 'format' => 'lightbox'), $atts));
  

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
	
	if($format == 'lightbox'){ //default fresco-style gallery
		return lam_lightbox_gallery_output($query->posts, $columns);
	
	}	
}

function lam_lightbox_gallery_output($items, $columns) {
	
	$columns = intval($columns);

    if($columns == 0 || $columns > 8)
        $columns = 5;    

    $out = "<div class='lam-gallery'><ul class='lam-clearfix cols-{$columns}'>";
    $gallery_ref = uniqid('gallery-');

    foreach($items as $picture) {
		$size = apply_filters('lpg_thumbnail_size', 'post-thumbnail'); //backward compat		
		
		$caption = array();
		if(!empty($picture->post_excerpt)){
			$caption[] = $picture->post_excerpt;
		}
	
		if(!empty($picture->post_content)) {
            $caption[] = $picture->post_content;
        }			
		
		$fresco_cap = strip_tags(str_replace('"', "'", apply_filters('frl_the_title', implode(' ', $caption))));
		
		$attr = array(
			'title' => '',
			'alt' => (!empty($title)) ? $title : ''
		);
		
        $img = wp_get_attachment_image($picture->ID, $size, false, $attr);
        $url = wp_get_attachment_url($picture->ID);
        

        // HTML for lightbox
        $out .= '<li>';
        $out .= "<a data-fresco-group='{$gallery_ref}' href='{$url}' data-fresco-caption='{$fresco_cap}' rel='image-overlay' class='img-padder fresco'>{$img}</a>";
        $out .= '</li>';
    }

    $out .= '</ul></div>';

    return $out;	
}


/* how to add gallery settings 
add_action('print_media_templates', function(){

  // define your backbone template;
  // the "tmpl-" prefix is required,
  // and your input field should have a data-setting attribute
  // matching the shortcode name
  ?>
  <script type="text/html" id="tmpl-my-custom-gallery-setting">
    <label class="setting">
      <span><?php _e('My setting'); ?></span>
      <select data-setting="my_custom_attr">
        <option value="foo"> Foo </option>
        <option value="bar"> Bar </option>
        <option value="default_val"> Default Value </option>
      </select>
    </label>
  </script>

  <script>

    jQuery(document).ready(function(){

      // add your shortcode attribute and its default value to the
      // gallery settings list; $.extend should work as well...
      _.extend(wp.media.gallery.defaults, {
        my_custom_attr: 'default_val'
      });

      // merge default gallery settings template with yours
      wp.media.view.Settings.Gallery = wp.media.view.Settings.Gallery.extend({
        template: function(view){
          return wp.media.template('gallery-settings')(view)
               + wp.media.template('my-custom-gallery-setting')(view);
        }
      });

    });

  </script>
  <?php

});
*/