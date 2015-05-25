<?php
/**
 * Widgets
 **/

 
add_action('widgets_init', 'ruka_custom_widgets', 11);
function ruka_custom_widgets(){

	unregister_widget('WP_Widget_Pages');
	unregister_widget('WP_Widget_Archives');
	unregister_widget('WP_Widget_Calendar');
	unregister_widget('WP_Widget_Meta');
	unregister_widget('WP_Widget_Categories');
	unregister_widget('WP_Widget_Recent_Posts');
	unregister_widget('WP_Widget_Tag_Cloud');
	unregister_widget('WP_Widget_RSS');
	unregister_widget('WP_Widget_Search');
	unregister_widget('FrmListEntries');
	
	
	
	register_widget('RUKA_Event_Post_Widget');
	register_widget('RUKA_Social_Links');
	//register_widget('TST_Related_Widget');
	
}



/** Featured Post **/
class RUKA_Event_Post_Widget extends WP_Widget {
	
	/** Widget setup */
	function __construct() {
        
		$widget_ops = array(
			'classname'   => 'widget_featured_content',
			'description' => 'Рекомендуемый отдельный материал'
		);
		$this->WP_Widget('widget_featured_content',  'Рекомендация', $widget_ops);	
	}

	
	/** Display widget */
	function widget($args, $instance) {
		global $post;
		
		extract($args, EXTR_SKIP);

		$title = apply_filters('widget_title', $instance['title']);
		$date = apply_filters('ruka_the_title', $instance['date']);	
		$cpost = get_post($instance['post_id']);
		if(empty($cpost))
			return;
		
		
		//markup
		echo $before_widget;
		
		if($title)
			echo $before_title.$title.$after_title;
			
	?>
		<div class="fw-item">
			
			<h3 class="entry-title">
				<a href="<?php echo get_the_permalink($cpost);?>"><?php echo get_the_title($cpost); ?></a>
			</h3>
			<?php if(!empty($date)) { ?>
				<div class="entry-meta"><?php echo $date;?></div>
			<?php } ?>
			<div class="entry-summary"><?php echo apply_filters('ruka_the_content', ruka_get_excerpt_with_link($cpost));?></div>
		</div>
		
	<?php	
		echo $after_widget;
	}
	
	

	/** Update widget */
	function update($new_instance, $old_instance) {

		$instance = $old_instance;
		
		$instance['title']    = sanitize_text_field($new_instance['title']);			
		$instance['post_id']  = intval($new_instance['post_id']);
		$instance['date']     = sanitize_text_field($new_instance['date']);		
				
		return $instance;
	}
	
	

	/** Widget setting */
	function form($instance) {

		/* Set up some default widget settings. */
		$defaults = array(
			'title'   => '',				
			'post_id' => 0,
			'date'    => ''
		);

		$instance = wp_parse_args((array)$instance, $defaults);
		
		$title = esc_attr($instance['title']);				
		$post_id = intval($instance['post_id']);
		$date = esc_attr($instance['date']);		
	?>	
		
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>">Заголовок:</label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo $title; ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('post_id')); ?>">ID записи:</label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id('post_id')); ?>" name="<?php echo esc_attr($this->get_field_name('post_id')); ?>" type="text" value="<?php echo $post_id; ?>">
		</p>	
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('date')); ?>">Дата:</label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id('date')); ?>" name="<?php echo esc_attr($this->get_field_name('date')); ?>" type="text" value="<?php echo $date; ?>">
		</p>
	<?php
	}
	
	
} // class end


/** Social Links Widget **/
class RUKA_Social_Links extends WP_Widget {
		
    function __construct() {
        $this->WP_Widget('widget_socila_links', __('Social Buttons', 'ruka'), array(
            'classname' => 'widget_socila_links',
            'description' => __('Social links menu with optional text', 'ruka'),
        ));
    }

    
    function widget($args, $instance) {
        extract($args); 
						
		
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$desc = apply_filters('the_content', $instance['desc']);
		
        echo $before_widget;
        if($title){			
            echo $before_title.$title.$after_title;
        }
		
		echo "<div class='social-menu-position'>";
		wp_nav_menu(array(
			'theme_location'  => 'social',
			//'menu'          => ,
			'menu_class'      => 'social-menu',
			'menu_id'         => 'social',
			'echo'            => true,                
			'depth'           => 0,
			'fallback_cb'     => ''
		));
		echo "</div>";
		
		if(!empty($desc))
			echo "<div class='social-desc'>{$desc}</div>";
		
		echo $after_widget;
    }
	
	
	
	
    function form($instance) {
		
        /* Set up some default widget settings */
		$defaults = array(
			'title' => '',			
			'desc'  => '',			
		);

		$instance = wp_parse_args((array)$instance, $defaults);
		
	?>
        <p>
            <label for="<?php echo $this->get_field_id('title');?>">
            Заголовок:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title');?>" type="text" value="<?php echo esc_attr($instance['title']);?>">
        </p>
		
		<p>
            <label for="<?php echo $this->get_field_id('desc');?>">Описание:</label>         
            <textarea id="<?php echo $this->get_field_id('desc'); ?>" name="<?php echo $this->get_field_name('desc');?>" class="widefat"><?php echo esc_textarea($instance['desc']); ?></textarea>
			
        </p>	
    <?php
    }

    
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        
		$instance['title'] = sanitize_text_field($new_instance['title'], 'save');				
		$instance['desc'] = wp_kses_post(trim($new_instance['desc']));	
		
        return $instance;
    }
	
} //class end