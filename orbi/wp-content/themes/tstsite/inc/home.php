<?php
/**
 * Homepage elements
 **/

/**
 * 3W blocks
 **/

function la_banner_format_home_textblock($query, $format_args) {
	global $post;
	
	//check for plugin
	if(!class_exists('La_Banner_Core'))
		return '';		
	
	ob_start();
	
	$counter = 1;
		
	while($query->have_posts()): $query->the_post();
		$url = esc_url($post->post_excerpt);
		
?>
		<div class="<?php echo esc_attr($format_args);?>">
		<article class="home-block block-<?php echo $counter;?>">			
			
			<h3><?php the_title();?></h3>
			<div class="block-content">
				<?php the_content();?>
				<div class="more-link"><a href="<?php echo $url?>" class="">Подробнее &raquo;</a></div>
			</div>
			
		</article>
		</div>
	<?php $counter++; endwhile; wp_reset_postdata(); ?>
    
	
<?php     
    $out = ob_get_contents();
    ob_end_clean();
    return $out;
}


/**
 * Call out
 **/

add_shortcode('tst_home_callout', 'tst_home_callout_screen');
function tst_home_callout_screen($atts, $content = null){
		
	extract(shortcode_atts(array(
		'button_link' => false,
		'button_text' => __('Participate', 'tst'),		
	), $atts, 'tst_home_callout' ));
	
	if(empty($content))
		return '';
	
	$content = apply_filters('frl_the_content', $content);
	$out = '<div class="callout-wrap"><div class="frame">';
	
	if($button_link){
		$out .= '<div class="bit-10">'.$content.'</div>';
		$out .= '<div class="bit-2"><a class="call btn" href="'.esc_url($button_link).'">'.$button_text.'</a></div>';
	}
	else {
		$out = '<div class="bit-12">'.$content.'</div>';
	}
	
	$out .= "</div></div>";	

	return $out;
}