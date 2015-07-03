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
<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'tst' ); ?></a>

<nav id="site_nav" class="site-nav deep-purple darken-1" role="navigation">
	<span class="screen-reader-text"><?php _e( 'Primary menu', 'tst' ); ?></span>
	<div class="nav-wrapper container">
		<a href="#" data-activates="mobile_nav" class="button-collapse"><i class="mdi-navigation-menu"></i></a>
		<?php wp_nav_menu(array('theme_location' => 'primary', 'container' => false, 'menu_class' => 'right hide-on-med-and-down')); ?>
		<?php wp_nav_menu(array('theme_location' => 'primary', 'container' => false, 'menu_class' => 'side-nav', 'menu_id' => 'mobile_nav')); ?>
	</div>
</nav>

<?php if(!is_front_page()) { ?>
<header id="site_header" class="site-header section" role="banner">
	<div class="container">
		<div class="row">
			<div class="col s12 m9">
				<div class="site-branding">
					
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="logo-wrap">
					
						<h4><?php bloginfo('name'); ?></h4>
						<h5><?php bloginfo('description');?></h5>
					
						</a>
					
				</div><!-- .branding -->
			</div>
			<div class="col s12 m3">
				<?php dynamic_sidebar('header-sidebar'); ?>
			</div>
		</div>
	</div>
</header>



<div id="site_content" class="site-content section">
<div class="container">
<?php } ?>