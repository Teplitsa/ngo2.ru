<?php
/**
 * Single product
 */

global $post;

$price = (function_exists('get_field')) ? get_field('product_price', get_the_ID()) : '';


get_header();
?>
<div class="page-content-grid">
<div class="mdl-grid product-grid">
	
	
	<div class="mdl-cell mdl-cell--3-col mdl-cell--hide-phone mdl-cell--hide-tablet"></div>
	<div class="mdl-cell mdl-cell--6-col mdl-cell--5-col-tablet">
	<?php while(have_posts()){ the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-product-full'); ?>>
		
		<div class="entry-meta">
			<div class="captioned-text">
				<div class="caption"><?php _e('Price', 'tst');?></div>
				<div class="text price-mark"><?php echo number_format ((int)$price , 0 , "." , " " );?> руб.</div>
			</div>
		</div>
		
		<div class="entry-summary"><?php the_excerpt();?></div>
		<div class="sharing-on-top"><?php tst_social_share();?></div>
		
		<?php
			if(has_post_thumbnail()) {
				tst_single_post_thumbnail_html(null, 'embed');
			}
		?>
		
		<div class="entry-content"><?php the_content(); ?></div>
		
		<!-- panel -->
		<?php get_template_part('partials/panel', 'float');?>
		
		</article>
	<?php }	// endwhile ?>		
	</div>
	
	<div class="mdl-cell mdl-cell--3-col mdl-cell--hide-phone mdl-cell--hide-tablet"></div>
	
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
