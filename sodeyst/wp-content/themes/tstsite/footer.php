<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package Blank
 */

$cc_link = '<a href="http://creativecommons.org/licenses/by-sa/3.0/">Creative Commons СС-BY-SA 3.0</a>';
$tst = __("Teplitsa of social technologies - crowdsourcing, technologies for the charity", 'tst');
?>

	</div><!-- .inner #content -->
	
	<div id="bottombar" class="widget-area"><div class="inner">
		
		<div class="frame">
			<div class="bit-4"><?php dynamic_sidebar( 'footer_one-sidebar' );?></div>
			<div class="bit-4"><?php dynamic_sidebar( 'footer_two-sidebar' );?></div>
			<div class="bit-4"><?php dynamic_sidebar( 'footer_three-sidebar' );?></div>
		</div>
	
	</div></div>
	
	<footer id="colophon" class="site-footer" role="contentinfo"><div class="inner">
				
		<div class="copy"><a href="<?php home_url();?>"><?php bloginfo('name');?></a>. <?php printf(__('All materials of the site are avaliabe under license %s', 'tst'), $cc_link);?></div>
			
		<div class="te-st">
			<a title="<?php echo $tst;?>" href="http://te-st.ru/"><img src="http://te-st.ru/wp-content/uploads/white-logo-100x50.png" alt="<?php echo $tst;?>" width="100" height="50" /></a>
		</div>
		
	</div></footer>
	
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>