<?php
/**
 * Additional footer
 **/

$css = (is_front_page()) ? 'container-wide' : 'container';
?>
 
<div class="bottombar">	
	<div class="<?php echo $css;?>">
		<div class="frame">			
			<div class="bit md-6 first"><?php dynamic_sidebar('footer_one'); ?></div>
			<div class="bit md-6"><?php dynamic_sidebar('footer_two'); ?></div>
			
		</div>
	</div>
</div>