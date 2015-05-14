<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package bb
 */

get_header(); ?>

<?php get_template_part('partials/title', 'section');?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<section class="error-404 not-found">
				<header class="page-header">
					<h1 class="page-title">К сожалению, мы не нашли запрошенную страницу</h1>
				</header><!-- .page-header -->

				<div class="page-content">
					<p>Возможно, она переехала по новому адресу. Воспользуйтесь меню, для навигации по сайту.</p>				

				</div>
			</section>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>
