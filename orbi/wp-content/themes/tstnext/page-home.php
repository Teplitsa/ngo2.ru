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

get_header();
?>
<section class="home-content-grid">
<div class="mdl-grid">
	
	<div class="mdl-cell mdl-cell--8-col">
	<?php
		if($f_event) {
			//$img = tst_get_post_thumbnail_src($f_event, 'thumbnail-long');
			$date = (function_exists('get_field')) ? get_field('event_date', $f_event->ID) : $f_event->post_date;
			$meta[] = date_i18n('j M. Y', strtotime($date));
			$meta[] = (function_exists('get_field')) ? get_field('event_location', $f_event->ID) : '';
	?>
		<div class="home-featured-event mdl-card mdl-shadow--2dp">
			<div class="mdl-card--expand mdl-card__media"><?php echo tst_get_post_thumbnail($f_event, 'thumbnail-long');?></div>
				
				<div class="mdl-card__title">					
					<h4 class="mdl-card__title-text">
						<div class="mdl-typography--caption">Скоро</div>
						<span><?php echo get_the_title($f_event);?></span></h4>
				</div>
				
				<div class="mdl-card__supporting-text"><?php echo implode(', ', $meta);?></div>
				
				<div class="mdl-card__actions mdl-card--border">
					<a href="<?php echo get_permalink($f_event);?>" class="mdl-button mdl-js-button mdl-button--colored">Принять участие</a>
				</div>
			
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
