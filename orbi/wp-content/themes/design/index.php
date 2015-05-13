<?php
/** home blocks */

function slider(){
?>
	<div class="fake-slide">
		
		<img src="img/slide.jpg">
		<div class="cycloneslider-caption">
			<div class="cycloneslider-caption-title">Небольшой важный текст</div>
			<div class="cycloneslider-caption-description">Личность интегрирует интеракционизм, что вызвало развитие функционализма</div>
		</div>
	</div>
<?php	
}

function text_block($args = array()) {
	
	// $args['css'] $args['title'] 
?>
	<div class="<?php echo (isset($args['css'])) ? $args['css']: 'wrap';?>">
		<article class="type-banner text-block">
			<h3><?php echo $args['title']; ?></h3>
			<div class="block-content">
				<p>Исходя из структуры пирамиды Маслоу, начальная стадия проведения исследования по-прежнему востребована. Повышение жизненных стандартов транслирует комплексный креатив, отвоевывая рыночный сегмент.</p>
				<div class="more-link"><a href="">Подробнее &raquo;</a></div>
			</div>
		</article>
	</div>
<?php
}

function callout($args = array()) {
	
// $args['layout'] $args['text'] $args['btn_text']	
?>
<div class="callout-wrap">
	<div class="frame">
<?php if($args['layout'] == 'button'):?>
	<div class="bit-10"><?php echo $args['text'];?></div>
	<div class="bit-2"><a class="call btn"><?php echo $args['btn_text'];?></a></div>
<?php else: ?>
	<div class="bit-12"><?php echo $args['text'];?></div>
<?php endif;?>
	</div>
</div>
<?php
}

function recent_items($args){

// $args['widget_title'] $args['has_thumb'] $args['has_date'] $args['has_cat'] $args['has_desc'] $args['num']
?>
<div class="widget widget_la_recent">
	<h3 class="widget-title"><?php echo $args['widget_title'];?></h3>
	<div class="la-rpw-block">

	<ul class="la-rpw-ul">
		<?php for($i=0; $i < $args['num']; $i++):?>		
			<li class="la-rpw-item la-rpw-clearfix <?php if($args['has_thumb']) echo ' has-thumb';?>">
			
			<?php if($args['has_thumb']):?>
				<div class="la-rpw-preview"><a href=""><img src="img/post-thumbnail.jpg"></a></div>
			<?php endif;?>
			
				<div class="la-rpw-title"><a href="" rel="bookmark">Заголовок новости или анонса или какой еще публикации</a></div>
				
			<?php if($args['has_date'] || $args['has_cat']): ?>	
				<div class="la-rpw-metadata">
					
				<?php if($args['has_date']): ?>		
					<time class="entry-date">30.12.2013</time>
				<?php endif;?>
				<?php if($args['has_cat']): ?>	
					<span class='sep'>//</span>
					<span class="category"><a href="" rel="tag">Рубрика</a></span>
				<?php endif;?>
				</div>
			<?php endif;?>
			
			<?php if($args['has_desc']):?>
				<div class="la-rpw-exerpt">Исходя из структуры пирамиды Маслоу, начальная стадия проведения исследования по-прежнему…</div>
			<?php endif;?>
			</li>
		<?php endfor; ?>
	</ul>
	</div><!-- .la-rpw-block -->

</div>
<?php
}

function pictured_block($args = array()) {
	
	// $args['has_meta'] $args['has_desc'] $args['css']
?>
	<div class="<?php echo (isset($args['css'])) ? $args['css']: 'wrap';?>">
		<article class="type-banner pictured-block">
			<div class="item-preview"><a href=""><img src="img/post-thumbnail.jpg"></a></div>
			<h4 class="item-title"><a href="">Программа поддержи и помощи тем, кто кому нужна эта помощь сегодня и всегда</a></h4>
		
		<?php if($args['has_meta']):?>
			<div class="item-meta">Метаданные по программе</div>
		<?php endif;?>
		
		<?php if($args['has_desc']):?>
			<div class="item-content">
				<p>Исходя из структуры пирамиды Маслоу, начальная стадия проведения исследования по-прежнему востребована. </p>
				<div class="more-link"><a href="">Подробнее</a></div>
			</div>
		<?php endif;?>
		</article>
	</div>
<?php
}


function  news_grid_item($args = array()) {
	
	// $args['has_date'] $args['has_cat']
?>
	<article class="grid-item bit-3">
		<div class="item-preview"><a href=""><img src="img/post-thumbnail.jpg"></a></div>
		<h4 class="item-title"><a href="">Заголовок новости желательно чтобы он не был слишком большой</a></h4>
	
	<?php if($args['has_date'] || $args['has_cat']): ?>	
		<div class="item-metadata">
			
		<?php if($args['has_date']): ?>		
			<time class="entry-date">30.12.2013</time>
		<?php endif;?>
		<?php if($args['has_cat']): ?>	
			<span class='sep'>//</span>
			<span class="category"><a href="" rel="tag">Рубрика</a></span>
		<?php endif;?>
		
		</div>
	<?php endif;?>
	
	</article>
<?php
}
?>

<?php require_once("inc/functions.php"); ?>
<?php include("inc/header.php"); ?>
	
<section class="home-section slider"><div class="inner">

	<?php slider();?>

</div></section>



<section class="home-section home-blocks"><div class="inner">
		
	<div class="frame">
	<?php
		$blocks = array(
			'Кто мы?',
			'Что мы делаем?',
			'Как нам помочь?'
		);
		//text_blocks($blocks);
		for($i = 0; $i <3; $i++){
			text_block(array('title' => $blocks[$i], 'css' => 'bit-4'));
		}
	?>	
	</div><!-- .frame -->
	

</div></section><!-- .home-section -->

<section class="home-section callout"><div class="inner">

	<?php
		$args = array(
			'layout' => 'button',
			'text' => '<h4>Хотите помогать нам делать этот мир лучше?</h4><p>Мы ждем вас прямо сегодня!</p>',
			'btn_text' => 'Участвовать'
		);
		callout($args);
	?>

</div></section>

<section class="home-section recent-widgets"><div class="inner">
		
	<div class="frame">
		<div class="bit-3">
		<?php
			$args_1 = array(
				'widget_title' => 'Анонсы',
				'has_thumb' => false,
				'has_date' => true,
				'has_cat' => false,
				'has_desc' => false,
				'num' => 3
			);
			recent_items($args_1);
		?>
		</div>
		
		<div class="bit-6">
		<?php
			$args_1 = array(
				'widget_title' => 'Последние новости',
				'has_thumb' => true,
				'has_date' => true,
				'has_cat' => true,
				'has_desc' => true,
				'num' => 2
			);
			recent_items($args_1);
		?>
		</div>
		
		<div class="bit-3">
		<?php
			$args_1 = array(
				'widget_title' => 'Новые материалы',
				'has_thumb' => false,
				'has_date' => false,
				'has_cat' => false,
				'has_desc' => false,
				'num' => 4
			);
			recent_items($args_1);
		?>
		</div>
		
	</div><!-- .frame -->
	
</div></section><!-- .home-section -->

<section class="home-section news-grid"><div class="inner">
	
	<div class="frame">
		
	</div>
</div></section><!-- .home-section -->

<section class="home-section home-blocks"><div class="inner">
		
	<div class="frame">
	<?php 
		$args_2 = array(
			'has_meta' => true,
			'has_desc' => true,
			'css' => 'bit-4'
		);
		
		for($i = 0; $i <3; $i++){
			pictured_block($args_2);
		}
	?>	
	</div><!-- .frame -->
	

</div></section><!-- .home-section -->

<section class="home-section news-grid"><div class="inner">
	<h3 class="grid-title">Наши новости</h3>
	
	<div class="frame">
	<?php
		for($i = 0; $i <8; $i++){
			news_grid_item(array('has_date' => true, 'has_cat' => true));
		}
	?>
	</div>
	
</div></section><!-- .home-section -->


<?php include("inc/footer.php");?>