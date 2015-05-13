<?php
/**
 * The Navbar 
 */

global $tst_nav_w;  
 
$sidebar = 'helper';

?>
<div id="navbar" class="widget-area bit-<?php echo $tst_nav_w;?>" role="complementary">

	<?php dynamic_sidebar($sidebar.'-sidebar'); ?>

	
</div>