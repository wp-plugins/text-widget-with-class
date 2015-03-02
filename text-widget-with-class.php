<?php
/**
 * Plugin Name: Text Widget with Class
 * Description: A text widget that allows you to add a custom classes to widget container and header.
 * Version: 1.2
 * Author: Chris LaFrombois
 * Author URI: http://www.orbitmedia.com
 */

/* 
 *	This is a modified version of the default text widget. It is entirely free to use, 
 *	distribute and modify as you see fit. Good times.
 *
*/

add_action( 'widgets_init', 'register_text_widget_with_class' );


function register_text_widget_with_class() {
    register_widget( 'Text_Widget_With_Class' );
}

class Text_Widget_With_Class extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_advanced_text', 'description' => __('This plugin allows you to add a class to the widget title tag.'));
		$control_ops = array('width' => 400, 'height' => 350);
		parent::__construct('advanced_text', __('Text Widget with Class'), $widget_ops, $control_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$div_classes = apply_filters('widget_div_classes', empty($instance['div_classes']) ? '' : $instance['div_classes'], $instance );
		$classes = apply_filters('widget_classes', empty($instance['classes']) ? '' : $instance['classes'], $instance );
		$text = apply_filters( 'widget_text', empty( $instance['text'] ) ? '' : $instance['text'], $instance );
		
		/* VERSION 1.2
		** Adds a strpos to find if the theme widget registration
		** contains a `before` string. If not, the this will add
		** the entire title class plus custom classes.
		*/
		
		// Looks in the sidebar to find the $before_title parameter
		// And splits it at the end quote and bracket
		
		$b_title = '';
		$b_title_e = '';
		
		if($before_title !== '') {
			$b_title = explode('">', $before_title);
			$b_title_e = $b_title['0'] . ' twwc-widget-title '; 
		} else {
			$b_title_e = '<h2 class="widget-title twwc-widget-title ';
		}
		
		if($after_title === '') {
			$after_title = '</h2>';
		}
		
		echo $before_widget;
		if ( !empty( $title ) ) { echo $b_title_e . $classes . '">' . $title . $after_title; } ?>
			<div class="textwidget <?php echo $div_classes;?>"><?php echo !empty( $instance['filter'] ) ? wpautop( $text ) : $text; ?></div>
		<?php
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['div_classes'] = strip_tags($new_instance['div_classes']);
		$instance['classes'] = $new_instance['classes'];
		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
			$instance['filter'] = isset($new_instance['filter']);
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '', 'div_classes'=>'', 'classes'=>'' ) );
		$title = strip_tags($instance['title']);
		$text = esc_textarea($instance['text']);
		$div_classes = strip_tags($instance['div_classes']);
		$classes = strip_tags($instance['classes']);
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		
        <p><label for="<?php echo $this->get_field_id('div_classes');?>"><?php _e('Add Classes for the widget container. Separate multiple classes by a space (no commas!)'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('div_classes');?>" name="<?php echo $this->get_field_name('div_classes');?>" type="text" value="<?php echo $div_classes;?>" /></p>
        
        <p><label for="<?php echo $this->get_field_id('classes');?>"><?php _e('Add Classes. Separate multiple classes by a space (no commas!)'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('classes');?>" name="<?php echo $this->get_field_name('classes');?>" type="text" value="<?php echo $classes;?>" /></p>
        
		<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>

		<p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs'); ?></label></p>
<?php
	}
}
?>