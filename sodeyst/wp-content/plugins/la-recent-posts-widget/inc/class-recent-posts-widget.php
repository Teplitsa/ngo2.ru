<?php if( !defined('ABSPATH') ) exit; // Exit if accessed directly

class La_Recent_Posts_Widget extends WP_Widget {

	/** Widget setup */
	function __construct() {
        
		$widget_ops = array(
			'classname'   => 'widget_la_recent',
			'description' => __('Improved recent posts widget', 'la-rpw')
		);
		$this->WP_Widget('widget_la_recent', __('La Recent Posts', 'la-rpw'), $widget_ops);	
	}

	/** Display widget */
	function widget($args, $instance) {
		global $post;
		
		extract($args, EXTR_SKIP);

		$title = apply_filters('widget_title', $instance['title']);
		
		$limit = $instance['limit']; 
		$excerpt = $instance['excerpt'];
		$length = (int)($instance['length']);
		$thumb = $instance['thumb'];
		$thumb_size = $instance['thumb_size'];
		
		
		$taxonomy = $instance['taxonomy'];
		$term = $instance['slug'];
		$post_type = $instance['post_type'];
		$date = $instance['date'];
		

		echo $before_widget;
	

		if($title)
			echo $before_title.$title.$after_title;
		
		$args = array(
			'post_type' => (!empty($post_type)) ? $post_type : 'any',
			'posts_per_page' => $limit
		);
		
		if(!empty($taxonomy) && !empty($term)){
			$args['tax_query'] = array(
				array(
					'taxonomy' => $taxonomy,
					'field' => 'slug',
					'terms' => $term
				)
			);
		}
		
		
		$query = new WP_Query($args);
		if($query->have_posts()):
		?>

		<div class="la-rpw-block">

			<ul class="la-rpw-ul">

			<?php while($query->have_posts()): $query->the_post(); $pt = esc_attr(get_post_type()); ?>

				<li class="la-rpw-item la-rpw-clearfix <? echo $pt;?><?php if($thumb) echo ' has-thumb';?>">

			<?php
				if(has_post_thumbnail() && $thumb == true){ 
					
					if(empty($thumb_size))
							$thumb_size = apply_filters('la_rpw_thumbnail_size', 'post-thumbnail', $post, $instance);
					
					self::_recent_item_preview($thumb_size);
				}
					
				self::_recent_item_title();
				
				$meta = array();
				if($date)
					$meta[] = "<time>".get_the_date()."</time>";
				
				$meta = apply_filters('la_rpw_post_meta', $meta, $post, $instance);
				$sep = apply_filters('la_rpw_post_meta_separator', '<span class="sep">//</span>', $post);
				
				if(!empty($meta)){
					self::_recent_item_meta($meta, $sep);
				}
					
				if($excerpt){
					self::_recent_item_excerpt($length);
				}
			?>
				</li>

				<?php endwhile; wp_reset_postdata(); ?>

			</ul>

		</div><!-- .la-rpw-block -->

		<?php endif; echo $after_widget;
	}
	
	/**
	 * Markup methods
	 * work only inside the loop
	 **/
	static function _recent_item_preview($thumb_size){
	?>
		<div class="la-rpw-preview">
			<a href="<?php the_permalink(); ?>" rel="bookmark" ><?php the_post_thumbnail($thumb_size);?></a>
		</div>
	<?php
	}
	
	static function _recent_item_title(){
	?>
		<div class="la-rpw-title">
			<a href="<?php the_permalink();?>" title="<?php printf(esc_attr__('Permalink to %s', 'la-rpw'), the_title_attribute('echo=0')); ?>" rel="bookmark"><?php the_title();?></a>
		</div>	
	<?php
	}
	
	static function _recent_item_meta($meta, $sep){
	?>
		<div class="la-rpw-metadata"><?php echo implode($sep, $meta); ?></div>
	<?php
	}
	
	static function _recent_item_excerpt($length){
	?>
		<div class="la-rpw-excerpt"><?php echo self::create_excerpt($length);?></div>
	<?php
	}
	
	static function create_excerpt($excerpt_length = 55) {
		
		$text = get_the_excerpt();
		$text = str_replace(']]>', ']]&gt;', $text);
		
		//filter shortcodes
		$text = preg_replace( '|\[(.+?)\](.+?\[/\\1\])?|s', '', $text );
		
		if(empty($text))
			return '';
		
		//check do we need trimming
		$all_words = preg_split("%\s*((?:<[^>]+>)+\S*)\s*|\s+%s", $text,-1,PREG_SPLIT_NO_EMPTY);
		
		if(count($all_words) <= $excerpt_length)
			return $text;
		
		// trim finally
		$words = preg_split("%\s*((?:<[^>]+>)+\S*)\s*|\s+%s", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		
		if(count($words) > $excerpt_length) {
			array_pop($words);
			$text = implode(' ', $words);
		}
		
		//clear last symbol
		$last = substr($text, -1);
		preg_match("/^(,|\.|!|\?|\-|:)$/", $last, $matches);
		if(!empty($matches))
			$text = substr($text, 0, -1);
		
		
		$text .= '&hellip;';
		
		
		return $text;
	}

	/** Update widget */
	function update($new_instance, $old_instance) {

		$instance = $old_instance;
		$instance['title'] = esc_attr($new_instance['title']);
			
		$instance['limit'] = $new_instance['limit'];
		$instance['excerpt'] = $new_instance['excerpt'];
		$instance['length'] = (int)($new_instance['length']);
		
		$instance['thumb'] = $new_instance['thumb'];
		$instance['thumb_size'] = $new_instance['thumb_size'];
		
		
		$instance['taxonomy'] = $new_instance['taxonomy'];
		$instance['slug'] = $new_instance['slug']; 
		$instance['post_type'] = $new_instance['post_type'];		
		$instance['date'] = $new_instance['date'];
		
		

		return $instance;
	}
	
	

	/** Widget setting */
	function form($instance) {

		/* Set up some default widget settings. */
		$defaults = array(
			'title' => '',				
			'limit' => 5,
			'excerpt' => '',
			'length' => 10,
			'thumb' => true,
			'thumb_size' => '',
			'taxonomy' => '',
			'slug' => '',
			'post_type' => '',
			'date' => true,			
		);

		$instance = wp_parse_args((array)$instance, $defaults);
		
		$title = esc_attr($instance['title']);
				
		$limit = $instance['limit'];
		$excerpt = $instance['excerpt'];
		$length = (int)($instance['length']);
		
		$thumb = $instance['thumb'];
		$thumb_size = $instance['thumb_size'];
				
		$taxonomy = $instance['taxonomy'];
		$term = $instance['slug']; 
		$post_type = $instance['post_type'];
		
		$date = $instance['date'];
				
		$post_types = get_post_types(array('public' => true), 'objects');
		$taxes = get_taxonomies(array('public' => true), 'objects'); 
	?>
		
		
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'la-rpw');?></label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo $title; ?>"/>
		</p>
			
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('limit')); ?>"><?php _e('Limit:', 'la-rpw'); ?></label>
			<select class="widefat" name="<?php echo $this->get_field_name('limit'); ?>" id="<?php echo $this->get_field_id('limit'); ?>">
				<?php for ($i = 1; $i <= 20; $i++) { ?>
					<option <?php selected($limit, $i) ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('date')); ?>"><?php _e('Display Date?', 'la-rpw'); ?></label>
			<input id="<?php echo $this->get_field_id('date'); ?>" name="<?php echo $this->get_field_name('date'); ?>" type="checkbox" value="1" <?php checked('1', $date); ?> />&nbsp;
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('excerpt')); ?>"><?php _e('Display Excerpt?', 'la-rpw'); ?></label>
			<input id="<?php echo $this->get_field_id('excerpt'); ?>" name="<?php echo $this->get_field_name('excerpt'); ?>" type="checkbox" value="1" <?php checked('1', $excerpt); ?> />&nbsp;
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('length')); ?>"><?php _e('Excerpt Length:', 'la-rpw'); ?></label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id('length')); ?>" name="<?php echo esc_attr($this->get_field_name('length')); ?>" type="text" value="<?php echo $length; ?>"/>
		</p>

		<?php if(current_theme_supports('post-thumbnails')) :?>

			<p>
				<label for="<?php echo esc_attr($this->get_field_id('thumb')); ?>"><?php _e('Display Thumbnail?', 'la-rpw'); ?></label>
				<input id="<?php echo $this->get_field_id('thumb'); ?>" name="<?php echo $this->get_field_name('thumb'); ?>" type="checkbox" value="1" <?php checked('1', $thumb); ?> />&nbsp;
			</p>
			
			<p>
				<label for="<?php echo $this->get_field_id( 'thumb_size' ); ?>"><?php _e('Thumbnail size', 'la-rpw'); ?>: </label>
				<input id="<?php echo $this->get_field_id( 'thumb_size' ); ?>" name="<?php echo $this->get_field_name( 'thumb_size' ); ?>" value="<?php echo $thumb_size; ?>" type="text" class="widefat"><br/>
				<small class="help"><?php _e('Specify thumbnail size. Leave blank to apply size defined by theme.', 'la-rpw'); ?> </small>
			</p>

		<?php endif; ?>
		
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('post_type'));?>"><?php _e('Choose the Post Type: ', 'la-rpw');?></label>

			<select class="widefat" id="<?php echo $this->get_field_id('post_type');?>" name="<?php echo $this->get_field_name('post_type');?>">
				<option value="0"><?php _e('Select post type', 'la-rpw');?></option>
				<?php foreach ($post_types as $post_type) {?>
					<option value="<?php echo esc_attr($post_type->name);?>" <?php selected($instance['post_type'], $post_type->name);?>><?php echo esc_html($post_type->labels->singular_name);?></option>
				<?php } ?>
			</select>
		</p>
		
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('taxonomy'));?>"><?php _e('Choose the Taxonomy: ', 'la-rpw');?></label>

			<select class="widefat" id="<?php echo $this->get_field_id('taxonomy');?>" name="<?php echo $this->get_field_name('taxonomy');?>">
			<option value="0"><?php _e('Select taxonomy', 'la-rpw');?></option>
				<?php foreach ($taxes as $id => $tax):  ?>
					<option value="<?php echo esc_attr($id);?>" <?php selected($instance['taxonomy'], $id);?>><?php echo esc_html($tax->labels->name);?></option>
				<?php endforeach; ?>
			</select>
		</p>
		
		
		<p>
		<label for="<?php echo $this->get_field_id( 'slug' ); ?>"><?php _e('Term slug', 'la-rpw'); ?>: </label>
		<input id="<?php echo $this->get_field_id( 'slug' ); ?>" name="<?php echo $this->get_field_name( 'slug' ); ?>" value="<?php echo $term; ?>" type="text" class="widefat"><br/>
		<small class="help"><?php _e('Copy paste a slug (or comma-separated list of several slugs) of the term', 'la-rpw'); ?> </small>
		</p>
		
		
	<?php
	}
	
	
	
} //class end



