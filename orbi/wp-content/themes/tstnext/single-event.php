<?php
/**
 * The template for displaying all single posts.
 *
 * @package bb
 */

global $post;

get_header();

if( !empty($_GET['tst']) ) {
	
	echo '<pre>';
	echo 'Current: '; print_r(get_post_meta($post->ID, 'event_date', true));

	$next = get_posts(array(
		'post_type' => 'event',
		'meta_key' => 'event_date',
		'orderby' => 'meta_value',
		'order' => 'DESC', // 'ASC' to get a chronologically next event, 'DESC' to get a previous one
		'posts_per_page' => 1,
		//'' => '',
	));
	$next = reset($next);

	echo 'Next:'; print_r(get_post_meta($next->ID, 'event_date', true));
	//print_r(get_post_meta($post->ID, 'event_date', true));
	echo '</pre>';
	
	
}
?>
<div class="page-content-grid">
<div class="mdl-grid">
	
<?php while(have_posts()){ the_post(); ?>
	<div class="mdl-cell mdl-cell--3-col mdl-cell--4-col-phone mdl-cell--hide-tablet">
		<?php tst_event_meta();?>		
	</div>
	<div class="mdl-cell mdl-cell--6-col mdl-cell--5-col-tablet">	
		<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-event-full'); ?>>
			
			<div class="entry-meta"><?php tst_event_meta();?></div>
				
			<div class="entry-summary"><?php the_excerpt();?></div>
			<div class="sharing-on-top"><?php tst_social_share();?></div>
			
			<?php
				if(has_post_thumbnail()) {
					tst_single_post_thumbnail_html(null, 'embed');
				}
			?>
			
			<div class="entry-content"><?php the_content(); ?></div>	
			
			<div class="entry-footer">
				<div class="sharing-on-bottom"><?php tst_social_share();?></div>
			</div>
			
			<!-- panel -->
			<?php get_template_part('partials/panel', 'float');?>

		</article>	
	
	</div>	
	<div class="mdl-cell mdl-cell--3-col mdl-cell--4-col-phone"><?php get_sidebar(); ?></div>
	
<?php }	//endwhile ?>

</div><!-- .row -->
</div>

<div class="page-footer"><div class="mdl-grid">
	<div class="mdl-cell mdl-cell--3-col mdl-cell--hide-phone mdl-cell--hide-tablet"></div>
	<div class="mdl-cell mdl-cell--6-col mdl-cell--8-col-tablet">
		<?php get_template_part('partials/related', get_post_type());?>
	</div>
	<div class="mdl-cell mdl-cell--3-col mdl-cell--hide-phone mdl-cell--hide-tablet"></div>
</div></div>

<?php get_footer(); ?>
