<?php
/**
 * @package bb
 */

$show_thumb = (function_exists('get_field')) ? (bool)get_field('show_thumbnail') : true;

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-post-full'); ?>>

	<header class="entry-header">
		<h2 class="entry-title"><?php the_title(); ?></h2>
			
	</header>
		
	<div class="entry-summary"><?php the_excerpt();?></div>
	<div class="sharing-on-top"><?php tst_social_share();?></div>
	
	<?php if($show_thumb) { ?>
		<div class="entry-media">
		<?php echo tst_get_post_thumbnail(null, 'embed'); ?>
		</div>
	<?php } ?>
	
	<div class="entry-content"><?php the_content(); ?></div>
		
	<?php if('post' == get_post_type()) { ?>
		<div class="entry-footer">
			<div class="row">
				<div class="col s12 m8"><?php echo tst_posted_on(); ?></div>
				<div class="col s12 m4"><?php tst_post_nav(); ?></div>
			</div>
			
			<div class="sharing-on-bottom"><?php tst_social_share();?></div>
		</div>
	<?php } ?>
	
</article><!-- #post-## -->

