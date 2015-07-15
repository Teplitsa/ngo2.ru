<?php
/**
 * Template Name: Homepage
 * 
 */

global $post; 
 
$home_id = $post->ID;
$dnd = array();
$today = strtotime(sprintf('now %s hours', get_option('gmt_offset')));


//featured event
$f_event = (function_exists('get_field')) ? get_field('featured_event', $home_id) : 0; 
if($f_event > 0){
	$dnd[] = $f_event;
}

$f_event = get_post($f_event);


//featured articles
$f_post = new WP_Query(array(
	'post_type' => 'post',
	'posts_per_page' => 1,
	'tax_query' => array(
		array(
			'taxonomy' => 'category',
			'field' => 'slug',
			'terms' => array('news'),
			'operator' => 'NOT IN'
		)
	)
));
$dnd[] = (isset($f_post->posts[0])) ? $f_post->posts[0]->ID : 0;


// posts
$blog = new WP_Query(array(
	'post_type' => 'post',
	'posts_per_page' => 4,
	'post__not_in' => $dnd
));


// events
$events = new WP_Query(
	array(
		'post_type' => 'event',
		'posts_per_page' => 4,
		'meta_query' => array(
			array(
				'key' => 'event_date',
				'value' => date('Y', $today).date('m', $today).date('d', $today),
				'compare' => '>=',
			),
		),
		'post__not_in' => $dnd
	)
);

function tst_featured_event_media($fe, $size = 'embed'){
	
	$img = tst_get_post_thumbnail_src($fe, $size);	
?>
	<div class="hfe-media-content" style="background-image: url('<?php echo $img;?>');"></div>	
<?php
}

get_header();
?>
<section class="home-content-grid">
<div class="mdl-grid">
	
	<div class="mdl-cell mdl-cell--8-col">
	<?php
		if($f_event) {
			
			$date = (function_exists('get_field')) ? get_field('event_date', $f_event->ID) : $f_event->post_date;
			$meta[] = date_i18n('j M. Y', strtotime($date));
			$meta[] = (function_exists('get_field')) ? get_field('event_location', $f_event->ID) : '';
	?>
		<div class="home-featured-event mdl-card mdl-shadow--2dp">
			
			<div class="mdl-grid mdl-grid--no-spacing">
			
				<div class="mdl-cell mdl-cell--8-col mdl-cell--hide-desktop mdl-cell--hide-tablet hfe-media">
					<?php tst_featured_event_media($f_event);?>
				</div>
					
				<div class="mdl-cell mdl-cell--8-col mdl-cell--2-col-tablet mdl-cell--4-col-desktop">
					
					<div class="hfe-content">
						
						<header class="hfe-content-element">
							<div class="mdl-typography--caption ">Скоро</div>
							<h4 class="mdl-typography--title">
								<a href="<?php echo get_permalink($f_event);?>"><?php echo get_the_title($f_event);?></a>
							</h4>
						</header>
												
						<div class="hfe-summary hfe-content-element">
							<h6><?php echo implode(', ', $meta);?></h6>
						<?php
							$e = (!empty($f_event->post_excerpt)) ? $f_event->post_excerpt : wp_trim_words(strip_shortcodes($f_event->post_content), 30);
							echo apply_filters('tst_the_title', $e);
						?>
						</div>
						
						<div class="mdl-card__actions mdl-card--border hfe-action">
							<a href="<?php echo get_permalink($f_event);?>" class="mdl-button mdl-js-button mdl-button--colored">Принять участие</a>
						</div>
					</div>
					
				</div>
				<div class="mdl-cell mdl-cell--hide-phone mdl-cell--6-col-tablet mdl-cell--8-col-desktop hfe-media">
					<?php tst_featured_event_media($f_event);?>
				</div>
				
			</div><!-- .mdl-grid -->
		</div>
		<?php } ?>
		
		<div class="home-blog-section-header">
			<h3>Бежим вместе</h3>
			<a href="" class="all-stories">Все истории</a>
		</div>
		
		<div class="mdl-grid mdl-grid--no-spacing blog-items">
			<div class="mdl-cell mdl-cell--6-col mdl-cell--4-col-tablet"><div class="no-spacing-correct-left featured-story">			
			<?php
				if(isset($f_post->posts[0])){
					echo "<div class='tpl-post featured mdl-card mdl-shadow--2dp invert'>";
					tst_post_card_content($f_post->posts[0]);
					echo "</div>";
				}
			?>
			</div></div><!-- .mdl-cell -->
			
			<div class="mdl-cell mdl-cell--6-col mdl-cell--4-col-tablet"><div class="no-spacing-correct-right blog-list">
			<?php
				if($blog->have_posts()){
					while($blog->have_posts()){
						$blog->the_post();	
						tst_compact_post_item($post, false, 'embed-small');
					}
					wp_reset_postdata();
				}
			?>
			</div></div><!-- .mdl-cell -->
		</div><!-- .mdl-grid -->
		
	</div>
	
	<div class="mdl-cell mdl-cell--4-col mdl-cell--8-col-tablet mdl-cell--6-col-phone">
		<div class="mdl-grid mdl-grid--no-spacing">
			<div class="mdl-cell mdl-cell--12-col mdl-cell--4-col-tablet"><?php get_sidebar(); ?></div>
			<div class="mdl-cell mdl-cell--12-col mdl-cell--4-col-tablet ev-future">
			<?php
				if($events->have_posts()){
					foreach($events->posts as $ev){
						tst_compact_event_item($ev);
					}
				}
			?>
			</div>
		</div>
	</div>
</div>
</section>

<section class="home-partners-block">
	partners
</section>
<?php get_footer(); ?>
