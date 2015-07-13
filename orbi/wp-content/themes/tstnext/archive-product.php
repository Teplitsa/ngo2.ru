<?php
/**
 * Products
 **/

global $wp_query;

get_header();
?>


<div class="mdl-grid catalogue-grid">
	
<?php
	if(have_posts()){
		$counter = 0;
		while(have_posts()){
			the_post();
			$counter++;
			
			if($counter == 1){// print first row
		?>
			<div class="mdl-cell mdl-cell--8-col">
				<?php get_template_part('partials/content', get_post_type()); ?>
			</div>
			<div class="mdl-cell mdl-cell--8-col mdl-cell--4-col-desktop"><?php get_sidebar(); ?></div>
		<?php }	else { ?>
			<div class="mdl-cell mdl-cell--12-col">
				<?php get_template_part('partials/content', get_post_type()); ?>
			</div>			
		<?php } //counter
		}
	}
	?>	
</div>

<div class="mdl-grid">
	<div class="mdl-cell mdl-cell--12-col"><?php tst_paging_nav(); ?></div>
</div>

<?php get_footer(); ?>