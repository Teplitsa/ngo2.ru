<?php
/**
 * Template Name: Map
 */

get_header(); ?>

<?php get_template_part('partials/title', 'section');?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">		
		
		<section class="map-holder">
			<div id="map" style="height: 520px;"></div>
			
			<script type="text/javascript">
				<?php $markers = array();
				foreach(get_posts(array('post_type' => 'marker')) as $marker) {
	
					$coords = get_field('coords', $marker->ID);
					$history = get_field('history_photo', $marker->ID);
					$modern = get_field('modern_photo', $marker->ID);
	
					$markers[] = array(
						'lat' => $coords['lat'],
						'lng' => $coords['lng'],
						'addr' => $marker->post_title,
						'text' => $marker->post_content,
						'history_photo' => $history['sizes']['portrait'],
						'modern_photo' => $modern['sizes']['portrait'],
					);
				}?>
				var markers = <?php echo json_encode($markers);?>;
			</script>
		</section>
		
		<section class="map-description">
			<div class="frame">
				<div class="bit md-8">
				<?php
					while ( have_posts() ) {
						the_post();
						
						echo "<div class='entry-content'>";
						the_content();
						echo "</div>";
					}
				?>
				</div>
				<?php get_sidebar();?>
			</div>
		</section>
	</main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>