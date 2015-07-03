<?php
/**
 * Shortcodes
 **/



/**
 * Repeateble blocks
 **/
add_shortcode('repeatable_blocks', 'repeatable_blocks_screen');
function repeatable_blocks_screen($atts){
	global $post, $wp_query;
	
	if(!function_exists('get_field'))
		return '';
	
	$css = get_field('repeatable_blocks_css');
	$thumb_size = (get_field('repeatable_blocks_thumb_size')) ? get_field('repeatable_blocks_thumb_size') : 'thumbnail';
	$out = '';
	
	if(have_rows('repeatable_blocks')): 
	ob_start();
	
	echo "<div class='repeatable-blocks {$css}'>";
	while(have_rows('repeatable_blocks')): the_row(); 

		// vars
		$img_obj = get_sub_field('block_image');
		$content = get_sub_field('block_html');
		$title = get_sub_field('block_title');
		$img = '';
		$content_css = 'bit';
		
		if(isset($img_obj['id']) && $img_obj['id'] > 0){
			$img = wp_get_attachment_image($img_obj['id'], $thumb_size);
			$content_css = 'bit md-8 lg-9';
		}
	?>
		<section class="rb-item">

			<?php if(!empty($title)): ?>
				<h3><?php echo apply_filters('tst_the_title', $title);?></h3>
			<?php endif; ?>
			
			<div class="rb-section frame">
			
				<?php if(!empty($img)): ?>
				<div class="bit md-4 lg-3">
					<div class="rb-image"><?php echo $img;?></div>
				</div>
				<?php endif;?>
				
				<div class="rb-content <?php echo esc_attr($content_css);?>">
					<?php echo apply_filters('the_content', $content); ?>
				</div>
			
			</div>
		</section>
<?php
	endwhile;
	echo "</div>";
	
	$out = ob_get_contents();
	ob_end_clean();
	endif;
	
	return $out;
}


/**
 * Clear
 **/
add_shortcode('clear', 'tst_clear_screen');
function tst_clear_screen($atts){
		
	
	$out = '<div class="clear"></div>';		

	return $out;
}


/**
 * Partners gallery
 **/

add_shortcode('partners_gallery', 'tst_partners_gallery_screen');
function tst_partners_gallery_screen($atts){
	
	extract(shortcode_atts(array(
		'type' => '',
		'num'  => -1,
		'css'  => ''
	), $atts));
		
	$size = 'full'; // logo size
	
	$args = array(
		'post_type' => 'partner',
		'posts_per_page' => $num,
		'orderby' => array('menu_order' => 'DESC')
	);
	
	if(!empty($type)){
		
		$type = array_map('trim', explode('_', $type));
		
		
		$args['tax_query'][] = array(
			'taxonomy' => ($type[0] == 'category') ? 'partner_cat' : 'period',
			'field' => 'id',
			'terms' => intval($type[1])
		);
	}
	
	$query = new WP_Query($args);
		
	ob_start();
?>
	<ul class="cf partners logo-gallery frame <?php echo $css;?>">
    <?php
		foreach($query->posts as $item):
        
            $url = $item->post_excerpt ? esc_url($item->post_excerpt) : '';
            $txt = esc_attr($item->post_title);					
			$cat = tst_get_partner_type($item);
        ?>
		<li class="bit mf-4 md-3 lg-2">
			<div class="logo">
				<div class='logo-frame'>			
				<?php if(!empty($url)): ?>
					<a href="<?php echo $url;?>" target="_blank" title="<?php echo $txt;?>" class="logo-link">
				<?php else: ?>
					<span class="logo-link" title="<?php echo $txt;?>">
				<?php endif;?>
				
				<?php echo get_the_post_thumbnail($item->ID, $size);?>
			
				<?php if(!empty($url)): ?>
					</a>
				<?php else: ?>
					</span>
				<?php endif;?>
				</div>
				<?php if(!empty($cat)):?>
					<span class="partner-cat"><?php echo $cat;?></span>
				<?php endif;?>
			</div>
			
		</li>
        <?php endforeach; ?>        
    </ul>
<?php	
	$out = ob_get_contents();
	ob_end_clean();
	
	return $out;
}