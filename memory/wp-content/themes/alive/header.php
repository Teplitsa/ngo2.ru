<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package alv
 */
?><!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />

<?php wp_head(); ?>
</head>

<body id="top" <?php body_class(); ?>>
<div id="page" class="hfeed site">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'alv' ); ?></a>
	
	<nav id="head-nav" class="site-nav">
		<span class="screen-reader-text"><?php _e( 'Main menu', 'svet' ); ?></span>
		<?php wp_nav_menu(array('theme_location' => 'primary', 'container' => false, 'menu_class' => 'main-menu')); ?>
	</nav>
		
	
	<header id="masthead" class="site-header" role="banner">
		
		<div class="frame">
			<div class="bit lg-6">
				<?php alv_site_brnading();?>			
			</div><!-- bit -->
			
			<div class="bit lg-3 lg-offset-3">
				<span class="screen-reader-text"><?php _e( 'Social menu', 'svet' ); ?></span>
				<?php dynamic_sidebar('header-sidebar'); ?>
			</div>
		</div>	
	</header><!-- #masthead -->

	<div id="content" class="site-content">
