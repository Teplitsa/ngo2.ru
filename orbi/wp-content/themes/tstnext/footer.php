<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package bb
 */

$cc_link = '<a href="http://creativecommons.org/licenses/by-sa/3.0/" target="_blank">Creative Commons СС-BY-SA 3.0</a>';
$tst = __("Teplitsa of social technologies", 'tst');

$banner = get_template_directory_uri().'/img/te-st-logo-10x50';

?>

</div><!-- .page-content -->
<?php if(tst_has_bottombar()) { ?>
<div id="bottombar" class="widget-area"><div class="container">		
	
	
	<div class="mdl-grid">		
		<div class="mdl-cell mdl-cell--4-col"><?php dynamic_sidebar( 'footer_1-sidebar' );?></div>
		<div class="mdl-cell mdl-cell--4-col"><?php dynamic_sidebar( 'footer_2-sidebar' );?></div>
		<div class="mdl-cell mdl-cell--4-col"><?php dynamic_sidebar( 'footer_3-sidebar' );?></div>					
	</div>
	
</div></div>
<?php } ?>

<?php 
	if(is_singular('post')) { //related block in post
		get_template_part('partials/related', get_post_type());	
	}
	
?>

<footer id="colophon" class="site-footer" role="contentinfo">	

	
	<div class="mdl-grid full-width">
		<div class="mdl-cell mdl-cell--6-col mdl-cell--8-col-tablet">
			<div class="credits">
			<div class="bottom-logo"><?php tst_site_logo('context');?></div>
			<div class="copy">
				<a href="<?php home_url();?>"><?php bloginfo('name');?></a> - программа фонда "ОРБИ".</a><br>
				<?php printf(__('All materials of the site are avaliabe under license %s.', 'tst'), $cc_link);?>
			</div>
			</div>
		</div><!-- .col -->
		
		<div class="mdl-cell mdl-cell--6-col mdl-cell--8-col-tablet">
			<div class="te-st-bn">
				<span class="support">Сайт сделан <br>при поддержке</span>
				<a title="<?php echo $tst;?>" href="http://te-st.ru/">
					<img alt="<?php echo $tst;?>" src="<?php echo $banner;?>.svg" onerror="this.onerror=null;this.src=<?php echo $banner;?>.png;">
				</a>
			</div>
		</div><!-- .col -->
	</div>

</footer>

</main><!-- mdl-layout__content -->
</div><!-- .mdl-layout -->

<?php wp_footer(); ?>

</body>
</html>
