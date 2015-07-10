<?php
/**
 * Template Name: Map
 */

get_header(); ?>

<?php get_template_part('partials/title', 'section');?>

<div class="content-area">
		
		
		<section class="map-holder">
			<div id="map" style="height: 520px;"></div>		
			
			<?php
				$markers = array();
				foreach(get_posts(array('post_type' => 'marker', 'nopaging' => 1,)) as $marker) {
	
					$coords = get_field('coords', $marker->ID);
					$history = (function_exists('get_field')) ? get_field('history_photo', $marker->ID) : ''; 
					$history_url = wp_get_attachment_image_src($history, 'marker'); 
					
					$modern = (function_exists('get_field')) ? get_field('modern_photo', $marker->ID) : '' ;
					$modern_url = wp_get_attachment_image_src($modern, 'marker');
	
					$markers[] = array(
						'lat' => $coords['lat'],
						'lng' => $coords['lng'],
						'addr' => $marker->post_title,
						'text' => get_permalink($marker),
						'history_photo' => ($history_url[0]) ? $history_url[0] : '',
						'modern_photo' => ($modern_url[0]) ? $modern_url[0] : '',
					);
					
				}
			?>			
			<script type="text/javascript">	
				var markers = <?php echo json_encode($markers); ?>;
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
	
</div><!-- #primary -->

<?php get_footer(); ?>