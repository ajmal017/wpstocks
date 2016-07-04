<?php

function wpstocks_promo_page()
{
  global $admin_page_formatter;
  ob_start();
?>
             <div class="col-sm-6 col-sm-offset-3">
              <p style="width:398px; line-height: 1.6em; background: white; padding: 5px;">Sign up for Replace Me Pro and get 50% off the price. Simply fill in the form below and we&rsquo;ll let you know when Replace Me Pro is available. Limited offer so sign up now!<p>
             </div>
             <div class="col-sm-6 col-sm-offset-3">
             </div>
<?php
  $content = ob_get_clean();
  $admin_page_formatter('pro', 'Get Replace Me Pro!', $content);
}

function wpstocks_pro_options()
{
  global $form_formatter;
  $form_values = array(
		       array('id'=>'wpstocks_email', 'name'=>'wpstocks_email', 'type'=>'email', 'placeholder'=>'Enter your email address', 'required'=>true, 'value'=>$wpstocks_email, 'label'=>'Email', 'help'=>''),
		       array('id'=>'wpstocks_phone', 'name'=>'wpstocks_phone', 'type'=>'tel', 'placeholder'=>'Enter your phone number', 'required'=>true, 'value'=>$wpstocks_phone, 'label'=>'Phone', 'help'=>''));
  $form_formatter($form_values);
}

function wpstocks_about()
{
  global $admin_page_formatter;
  ob_start();
?>
  <h3>Description</h3>
  <p>Description goes here</p>
<?php
  $content = ob_get_clean();
  $admin_page_formatter('about', 'About', $content);
}

function wpstocks_admin_form()
{
  global $admin_page_formatter;
  ob_start();
  wpstocks_pro_options();
  $content = ob_get_clean();
  $admin_page_formatter('admin', 'Admin', $content);
}

function wpstocks_settings_page()
{ 
  global $admin_page_formatter;
  ob_start();
?>
<?php
  $content = ob_get_clean();
  $admin_page_formatter('settings', 'Settings', $content);
}

