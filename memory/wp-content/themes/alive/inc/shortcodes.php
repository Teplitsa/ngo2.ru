<?php
/**
 * Shortcodes
 **/

 
/**
 * Custom query shortcode
 **/
add_shortcode('alv_query', 'alv_query_screen');
function alv_query_screen($atts){
	global $wp_query;
	
	extract( shortcode_atts( array(
		'q' => '',
		'paging' => 0,
		'format' => 'content',
		'css' => ''
	), $atts ) );
	
	$q = str_replace('+', '&', $q);
		
	//on singlural pages page=2 qv detects paging
	if(isset($wp_query->query_vars['paged']) && $wp_query->query_vars['paged'] > 0)
		$q .= "&paged=".$wp_query->query_vars['paged'];
	elseif(isset($wp_query->query_vars['page']) && $wp_query->query_vars['page'] > 0)
		$q .= "&page=".$wp_query->query_vars['page']."&paged=".$wp_query->query_vars['page'];
	
	
	
	$query = new WP_Query($q);
	if(!$query->have_posts())
		return '';
	
	$out = "";
	
	ob_start();
	echo "<div class='query-loop cf'>";
	while($query->have_posts()): $query->the_post();
		get_template_part($format);
	endwhile; wp_reset_postdata();
	
	if($paging){
		echo "<div class='pagination'>";
		alv_paginate_links($query);
		echo "</div>";
	}
	
	echo "</div>";
	$out = ob_get_contents();
	ob_end_clean();
	
	$css = esc_attr($css);
	return  "<div class='alv-query {$css}'>{$out}</div>";
} 


/**
 * Embed post lists functions
 **/
add_shortcode('embed_posts', 'embed_posts_screen');
function embed_posts_screen($atts){
	global $post, $wp_query;
	
	if(!function_exists('get_field'))
		return '';
	
	$post_type = get_field('post_type', $post->ID);
	if($post_type == 'none')
		$post_type = 'post';
	
	$posts_per_page = get_field('posts_per_page', $post->ID);
	if(empty($posts_per_page))
		$posts_per_page = get_option('posts_per_page');
		
	$period = get_field('period', $post->ID);
	if(empty($period))
		$period = 0;
		
	$taxonomy = get_field('taxonomy', $post->ID);
	if(empty($taxonomy))
		$taxonomy = 'none';
	
	$term_field_name = $taxonomy.'_term_id';
	$term_id = get_field($term_field_name, $post->ID);
	if(empty($term_id))
		$term_id = 0;
	
	$template = get_field('template', $post->ID);
	if(empty($template))
		$template = 'archive';
	
	$paging = (bool)get_field('paging', $post->ID);
	
	$css = esc_attr(get_field('css', $post->ID));
	if(empty($css))
		$css = '';
	
	
	$args = array(
		'post_type' => $post_type,
		'posts_per_page' => $posts_per_page
	);
	
	if($paging){
		//add page arg to query
		if(isset($wp_query->query_vars['paged']) && $wp_query->query_vars['paged'] > 0){
			$args['paged'] = $wp_query->query_vars['paged'];
		}
		elseif(isset($wp_query->query_vars['page']) && $wp_query->query_vars['page'] > 0){
			$args['page'] = $wp_query->query_vars['page'];
			$args['paged'] = $wp_query->query_vars['page'];
		}
	}
	
	if($period > 0){
		$args['tax_query'][] = array(
			'taxonomy' => 'period',
			'field' => 'id',
			'terms' => array($period)
		);
	}
		
	if(isset($_GET['event_speaker_cat']) && !empty($_GET['event_speaker_cat'])){
		//$_GET overwrites page settings - some active filter
		$args['tax_query'][] = array(
			'taxonomy' => 'speaker_cat',
			'field' => 'slug',
			'terms' => trim($_GET['event_speaker_cat'])
		);	
		
	}
	elseif($taxonomy != 'none' && $term_id > 0){
		$args['tax_query'][] = array(
			'taxonomy' => $taxonomy,
			'field' => 'id',
			'terms' => array($term_id)
		);
	}
	
	if(count($args['tax_query']) > 1){
		$args['tax_query']['relation'] = 'AND';
	}
	
	if($args['post_type'] == 'speaker'){
		$args['orderby'] = array('meta_value' => 'ASC');
		$args['meta_key'] = 'last_name';
	}
	elseif($args['post_type'] == 'partner'){
		$args['orderby'] = array('menu_order' => 'DESC', 'title' => 'ASC');
	}
	
	
	$query = new WP_Query($args);
	
	if(!$query->have_posts())
		return '';
	
	$out = "";
	
	ob_start();
	echo "<div class='embed-posts-loop'><div class='post-list {$css}'>";
	while($query->have_posts()): $query->the_post();
		get_template_part('partials/content', $template);
	endwhile; wp_reset_postdata();
	
	echo "</div>";
	
	if($paging){
//		echo "<div class='pagination'>";
//		alv_paginate_links($query);
//		echo "</div>";
		apl_paging_nav($query);
	}
	
	echo "</div>";
	$out = ob_get_contents();
	ob_end_clean();
	
	
	return  $out;
}


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
				<h3><?php echo apply_filters('alv_the_title', $title);?></h3>
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
 * Compact custom togle
 **/
add_shortcode('my_togle', 'alv_my_toggle_screen');
function alv_my_toggle_screen($atts, $content = ''){
	
	if(empty($content))
		return '';
	
	$open = __('More', 'apl');
	$close = __('Close', 'apl');
	
	
	$out = '<div class="tst-toggle content-details">';
	$out .= '<div class="tst-toggle-content">';
	$out .= apply_filters('the_content', $content).'</div>';
	$out .= "<div class='tst-toggle-trigger'><span class='open'>{$open}</span><span class='close'>{$close}</span></div>";
	$out .= "</div>";

	return $out;
}


/**
 * Page sections
 **/
add_shortcode('page_sections', 'page_sections_screen');
function page_sections_screen($atts){	
	global $post, $wp_query;
	
	if(!function_exists('get_field'))
		return '';
		
	$out = '';
	
	if(have_rows('page_sections')): 
	ob_start();
	
	echo "<div class='page-sections'>";
	
	
	while(have_rows('page_sections')): the_row(); 

		// vars		
		$content = get_sub_field('section_content');
		$title = get_sub_field('section_title');
		$css = get_sub_field('section_css');
		$img_id = get_sub_field('section_bg_image'); 
		$img = wp_get_attachment_url($img_id);
		$style = '';
		
		if(!empty($img)){
			$css .= " has-bg-image";
			$style = " style='background-image: url({$img})'";
		}
	?>
		<section class="page-section <?php echo esc_attr($css);?>" <?php echo $style;?>>
			
			
			<?php if(!empty($title)): ?>
				<h2 class="section-title">				
				<?php echo apply_filters('alv_the_title', $title);?>				
				</h3>
			<?php endif; ?>
			
			<div class="page-section-content">
				<?php echo apply_filters('the_content', $content);?>
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
 * Markup shortcodes
 **/
add_shortcode('clear', 'alv_clear_screen');
function alv_clear_screen($atts){
		
	
	$out = '<div class="clear"></div>';		

	return $out;
}


/**
 * Partners gallery
 **/

add_shortcode('partners_gallery', 'apl_partners_gallery_screen');
function apl_partners_gallery_screen($atts){	
	
	extract( shortcode_atts( array(
		'type' => '',
		'num'  => -1,
		'css'  => ''
	), $atts ));
		
	$size = 'full'; //logo size
	
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
        
            $url = (!empty($item->post_excerpt)) ? esc_url($item->post_excerpt) : '';
            $txt = esc_attr($item->post_title);					
			$cat = alv_get_partner_type($item);
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