<?php
/**
 * @package Blank
 */

$column_class = ' bit-8';
$thumb_class = ' bit-4';
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	
	<div class="frame">
		<?php if(has_post_thumbnail()):?>
			<div class="entry-preview<?php echo $thumb_class;?>"><?php the_post_thumbnail();?></div>
		<?php endif;?>
	
	<div class="entry-column<?php echo $column_class;?>">
		
		<header class="entry-header">
			<h1 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
			
			<div class="entry-meta">
				<?php tst_posted_on(); ?>
			</div>
			
		</header><!-- .entry-header -->
	
		
		<div class="entry-summary">
			<?php the_excerpt(); ?>
		</div>		
		
	</div>
	
	</div>	
		
</article><!-- #post-## -->
