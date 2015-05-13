<?php
/**
 * Core banner functionality
 *
 * to-do: load basic CSS 
 **/

 
class La_Banner_Core {
	
	private static $instance = NULL; //instance store
	var $post_type = 'banner';
	var $taxonomy = 'banner_element_type';
	
	private function __construct() {
                
        
        add_action('init', array($this, 'register_objects'));
		add_shortcode('la_banner', array($this, 'shortcode_screen'));
		add_action('widgets_init', array($this, 'register_widgets'));
    }
	
	
	/* get core instance */
    public static function get_instance(){
        
        if (NULL === self :: $instance)
			self :: $instance = new self;
					
		return self :: $instance;
    }       
	
	
	function register_objects(){
		
		/** CPT **/
		$labels = array(
			'name' => __('Banners', 'la-banner'),
			'singular_name' => __('Banner', 'la-banner'),
			'menu_name' => __('Banners', 'la-banner'),
			'all_items' => __('All banners', 'la-banner'),
			'add_new' => __('Add new banner', 'la-banner'),
			'add_new_item' => __('Add banner', 'la-banner'),
			'edit_item' => __('Edit banner', 'la-banner'),
			'new_item' => __('New banner', 'la-banner'),
			'view_item' => __('View banner', 'la-banner'),
			'search_items' => __('Search banners', 'la-banner'),
			'not_found' =>  __('No banners found', 'la-banner'),
			'not_found_in_trash' => __('No banners found in Trash', 'la-banner'), 
			'parent_item_colon' => ''
		);		
		
		$args = array(
			'labels' => $labels,
			'public' => true,
			'exclude_from_search' => true,
			'show_in_nav_menus' => false,
			'show_in_menu' => true,
			'show_in_admin_bar' => false,
			'menu_position' => 20,
			'capability_type' => 'post',
			'hierarchical' => false,
			'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'),
			//'register_meta_box_cb' => '', to-do,
			'has_archive' => false,			
			'query_var' => true,
			'rewrite' => array(
						'slug'=> 'partner',
						'with_front'=>false
						)
		);
		
		register_post_type($this->post_type, $args );
		add_filter('post_updated_messages', array($this, 'filter_updated_messages'), 10);
		
		
		/* CT */
		$tlabels = array(
			'name' => __('Banners Types', 'la-banner'),
			'singular_name' => __('Banners Type', 'la-banner'),
			'menu_name' => __('Banners Types', 'la-banner'),
			'all_items' => __('All Types', 'la-banner'),			
			'edit_item' => __('Edit Type', 'la-banner'),
			'view_item' => __('View Type', 'la-banner'),
			'update_item' => __('Update Type', 'la-banner'),
			'add_new_item' => __('Add new Type', 'la-banner'),
			'new_item_name' => __('New Type Name', 'la-banner'),
			'parent_item' => __('Parent Type', 'la-banner'),
			'parent_item_colon' => __('Parent Type:', 'la-banner'),
			'search_items' => __('Search Types', 'la-banner'),
			'popular_items' => __('Popular Types', 'la-banner'),
			'separate_items_with_commas' => __('Separate with commas', 'la-banner'),
			'add_or_remove_items' => __('Add or remove Types', 'la-banner'),
			'choose_from_most_used' => __('Choose from most used', 'la-banner'),
			'not_found' => __('Types not found', 'la-banner'),
		);
		
		$tcaps = array(
			'manage_terms' => 'manage_categories',
			'edit_terms'   => 'manage_categories',
			'delete_terms' => 'manage_categories',
			'assign_terms' => 'edit_posts' 
		);
		
		$targs = array(
			'labels' => $tlabels,
			'public' => true,
			'show_ui' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud' => false,
			'show_admin_column' => true,
			'hierarchical' => true,
			'rewrite' => true,
			'capabilities' => $tcaps			
		);
		
		register_taxonomy( $this->taxonomy, array($this->post_type), $targs );
		
		
	}
	
	/* CPT messages */
	function filter_updated_messages($messages){
		global $post, $post_ID;
		
		if($post->post_type != $this->post_type)
			return $messages;
		
		
		$la_messages = array(
			0 => '', // Unused. Messages start at index 1.
			1 => __('Banner updated.', 'la-banner'),
			2 => __('Custom field updated', 'la-banner'),
			3 => __('Custom field deleted', 'la-banner'),
			4 => __('Banner updated', 'la-banner'),    
			5 => isset($_GET['revision']) ? __('Banner restored to revision', 'la-banner') : false,
			6 => __('Banner published.', 'la-banner'), 
			7 => __('Banner saved', 'la-banner'),
			8 => __('Banner submitted', 'la-banner'),
			9 => __('Banner scheduled', 'la-banner'),
			10 => __('Banner draft updated', 'la-banner')
		);
		
		
		
		$messages[$post->post_type] = $la_messages;
		
		return $messages;		 
	}
	
	
	function shortcode_screen($atts){
				
		extract(shortcode_atts(array(
			'format'      => 'logo_list',
			'format_args' => '',
			'num'         => 4,
			'type'        => '',
			'orderby'     => 'rand',
			'order'       => 'DESC', 
		), $atts, 'la_banner' ));
		
		$args = array(
			'post_type' => $this->post_type,
			'post_status' => 'publish',
			'posts_per_page' => $num,
			'orderby' => $orderby,
			'order' => $order
		);
		
		if(!empty($type)){
			$args['tax_query'] = array(
				array(
					'taxonomy' => $this->taxonomy,
					'field' => 'slug',
					'terms' => $type
				)
			);
		}
		
		$query = new WP_Query($args); 
		if(!$query->have_posts())
			return '';
		
		/**
		 * to-do: find a good way to apply callback for format
		 **/
		$callback = 'la_banner_format_'.$format;
		if(is_callable($callback))
			return call_user_func($callback, $query, $format_args);
	}
	
	function register_widgets() {
		
		register_widget('La_Banner_Widget');
	}
	
} //class end



/**
 * Common templates for banners
 **/

/**
 * Logo gallery
 * contains additional frame to resize logos vertically
 **/
function la_banner_format_logo_gallery($query, $format_args){	
	
	//prepare args
	$format_args = explode(',', $format_args);
	$format_args = array_map('trim', $format_args);
	$size = (isset($format_args[0]) && !empty($format_args[0])) ? $format_args[0] : 'logo'; 
	$css = (isset($format_args[1]) && !empty($format_args[1])) ? $format_args[1] : 'regular';
	
	ob_start();
?>
	<ul class="cf logo-gallery <?php echo $css;?>">
    <?php
		foreach($query->posts as $item):
        
            $url = (!empty($post->post_excerpt)) ? esc_url($item->post_excerpt) : '';
            $txt = esc_attr($item->post_title);
        ?>
		<li class="logo">
			<div class='logo-frame'>			
			<?php if(!empty($url)): ?>
				<a href="<?php echo $url;?>" target="_blank" title="<?php echo $txt;?>" class="logo-link">
			<?php else: ?>
				<span class="logo-link" title="<?php echo $txt;?>">
			<?php endif;?>
			
			<?php echo get_the_post_thumbnail($item->ID, $size);?>
		
			<?php if(!empty($url)): ?>
				</a>
			<?php else: ?>
				</span>
			<?php endif;?>
			</div>
		</li>
        <?php endforeach; ?>        
    </ul>
<?php	
	$out = ob_get_contents();
	ob_end_clean();
	
	return $out;
}


/**
 * Banners gallery
 * simple image gallery of banners
 **/
function la_banner_format_banners_gallery($query, $format_args) {
	
	//prepare args	
	$format_args = explode(',', $format_args);
	$format_args = array_map('trim', $format_args);
	$size = (isset($format_args[0]) && !empty($format_args[0])) ? $format_args[0] : 'post-thumbnail'; 
	$css = (isset($format_args[1]) && !empty($format_args[1])) ? $format_args[1] : 'regular';
	
	//check for plugin
	if(!class_exists('La_Banner_Core'))
		return '';		
	
	ob_start();	
?>
	<ul class="cf banners-gallery <?php echo $css;?>">
    <?php
		foreach($query->posts as $item):
        
            $url = (!empty($item->post_excerpt)) ? esc_url($item->post_excerpt) : '';
            $txt = esc_attr($item->post_title);
        ?>
            <li class="banner">
                <?php if(!empty($url)): ?>
                    <a href="<?php echo $url;?>" target="_blank" title="<?php echo $txt;?>">
                <?php else: ?>
                    <span title="<?php echo $txt;?>">
                <?php endif;?>
                
                <?php echo get_the_post_thumbnail($item->ID, $size);?>
            
                <?php if(!empty($url)): ?>
                    </a>
                <?php else: ?>
                    </span>
                <?php endif;?>
            </li>
        <?php endforeach; ?>        
    </ul>
	
<?php     
    $out = ob_get_contents();
    ob_end_clean();
    return $out;
}


/**
 * Team list
 * list of people with descriptions
 **/
function la_banner_format_team_list($query, $format_args) {
	global $post;
	
	//prepare args	
	$format_args = explode(',', $format_args);
	$format_args = array_map('trim', $format_args);
	$size = (isset($format_args[0]) && !empty($format_args[0])) ? $format_args[0] : 'post-thumbnail'; 
	$css = (isset($format_args[1]) && !empty($format_args[1])) ? $format_args[1] : 'regular';
	
	//check for plugin
	if(!class_exists('La_Banner_Core'))
		return '';		
	
	$out = '<div class="team-list '.$css.'">';
	while($query->have_posts()): $query->the_post();
	$out .= frl_banner_list_item($post, $size);
	endwhile; wp_reset_postdata();
    $out .= "</div>";


    return $out;
}

/**
 * Parnters list
 * list of companies with descriptions
 **/
function la_banner_format_partners_list($query, $format_args) {
	global $post;
	
	//prepare args	
	$format_args = explode(',', $format_args);
	$format_args = array_map('trim', $format_args);
	$size = (isset($format_args[0]) && !empty($format_args[0])) ? $format_args[0] : 'logo'; 
	$css = (isset($format_args[1]) && !empty($format_args[1])) ? $format_args[1] : 'regular';
	
	//check for plugin
	if(!class_exists('La_Banner_Core'))
		return '';	

	$out = '<div class="partners-list '.$css.'">';
	while($query->have_posts()): $query->the_post();
	$out .= frl_banner_list_item($post, $size);
	endwhile; wp_reset_postdata();
    $out .= "</div>";


    return $out;
}

function frl_banner_list_item($post, $size){
	
	$out = "<article class='banner-full frame'>";
	$url =  esc_url($post->post_excerpt);
	$logo = get_the_post_thumbnail($post->ID, $size);
	$title = apply_filters('the_title', $post->post_title);
	$desc = apply_filters('frl_the_content', $post->post_content);
	
	$out .= "<div class='bit-3'><div class='logo'><div class='logo-frame'><a href='{$url}' title='{$title}'>{$logo}</a></div></div></div>";
	$out .= "<div class='bit-9'><h4>{$title}</h4>";
	$out .= "<div class='details'>{$desc}</div></div>";
	$out .= "</article>";
	
	return $out;
}




/**
 * Widget
 **/
class La_Banner_Widget extends WP_Widget {
	
	var $post_type = 'banner';
	var $taxonomy = 'banner_element_type';
	
    function __construct() {
        $this->WP_Widget('widget_banners', 'Баннеры', array(
            'classname' => 'widget_banners',
            'description' => 'Демонстрация баннеров в соответствии с предустановленным форматом',
        ));
    }

    
    function widget($args, $instance) {
        extract($args);
        
		$shortcode = '[la_banner ';
		
		$shortcode .= " format=".$instance['format'];
		$shortcode .= " format_args=".$instance['format_args'];
		
		$num = intval($instance['num']);
		if($num == 0)
			$num = -1;
		
		$shortcode .= " num=".$num;
		$shortcode .= " orderby=".$instance['orderby'];
		$shortcode .= " order=".$instance['order'];
		$shortcode .= " type=".$instance['type'];
		$shortcode .= ']';
			
        echo $before_widget;
        if(isset($instance['title']) && !empty($instance['title']))
            echo $before_title.apply_filters('the_title', $instance['title']).$after_title;?>

        <div class="<?php echo isset($this->widget_options['classname']) ? $this->widget_options['classname'].'-inner' : 'widget-inner'?>">
            <?php echo do_shortcode($shortcode);?>
        </div>
    <?php echo $after_widget;
    }
    
    
    function form($instance) {
		
        /* Set up some default widget settings */
		$defaults = array(
			'title'       => '',
			'format'      => '',
			'format_args' => '',	
			'num'         => 5,
			'orderby'     => 'rand',
			'order'       => 'DESC',
			'type'        => ''
		);

		$instance = wp_parse_args((array)$instance, $defaults);
		
		
		$orderby = esc_attr($instance['orderby']);
		$order = esc_attr($instance['order']);
		$type = esc_attr($instance['type']);		
		$num = intval($instance['num']);
		
		$orderby_options = array(
			'rand'       => __('Random', 'la-banner'),
			'title'      => __('By title', 'la-banner'),
			'menu_order' => __('Sorting Order', 'la-banner'),
		);
		
		$order_options = array(
			'DESC' => __('Descebding', 'la-banner'),
			'ASC'  => __('Ascending', 'la-banner')			
		);
		
		$terms = get_terms($this->taxonomy, array('hide_empty' => 1));
	?>

        <p>
            <label for="<?php echo $this->get_field_id('title');?>">
            <?php _e('Title:', 'la-banner');?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title');?>" type="text" value="<?php echo esc_attr($instance['title']);?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('format');?>">
            <?php _e('Format:', 'la-banner');?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('format'); ?>" name="<?php echo $this->get_field_name('format');?>" type="text" value="<?php echo esc_attr($instance['format']);?>">
        </p>
		
		<p>
            <label for="<?php echo $this->get_field_id('format');?>">
            <?php _e('Format Arguments:', 'la-banner');?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('format_args'); ?>" name="<?php echo $this->get_field_name('format_args');?>" type="text" value="<?php echo  esc_attr($instance['format_args']);?>"><br>
			<span class="help"><?php _e('Comma separated list of keywords', 'la-banner');?></span>
        </p>
		
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('num')); ?>"><?php _e('Limit:', 'la-banner'); ?></label>
			<select class="widefat" name="<?php echo $this->get_field_name('num'); ?>" id="<?php echo $this->get_field_id('num'); ?>">
				<?php for ($i = 0; $i <= 20; $i++) : ?>
					<option <?php selected($num, $i) ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
				<?php endfor; ?>
			</select><br>
			<span class="help"><?php _e('Choose "0" to display all', 'la-banner');?></span>
		</p>
		
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('orderby')); ?>"><?php _e('Order by:', 'la-banner'); ?></label>
			<select class="widefat" name="<?php echo $this->get_field_name('orderby'); ?>" id="<?php echo $this->get_field_id('orderby'); ?>">
				<?php foreach ($orderby_options as  $key => $label): ?>
					<option <?php selected($key, $orderby) ?> value="<?php echo $key; ?>"><?php echo $label; ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('order')); ?>"><?php _e('Order:', 'la-banner'); ?></label>
			<select class="widefat" name="<?php echo $this->get_field_name('order'); ?>" id="<?php echo $this->get_field_id('order'); ?>">
				<?php foreach ($order_options as  $key => $label): ?>
					<option <?php selected($key, $order) ?> value="<?php echo $key; ?>"><?php echo $label; ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('type')); ?>"><?php _e('Type:', 'la-banner'); ?></label>
			<select class="widefat" name="<?php echo $this->get_field_name('type'); ?>" id="<?php echo $this->get_field_id('type'); ?>">
				<option <?php selected($type, '')?> value=""><?php _e('Select type', 'la-banner'); ?></option>
				<?php foreach ($terms as  $term): ?>
					<option <?php selected($term->slug, $type) ?> value="<?php echo esc_attr($term->slug); ?>"><?php echo esc_attr($term->name); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
    <?php
    }

    
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        
		$instance['title'] = sanitize_text_field($new_instance['title'], 'save');
		$instance['format'] = sanitize_title($new_instance['format']);
		$instance['format_args'] = sanitize_text_field($new_instance['format_args'], 'save');
		$instance['num'] = intval($new_instance['num']);
		$instance['orderby'] = sanitize_title($new_instance['orderby']);
		$instance['order'] = sanitize_title($new_instance['order']);
		$instance['type'] = sanitize_title($new_instance['type']);
       
		
        return $instance;
    }
}

?>