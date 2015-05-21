<?php
/**
 * Single Event
 */

 
$post_release = (function_exists('get_field')) ? get_field('post-release') : true; 
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-event-full'); ?>>

	<header class="entry-header">
		<h1 class="entry-title"><?php the_title(); ?></h1>
		
		<?php if(is_singular()) { ?>
			<h2 class="screen-reader-text wac-go-to-hell">Полный текст публикации</h2>
		<?php } ?>	
		
	</header>
	
	<div class="frame">
		<div class="bit md-4">
			<a href="<?php the_permalink();?>" class="thumbnail-link">
			<?php
				$attr = array(
					'alt' => sprintf(__('Thumbnail for - %s', 'tst'), get_the_title()),
				);
				the_post_thumbnail('thumbnail', $attr);
			?>
			</a>	
		</div>
		
		<div class="bit md-8">
			<div class="entry-meta event-meta"><?php tst_event_meta(); ?></div>
			<div class="entry-summary"><?php the_excerpt();?></div>
		</div>
	</div>
	
	<?php if(!empty($post_release)) { ?>
	<div class="entry-content post-release">
		<h3 class="event-section-title">Как это было</h3>
		<?php echo apply_filters('tst_the_content', $post_release);?>
	</div>
	<?php } ?>
	
	<div class="entry-content"><?php the_content(); ?></div><!-- .entry-content -->
	
	
	<!-- gallery -->
	<?php if(function_exists('have_rows')) { if(have_rows('event-partners')) { ?>
	<div class="entry-partners">
		<h3 class="event-section-title">Партнеры мероприятия</h3>
		
		<ul class="partners-gallery frame">
		<?php
			while(have_rows('event-partners')){
				the_row();
				
				$title = get_sub_field('event-partner-name');  
				$url = get_sub_field('event-partner-website');
				$url = (!empty($url)) ? esc_url($url) : $url;
				$logo_id = get_sub_field('event-partner-logo');
				$logo = wp_get_attachment_image($logo_id, 'full', false, array('alt' => $title));
			?>	
			<div class="logo bit mf-6 md-3"><div class="logo-frame">			
				<a class="logo-link" title="<?php echo esc_attr($title);?>" href="<?php echo $url;?>"><?php echo $logo ;?></a>
			</div></div>
		<?php } ?>
		</ul>		
	</div>
	<?php  }} //endif ?>
	
</article><!-- #post-## -->

<?php
	//related
	$num = 4;	
	$r_query = new WP_Query(array(
		'post_type' => 'event',
		'posts_per_page' => $num,
		'orderby' => 'rand'
	));
	if($r_query && $r_query->have_posts()){
?>
	<aside class="related-posts entry-relations">
		<h3 class="aside-title">Еще мероприятия</h3>
	<?php
		echo "<div class='frame'>";
			
		while($r_query->have_posts()){
			$r_query->the_post();
			
		?>
			<div class="tpl-related-item bit md-6">
				<div class="frame">
					<div class="bit mf-4 ld-5">
						<a href="<?php the_permalink();?>" class="thumbnail-link">
						<?php
							$attr = array('alt' => sprintf(__('Thumbnail for - %s', 'tst'), get_the_title()));
							the_post_thumbnail('thumbnail', $attr);
						?>
						</a>
					</div>
					<div class="bit mf-8 ld-7">
						<h4 class="entry-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>
						<div class="entry-meta"><?php tst_event_meta(); ?></div>
					</div>
				</div>
			</div>
		<?php	
		}
		wp_reset_postdata();
		
		echo "</div>";
	?>
	</aside>
<?php } ?>
