<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package bb
 */

get_header(); ?>
<?php get_template_part('partials/title', 'section');?>

	<div class="content-area">
		
			<section class="error-404 not-found">
				<header class="page-header">
					<h1 class="page-title">К сожалению, запрошенная страница не найдена, возможно она была перемещена.</h1>
				</header><!-- .page-header -->

				<div class="page-content">
					<p>Пожалуйста, воспользуйтесь поиском или меню для навигации по сайту.</p>

					<?php $src = get_template_directory_uri().'/img/nf-pic.jpg'; ?>
					<div class="nf-pic"><img src="<?php echo $src;?>"></div>

				</div><!-- .page-content -->
			</section><!-- .error-404 -->

		
	</div><!-- #primary -->

<?php get_footer(); ?>
