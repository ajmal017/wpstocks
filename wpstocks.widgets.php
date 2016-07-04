<?php

function wpstocks_register_widgets()
{
  register_widget("wpstocks_Widget");
}


class wpstocks_Widget extends WP_Widget{

  function wpstocks_Widget()
  {
    //processes the widget
    $widget_ops = array(
			'classname'=>'wpstocks_widget_class',
			'description'=>'');
    $this->WP_Widget("wpstocks_Widget", "wpstocks Widget", $widget_ops);
  }

  function form($instance)
  {
    // displays the widget form in the admin dashboard
    $defaults = array('title'=>"wpstocks title");
    $instance = wp_parse_args( (array) $instance, $defaults);
    $title = $instance["title"];
    ?>
    <p>Title: <input class="widefat" name="<?php echo $this->get_field_name("title"); ?>" type="text" value="<?php echo esc_attr($title); ?>"/></p>
       <?php
       }

  function update($new_instance, $old_instance)
  {
    // process widget options to save
    $instance = $old_instance;
    $instance["title"] = strip_tags($new_instance["title"]);
    return $instance;
  }

  function widget($args, $instance)
  {
    //displays the widget
    extract($args);
    echo $before_widget;
    $title = apply_filters( 'widge_title', $instance["title"]);
    if (!empty($title)) {
      echo $before_title . $title . $after_title;
    }
    echo $after_widget;
  }

}

