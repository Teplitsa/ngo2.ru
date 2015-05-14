<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package bb
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
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'memo' ); ?></a>

<header id="site_header" class="site-header" role="banner"><div class="container">
	
	<nav id="top_nav" class="top-nav">
		<div class="nomobile">
		<span class="screen-reader-text"><?php _e( 'Secondary menu', 'memo' ); ?></span>
		<?php wp_nav_menu(array('theme_location' => 'secondary', 'container' => false, 'menu_class' => 'top-menu')); ?>
		<div id="search-toggle">
			<div class="search-trigger"><i class="fa fa-search"></i></div>
			<div class="search-holder"><?php get_search_form();?></div>
		</div>
		</div>
	</nav>
	
	<div class="site-branding">
		<div class="site-description"><?php bloginfo('description');?></div>
		<div class="site-name">
			<?php if(!is_front_page()) { ?>	
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="name-wrap">
			<?php } else { ?>
				<span class="name-wrap">
			<?php }?>
				<?php bloginfo('name');?>
			<?php if(!is_front_page()) { ?>	
				</a>
			<?php } else { ?>
				</span>
			<?php }?>
		</div>
	</div>
	
	
	<nav id="site_nav" class="site-nav">
		<span class="screen-reader-text"><?php _e( 'Primary menu', 'memo' ); ?></span>	
		
		<button id="menu-trigger" class="menu-toggle"><span class="fa fa-bars" aria-hidden="true"></span> <?php echo esc_html(__('Menu', 'memo')); ?></button>
		
		<div class="site-navigation-area">
			
			<?php wp_nav_menu(array('theme_location' => 'primary', 'container' => false, 'menu_class' => 'main-menu')); ?>
			
			<?php wp_nav_menu(array('theme_location' => 'secondary', 'container' => false, 'menu_class' => 'aux-menu')); ?>
				
			<div class="search-holder"><?php get_search_form();?></div>
				
			
		</div><!-- .site-navigation-area -->
		
	</nav>
</div></header>
	

<div id="content" class="site-content"><div class="container">
