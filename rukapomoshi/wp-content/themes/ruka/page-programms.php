<?php
/**
 * Template Name: Programms
 **/

global $post;

$ppage = $post;

$query = new WP_Query(array(
	'post_type' => 'programm',
	'posts_per_page' => -1,	
));

get_header(); ?>

<?php get_template_part('partials/title', 'section');?>

<div class="frame">
	
<div id="primary" class="content-area bit md-8">
	<main id="main" class="site-main" role="main">

	<?php while ( have_posts() ) : the_post(); ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-programm-page'); ?>>	
			
			<div class="entry-content">
				<?php the_content(); ?>		
			</div>
		
			<?php if($query->have_posts()) { ?>
			<section class="programms-loop frame">
			<?php
				while($query->have_posts()){
					$query->the_post();
					
					$tagline = (function_exists('get_field')) ? get_field('prog_tagline') : '';
				?>
				<div class="tpl-programm bit sm-6">
					<a href="<?php the_permalink();?>" class="thumbnail-link">
					<?php
						$attr = array(
							'alt' => sprintf(__('Thumbnail for - %s', 'ruka'), get_the_title()),
						);
						the_post_thumbnail('post-thumbnail', $attr);
					?>
					</a>
					<header class="entry-header">
						<h3 class="entry-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>
					<?php if(!empty($tagline)) { ?>
						<div class="entry-meta"><?php echo apply_filters('ruka_the_title', $tagline);?></div>
					<?php  } ?>
					</header>
					
					<div class="entry-summary">
						<?php the_excerpt();?>
						<div class="more"><a href="<?php the_permalink();?>">Подробнее &raquo;</a></div>
					</div>
				</div>
				<?php
				}
				wp_reset_postdata();
			?>
			</section>
			<?php } ?>
			
		</article><!-- #post-## -->
		<?php if(function_exists('have_rows')) { if(have_rows('press_links', $ppage->ID)) { ?>
		<aside class="press-block">
			<h3>О нас пишут</h3>
			<ul class="press-list">
			<?php
				while(have_rows('press_links', $ppage->ID)){
					the_row();
					
					$title = get_sub_field('press_title');  
					$url = get_sub_field('press_url');
					$url = (!empty($url)) ? esc_url($url) : $url;
					$source = get_sub_field('press_source');
					if(!empty($source))
						$source = " / ".$source;
				?>	
				<li>			
					<a href="<?php echo $url;?>"><?php echo $title ;?></a><?php echo $source;?>
				</li>
			<?php } ?>
			</ul>	
		</aside>		
		<?php }} ?>
		
	<?php endwhile; // end of the loop. ?>
	</main><!-- #main -->
</div><!-- #primary -->

<?php get_sidebar(); ?>

</div>
<?php get_footer(); ?>
