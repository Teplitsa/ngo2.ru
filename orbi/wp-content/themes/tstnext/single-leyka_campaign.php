<?php
/**
 * Single template for donation page
 **/

global $post;

$campaign_id = $post->ID;

get_header(); ?>
<div class="page-content-grid">
<div class="mdl-grid">
	
	<div class="mdl-cell mdl-cell--3-col mdl-cell--hide-phone mdl-cell--hide-tablet"></div>
	<div class="mdl-cell mdl-cell--6-col mdl-cell--8-col-tablet">
		<?php		
			while(have_posts()){
				the_post();
		?>
			<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-page'); ?>>
				<div class="entry-content">
					<?php the_content(); ?>		
				</div>
				<div class="payment-form"><?php echo do_shortcode('[leyka_payment_form id="'+$campaign_id+'" template="toggles"]');?></div>
			</article>
		<?php } ?>
		
		
	</div>
	
	<div class="mdl-cell mdl-cell--3-col mdl-cell--hide-phone mdl-cell--hide-tablet"></div>
	
</div><!-- .mdl-grid -->
</div>

<div class="page-footer"><div class="mdl-grid">
	<div class="mdl-cell mdl-cell--3-col mdl-cell--hide-phone mdl-cell--hide-tablet"></div>
	<div class="mdl-cell mdl-cell--6-col mdl-cell--8-col-tablet">
		<aside class="donation-history section">
			<h5>Мы благодарим</h5>
			<?php echo do_shortcode('[leyka_donors_list id="'+$campaign_id+'" num="20"]');?>
		</aside>
		
	</div>
	<div class="mdl-cell mdl-cell--3-col mdl-cell--hide-phone mdl-cell--hide-tablet"></div>
</div></div>

<?php get_footer(); ?>