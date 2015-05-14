<?php
/**
 * Events functions and templates
 *
 * Event custom fields keys
 * event_date
 * event_date_end
 * event_date_expire
 * event_time
 * event_address
 * event_location
 * event_contacts
 **/



function tst_event_undertitle_meta($echo = true) {
	
	$list = array();
	
	$date = tst_event_date();
	if(!empty($date)){
		$list['date']['label'] = __('Event date', 'tst');
		$list['date']['value'] = $date;
	}
	
	$time = tst_event_time();
	if(!empty($time)){
		$list['time']['label'] = __('Event time', 'tst');
		$list['time']['value'] = $time;
	}
	
	$addr = tst_event_address();
	if(!empty($addr)){
		$list['addr']['label'] = __('Event address', 'tst');
		$list['addr']['value'] = $addr;
	}
	
	if(empty($list))
		return '';
	
	//print
	$l = array();
	foreach ($list as $key => $obj) {
		
		$l[] =  '<dt class="'.esc_attr($key).'">'.$obj['label'].'</dt><dd class="'.esc_attr($key).'">'.$obj['value'].'</dd>';
	}
	
	$out = '<dl class="post-fields event intro">';
	$out .= implode('', $l);
	$out .= '</dl>';
	
	if($echo)
		echo $out;
	return
		$out;
}

function tst_event_date($cpost = null){
	global $post;
	
	if(!$cpost)
		$cpost = $post;
	
	$start = (function_exists('get_field')) ? get_field('event_date', $cpost->ID) : get_post_meta($cpost->ID, 'event_date', true);
	$end = (function_exists('get_field')) ? get_field('event_date_end', $cpost->ID) : get_post_meta($cpost->ID, 'event_date_end', true);;
	
	$date = '';
	
	if(isset($start) && !empty($start)){		
		$date = date_i18n('d.m.Y (D)', strtotime($start));
	}
	
	if(!empty($date) && isset($end) && !empty($end))
		$date = $date.' - '.date_i18n('d.m.Y (D)', strtotime($end));
		
	return "<time>".$date."</time>";
}

function tst_event_time($cpost = null) {
	global $post;
	
	if(!$cpost)
		$cpost = $post;
		
	return (function_exists('get_field')) ? get_field('event_time', $cpost->ID) : get_post_meta($cpost->ID, 'event_time', true);
}

function tst_event_address($cpost = null) {
	global $post;
	
	if(!$cpost)
		$cpost = $post;
		
	return (function_exists('get_field')) ? get_field('event_address', $cpost->ID) : get_post_meta($cpost->ID, 'event_address', true);
}

function tst_event_contacts($cpost = null) {
	global $post;
	
	if(!$cpost)
		$cpost = $post;
		
	$contact = (function_exists('get_field')) ? get_field('event_contacts', $cpost->ID) : get_post_meta($cpost->ID, 'event_contacts', true);
	return make_clickable($contact);
}

function tst_event_map_block($cpost = null) {
	global $post;
	
	if(!$cpost)
		$cpost = $post;
		
	//[symple_googlemap title="Envato Office" location="2 Elizabeth St, Melbourne Victoria 3000 Australia" zoom="10" height=250]
	$addr = (function_exists('get_field')) ? get_field('event_location', $cpost->ID) : get_post_meta($cpost->ID, 'event_location', true);
	if(empty($addr))
		return;
	
	$addr = esc_attr($addr);
	$sc = '[symple_googlemap location="'.$addr.'" zoom="15" height=250]';
?>
	<div class="event-map"><?php echo do_shortcode($sc);?></div>
<?php
}


function tst_event_contacts_block(){
	
	$c = tst_event_contacts();
	if(empty($c))
		return;
	
?>
	<div class="post-fields event contact">
		<dt><?php _e('Contacts', 'tst');?></dt>
		<dd><?php echo apply_filters('frl_the_content', $c);?></dd>
	</div>
<?php
}

function tst_event_nextprev(){
	global $post;
	
	$cat = wp_get_object_terms($post->ID, 'eventcat');
	if(empty($cat) || is_wp_error( $cat ))
		return;
	
	$link = get_term_link($cat[0]);
	$txt = __('All events', 'tst');
?>
	
	<span class="nav-next">
		<a rel="next" href="<?php echo esc_url($link);?>"><?php echo $txt;?> &raquo;</a>
	</span>
<?php
}

/**
 * Filter query for upcoming / past category
 **/
add_action('parse_query', 'tst_event_correct_query', 5);
function tst_event_correct_query($query){
	
	if(isset($query->query_vars['eventcat']) && !empty($query->query_vars['eventcat']) ){
		
		$cat = $query->query_vars['eventcat'];
		$events_qv = array(
			'post_type' => 'event',
			'meta_query' => array(
				array(
					'key' => 'event_date_expire',
					'value' => date('Y-m-d', strtotime('now')),
					'compare' => ($cat == 'anonsy' || $cat == 'upcoming') ? '>' : '<='
				)
			),
			'orderby' => 'meta_value',
			'meta_key' => 'event_date_expire',
			'order' => 'DESC'
		); 
		$query->query_vars = array_merge($query->query_vars, $events_qv);
		//unset($query->query_vars['eventcat']);
		if(class_exists('PLL_Frontend')){ //filter for lang
			if($cat == 'anonsy' || $cat == 'proshed')
				$query->query_vars['lang'] = 'ru';
			else
				$query->query_vars['lang'] = 'en';
		}
		
		//var_dump($query->query_vars);
	}
}

/** Cron for Events and Upcoming category */
if( !wp_next_scheduled('frl_check_events_actuality') ) {
    wp_schedule_event(time(), 'twicedaily', 'frl_check_events_actuality');
}
//add_action('frl_check_events_actuality','frl_check_events_actuality_action');
//add_action('admin_init','frl_check_events_actuality_action');
function frl_check_events_actuality_action(){
	
	//events in anonsy category are in future
	$actual_eventcat_id = get_term_by('slug', 'anonsy', 'eventcat')->term_id;
    $past_eventcat_id = get_term_by('slug', 'proshed', 'eventcat')->term_id;
	$cur_date = date('Y-m-d', strtotime('now'));
	
	$events = new WP_Query(array(
			'post_type' => 'event',
			'posts_per_page' => -1,
			'tax_query' => array(
				array(
					'taxonomy' => 'eventcat',
					'field' => 'id',
					'terms' => $actual_eventcat_id
				)
			)
	));
	
	if(!empty($events->posts)){ foreach($events->posts as $event){
		
		$event_date_exp = get_field('event_date_expire', $event->ID);
		if( !$event_date_exp )
            $event_date_exp = get_field('event_date_end', $event->ID);
        if( !$event_date_exp )
            $event_date_exp = get_field('event_date', $event->ID);
        		
		if($event_date_exp < $cur_date){ //past event - move into past
			wp_set_post_terms($event->ID, array($past_eventcat_id), 'eventcat'); 
		}
	}}
	
	//future events are in anonsy
	$events = new WP_Query(array(
			'post_type' => 'event',
			'posts_per_page' => -1,
			'meta_query' => array(
				array(
					'key' => 'event_date_expire',
					'value' => date('Y-m-d', strtotime('now')),
					'compare' => '>'
				)
			)
	));
	
	if(!empty($events->posts)){ foreach($events->posts as $event){
		
		if(!is_object_in_term($event->ID, 'eventcat', $actual_eventcat_id)){ //wrong category move into anonsy
			wp_set_post_terms($event->ID, array($actual_eventcat_id), 'eventcat'); 
		}
	}}
    
}

 
/**
 * Upcoming widget
 **/

class TST_Upcoming_Widget extends WP_Widget {

	/** Widget setup */
	function __construct() {
        
		$widget_ops = array(
			'classname'   => 'widget_upcoming',
			'description' => __('Upcoming events list', 'tst')
		);
		$this->WP_Widget('widget_upcoming', __('Upcoming events', 'tst'), $widget_ops);	
	}

	/** Display widget */
	function widget($args, $instance) {
		global $post;
		
		extract($args, EXTR_SKIP);
		
		$title = $instance['title'];	
		$limit = intval($instance['limit']);			
		
		$title = apply_filters('widget_title', $title);
		
		echo $before_widget;
		if($title)
			echo $before_title.$title.$after_title;
			
		// markup here
		$args = array(
			'thumb'      => $instance['thumb'],
			'meta'       => $instance['meta'],
			'thumb_size' => $instance['thumb_size'],
			'excerpt'    => $instance['excerpt'],
			'length'     => (int)($instance['length'])
		);
		tst_upcoming_events_list($limit, $args);		
		
		echo $after_widget;
		
	}	
	
	function update($new_instance, $old_instance) {

		$instance = $old_instance;
		$instance['title']    = esc_attr($new_instance['title']);			
		$instance['limit']    = intval($new_instance['limit']);
		$instance['length']    = intval($new_instance['length']);
		$instance['excerpt'] = $new_instance['excerpt'];
		$instance['thumb']    = $new_instance['thumb'];
		$instance['thumb_size'] = $new_instance['thumb_size'];
		$instance['meta']     = $new_instance['meta'];
		
		return $instance;
	}
	
	/** Widget setting */
	function form($instance) {

		/* Set up some default widget settings. */
		$defaults = array(
			'title' => '',			
			'limit' => 5,
			'excerpt' => false,
			'length' => 10,
			'thumb' => true,
			'thumb_size' => '',
			'meta' => true,	
		);

		$instance = wp_parse_args((array)$instance, $defaults);
		
		$title = esc_attr($instance['title']);
		$limit = intval($instance['limit']);
		$excerpt = $instance['excerpt'];
		$thumb = $instance['thumb'];
		$thumb_size = $instance['thumb_size'];
		$meta = $instance['meta'];
		$length = (int)($instance['length']);
		
	?>			
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title', 'tst'); ?></label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo $title; ?>"/>
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('limit')); ?>"><?php _e('Num.', 'tst'); ?></label>
			<select class="widefat" name="<?php echo $this->get_field_name('limit'); ?>" id="<?php echo $this->get_field_id('limit'); ?>">
				<?php for ($i = 1; $i <= 20; $i++) { ?>
					<option <?php selected($limit, $i) ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
				<?php } ?>
			</select>
		</p>
		
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('excerpt')); ?>"><?php _e('Display Excerpt?', 'tst'); ?></label>
			<input id="<?php echo $this->get_field_id('excerpt'); ?>" name="<?php echo $this->get_field_name('excerpt'); ?>" type="checkbox" value="1" <?php checked('1', $excerpt); ?> />&nbsp;
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('length')); ?>"><?php _e('Excerpt Length:', 'la-rpw'); ?></label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id('length')); ?>" name="<?php echo esc_attr($this->get_field_name('length')); ?>" type="text" value="<?php echo $length; ?>"/>
		</p>
		
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('meta')); ?>"><?php _e('Display Metas?', 'tst'); ?></label>
			<input id="<?php echo $this->get_field_id('meta'); ?>" name="<?php echo $this->get_field_name('meta'); ?>" type="checkbox" value="1" <?php checked('1', $meta); ?> />&nbsp;
		</p>
		
		<?php if(current_theme_supports('post-thumbnails')) :?>

			<p>
				<label for="<?php echo esc_attr($this->get_field_id('thumb')); ?>"><?php _e('Display Thumbnail?', 'tst'); ?></label>
				<input id="<?php echo $this->get_field_id('thumb'); ?>" name="<?php echo $this->get_field_name('thumb'); ?>" type="checkbox" value="1" <?php checked('1', $thumb); ?> />&nbsp;
			</p>
			
			<p>
				<label for="<?php echo $this->get_field_id( 'thumb_size' ); ?>"><?php _e('Thumbnail size', 'tst'); ?>: </label>
				<input id="<?php echo $this->get_field_id( 'thumb_size' ); ?>" name="<?php echo $this->get_field_name( 'thumb_size' ); ?>" value="<?php echo $thumb_size; ?>" type="text" class="widefat"><br/>
				<small class="help"><?php _e('Specify thumbnail size. Leave blank to apply size defined by theme.', 'tst'); ?> </small>
			</p>

		<?php endif; ?>
	<?php
	}
	
	
} //class end


function tst_upcoming_events_list($limit = 5, $args = array()){
	global $post;
	
	$defaults = array(
		'excerpt' => false,
		'length' => 10,
		'thumb' => true,
		'meta' => true,
		'thumb_size' => 'post-thumbnail'
	);
	$args = wp_parse_args($args, $defaults);
	
	$query = new WP_Query(array(
		'post_type' => 'event',
		'posts_per_page' => $limit,
		'meta_query' => array(
			array(
				'key' => 'event_date_expire',
				'value' => date('Y-m-d', strtotime('now')),
				'compare' => '>'
			)
		),
		'orderby' => 'meta_value',
		'meta_key' => 'event_date_expire',
		'order' => 'ASC'		
	));
	
	if($query->have_posts() && class_exists('La_Recent_Posts_Widget')):
?>
	<ul class="upcoming-events la-rpw-ul">
	<?php while($query->have_posts()): $query->the_post(); $pt = get_post_type();?>
	<li class="la-rpw-item cf <? echo $pt;?><?php if($args['thumb']) echo ' has-thumb';?>">
	<?php
		if(has_post_thumbnail() && $args['thumb'] == true){
			La_Recent_Posts_Widget::_recent_item_preview($args['thumb_size']);
		}
		
		La_Recent_Posts_Widget::_recent_item_title();		
		
		if($args['meta']){
			$meta = tst_upcoming_event_meta($post);
			$sep = frl_get_sep();
				
			if(!empty($meta))
				La_Recent_Posts_Widget::_recent_item_meta($meta, $sep);
		}

		if($args['excerpt']){
			La_Recent_Posts_Widget::_recent_item_excerpt($args['length']);
		}
	?>
	</li>
	<?php endwhile; wp_reset_postdata(); ?>
	</ul>
<?php
	endif;
	
}

function tst_upcoming_event_meta($post) {
	
	$meta = array();
	$meta[] = tst_event_date();
		
	return $meta;
}