<?php require_once("inc/functions.php"); ?>
<?php include("inc/header.php");?>
		
<header class="page-header"><div class="inner">

<div class="frame">
	<h1 class="page-title bit-6">Все новости</h1>
	<div class="bit-6">
		<div class="breadcrumbs">
			<span xmlns:v="http://rdf.data-vocabulary.org/#">
				<span typeof="v:Breadcrumb"><a property="v:title" rel="v:url" href="http://step.ngo2.ru">Главная</a></span> » <span typeof="v:Breadcrumb"><span property="v:title" class="breadcrumb_last">Новости</span></span>
			</span>
		</div>
	</div>
</div>

</div></header>

<div class="page-body"><div class="inner">
	
	<div class="frame">
				
	<div id="primary" class="content-area bit-8">
	<div id="main" class="site-main in-loop" role="main">

	<?php for($i=0; $i <5; $i++): ?>
		<article id="post-286" class="post-286 post type-post status-publish format-standard hentry category-smi-o-nas">
		
			<div class="frame">
				<div class="entry-preview bit-4">
					<img src="img/post-thumbnail.jpg">
				</div>
				
				<div class="entry-column bit-8">
				
					<header class="entry-header">
						<h1 class="entry-title"><a href="single.php" rel="bookmark">Заголовок новостной публикации</a></h1>
					
						<div class="entry-meta">					
							<time class="entry-date">30.12.2013</time>
							<span class='sep'>//</span>
							<span class="category"><a href="" rel="tag">Рубрика</a></span>
						</div>
					</header><!-- .entry-header -->	
				
					<div class="entry-summary">
						<p>Дедуктивный метод решительно раскладывает на элементы из ряда вон выходящий знак, не учитывая мнения авторитетов. Знак оспособляет сенсибельный смысл жизни, отрицая очевидное. Культ джайнизма включает в себя поклонение Махавире и другим тиртханкарам, поэтому гравитационный парадокс&hellip;&nbsp; <a href=""><span class="meta-nav">[&raquo;]</span></a></p>
					</div>		
				
				</div>	
			</div>	
				
		</article><!-- #post-## -->
	<?php endfor; ?>
	
	<nav role="navigation" id="nav-below" class="navigation-paging">		
		<div class="pagination">
			<span class='page-numbers current'>1</span>
			<a class='page-numbers' href='http://promama.te-st.ru/novosti/page/2/'>2</a>
			<a class="next page-numbers" href="http://promama.te-st.ru/novosti/page/2/">след. &raquo;</a>
		</div>
	</nav>
		
				
	</div><!-- #main -->
	</div><!-- #primary -->
		
	<?php include("inc/sidebar.php");?>

	</div><!-- .frame -->

</div></div><!-- .inner .page-body -->
<?php include("inc/footer.php");?>