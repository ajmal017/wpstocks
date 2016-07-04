<?php

function wpstocks_addmeta_callback($post)
{
  // wpstocks.format.php
  global $meta_box_formatter;
  $wpstocks_email = get_post_meta($post->ID, 'wpstocks_email', true);                               
  $wpstocks_phone = get_post_meta($post->ID, 'wpstocks_phone', true);                               
  $meta_box_formatter($wpstocks_email, $wpstocks_phone);
} 

function wpstocks_add_meta_box()
{
  add_meta_box('wpstocks_meta', 'Email and phone', 'wpstocks_addmeta_callback', 'page', 'normal', 'high');                              
}  

function wpstocks_save_meta($post_id)
{
  if (isset($_POST['wpstocks_email'])) {
    update_post_meta($post_id, 'wpstocks_email', $_POST['wpstocks_email']);
    update_post_meta($post_id, 'wpstocks_phone', $_POST['wpstocks_phone']);
  }
}

