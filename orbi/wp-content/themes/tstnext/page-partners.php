<?php
/**
 * Template Name: Partners
 */

get_header(); ?>

	
<section class="partners-gallery">
<div class="mdl-grid">

<?php if(function_exists('have_rows')) { if(have_rows('our_partners')) { 

		while(have_rows('our_partners')){
			the_row();
			
			$title = get_sub_field('partner_title');  
			$url = get_sub_field('partner_link');
			$url = (!empty($url)) ? esc_url($url) : $url;
			$logo_id = get_sub_field('partner_logo');
			$logo = wp_get_attachment_image($logo_id, 'full', false, array('alt' => $title));
			
			$text = get_sub_field('partner_desc');
		?>	
		<div class="mdl-cell mdl-cell--4-col mdl-cell--3-col-desktop">
		<div class="mdl-card mdl-shadow--2dp tpl-partner">
			<div class="mdl-card__media">
			<?php if(!empty($url)){ ?>	
				<a class="logo-link" title="<?php echo esc_attr($title);?>" href="<?php echo $url;?>"><?php echo $logo ;?></a>
			<?php } else { ?>
				<span class="logo-link" title="<?php echo esc_attr($title);?>"><?php echo $logo ;?></span>
			<?php } ?>
			</div>
			
			<div class="mdl-card__title">
				<h4 class="mdl-card__title-text"><?php echo apply_filters('tst_the_title', $title);?></h4>
			</div>
			
			<div class="mdl-card__supporting-text mdl-card--expand"><?php echo apply_filters('tst_the_title', $text);?></div>
			
			<div class="mdl-card__actions mdl-card--border">
				<?php if(!empty($url)){ ?>	
				<a class="mdl-button mdl-js-button mdl-button--primary" href="<?php echo $url;?>">Веб-сайт</a>
				<?php } ?>
			</div>
		</div>
		</div>
		<?php } //enwhile ?>
<?php }} ?>	
</div>
</section>	

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
			</article>
		<?php } ?>
		
		
	</div>
	
	<div class="mdl-cell mdl-cell--3-col mdl-cell--hide-phone mdl-cell--hide-tablet"></div>
	
</div><!-- .mdl-grid -->

</div>

<div class="page-footer"><div class="mdl-grid">
	<div class="mdl-cell mdl-cell--3-col mdl-cell--hide-phone mdl-cell--hide-tablet"></div>
	<div class="mdl-cell mdl-cell--6-col mdl-cell--8-col-tablet"><?php get_sidebar(); ?></div>
	<div class="mdl-cell mdl-cell--3-col mdl-cell--hide-phone mdl-cell--hide-tablet"></div>
</div></div>

<?php get_footer(); ?>
