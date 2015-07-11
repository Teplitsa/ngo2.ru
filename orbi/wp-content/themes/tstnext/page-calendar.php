<?php
/**
 * Template Name: Calendar
 */

get_header(); ?>
<div class="calendar-content">
	<div class="mdl-grid">
		<div class="mdl-cell mdl-cell--9-col">			
			<div class="mdl-card mdl-shadow--2dp">
				
				<div class="mdl-grid">
					<div class="mdl-cell mdl-cell--3-col mdl-cell--8-col-tablet mdl-cell--4-col-phone">
					<?php
						$today_exact = strtotime(sprintf('now %s hours', get_option('gmt_offset')));
						
					?>
						<div class="mdl-typography--subhead"><?php echo date_i18n('Y', $today_exact);?></div>
						<div class="mdl-typography--headline"><?php echo date_i18n('l, j M.', $today_exact);?></div>
					</div>				
					<div class="mdl-cell mdl-cell--9-col mdl-cell--8-col-tablet mdl-cell--4-col-phone">
					<?php
						$cal = new TST_Calendar_Table();
						echo $cal->generate();
						
					?>
					</div>
				</div>
			</div>
		</div>
		
		<div class="mdl-cell mdl-cell--3-col mdl-cell--hide-phone mdl-cell--hide-tablet">
			<?php get_template_part('sidebar'); ?>
		</div>
	
	</div><!-- .mdl-grid -->
</div>

<div class="calendar-footer">
	<div class="mdl-grid">
		<div class="mdl-cell mdl-cell--4-col">Анонсы</div>
		
		<div class="mdl-cell mdl-cell--4-col">Прошедшие</div>
		<div class="mdl-cell mdl-cell--4-col"><?php get_template_part('sidebar'); ?></div>
	</div>		
</div>


<?php get_footer(); ?>
