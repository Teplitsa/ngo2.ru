<?php
/**
 * Template Name: Map
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

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

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>