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

<nav id="site_nav" class="site-nav navbar" role="navigation">
	<span class="screen-reader-text"><?php _e( 'Primary menu', 'tst' ); ?></span>
	
	<div class="navbar-panel">
		<div class="navbar-icon"><i class="material-icons">menu</i></div>
		<div class="navbar-title"><?php tst_breadcrumbs();?></div>
		<div class="navbar-logo"><?php tst_site_logo('small');?></div>
		<div class="navbar-actions"><i class="material-icons">search</i></div>
	</div>
</nav>


<header id="site_header" class="site-header" role="banner">
	<div class="container">
		<?php get_template_part('partials/title', 'section');?>
	</div>
</header>



<div id="site_content" class="site-content section">
<div class="container">
