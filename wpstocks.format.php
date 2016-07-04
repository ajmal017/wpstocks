<?php

function wpstocks_LoginFormHTML()
{
  ob_start();
?>
          <form class="form-horizontal" id="wpstocks_registrationForm">
             <div class="form-group control-group">
                <label class="control-label" for="username">Username</label>
                <div class="controls">
                   <input class="form-control input-lg" placeholder="Enter a username" type="text" id="wpstocks_username" name="wpstocks_username" required></input>
                   <span class="help-inline"></span>
                </div>
             </div>
             <div class="form-group control-group">
                <label class="control-label">Password</label>
                <div class="controls">
                   <input class="form-control input-lg" placeholder="Enter a password" type="password" id="wpstocks_password" name="wpstocks_password" required></input>
                   <span class="help-inline"></span>
                </div>
             </div>
             <div class="form-group form-actions">
                <button type="submit" class="btn">Login</button>
             </div>
          </form>
<?php
  return ob_get_clean();
}

function wpstocks_registrationFormHTML()
{
  ob_start();
?>
          <form class="form-horizontal" id="wpstocks_registrationForm">
             <div class="form-group control-group">
                <label class="control-label" for="username">Username</label>
                <div class="controls">
                   <input class="form-control input-lg" placeholder="Enter a username" type="text" id="wpstocks_username" name="wpstocks_username" required></input>
                   <span class="help-inline"></span>
                </div>
             </div>
             <div class="form-group control-group">
                <label class="control-label">First name</label>
                <div class="controls">
                   <input class="form-control input-lg" placeholder="Enter your first name" type="text" id="wpstocks_firstname" name="wpstocks_firstname" required></input>
                   <span class="help-inline"></span>
                </div>
             </div>
             <div class="form-group control-group">
                <label class="control-label">Last name</label>
                <div class="controls">
                   <input class="form-control input-lg" placeholder="Enter your last name" type="text" id="wpstocks_lastname" name="wpstocks_lastname" required></input>
                   <span class="help-inline"></span>
                </div>
             </div>
             <div class="form-group control-group">
                <label class="control-label">Email</label>
                <div class="controls">
                   <input class="form-control input-lg" placeholder="Enter your email" type="email" id="wpstocks_email" name="wpstocks_email" required></input>
                   <span class="help-inline"></span>
                </div>
             </div>
             <div class="form-group control-group">
                <label class="control-label">Confirm email</label>
                <div class="controls">
                   <input class="form-control input-lg" placeholder="Enter your email again" type="email" id="wpstocks_confirmemail" name="wpstocks_confirmemail"></input>
                   <span class="help-inline"></span>
                </div>
             </div>
             <div class="form-group control-group">
                <label class="control-label">Password</label>
                <div class="controls">
                   <input class="form-control input-lg" placeholder="Enter a password" type="password" id="wpstocks_password" name="wpstocks_password" required></input>
                   <span class="help-inline"></span>
                </div>
             </div>
             <div class="form-group control-group">
                <label class="control-label">Confirm password</label>
                <div class="controls">
                   <input class="form-control input-lg" placeholder="Confirm your password" type="password" id="wpstocks_confirmpassword" name="wpstocks_confirmpassword"></input>
                   <span class="help-inline"></span>
                </div>
             </div>
             <div class="form-group form-actions">
                <button type="submit" class="btn">Register</button>
             </div>
          </form>
<?php
  return ob_get_clean();
}

$error_formatter = function($error){
  ?>
  <div class="row">
       <div class="col-sm-12">
            <h3>Error</h3>
            <p><?php echo $error; ?></p>
       </div>
  </div>
<?php
};

$form_formatter = function($values)
{
  foreach ($values as $value) {
    ?>
    <div class="row">
    <div class="col-sm-3">
    <label class="control-label" for="<?php echo $value['id']; ?>"><?php echo $value['label']; ?></label>
    </div>
    <div class="col-sm-9 controls">
    <?php
    switch($value['type']) {
    case 'textarea':
    ?>
    <textarea class="form-control" name="<?php echo $value['name']; ?>" id="<?php echo $value['id']; ?>" placeholder="<?php echo $value['placeholder']; ?>" <?php echo isset($value['required']) && $value['required']?"required":""; ?>><?php echo $value['value']; ?></textarea>
    <?php
    break;
    default:
    ?>
    <input class="form-control" type="<?php echo $value['type']; ?>" name="<?php echo $value['name']; ?>" id="<?php echo $value['id']; ?>" placeholder="<?php echo $value['placeholder']; ?>" <?php echo isset($value['required']) && $value['required']?"required":""; ?> />
    <?php
    }
    ?>
    <p class="wpstocks_help"><?php echo $value['help']; ?></p>
    </div>
    </div>
    <?php
  }
};


$meta_box_formatter = function($wpstocks_email, $wpstocks_phone)
{
  global $form_formatter;
  $form_values = array(
		       array('id'=>'wpstocks_email', 'name'=>'wpstocks_email', 'type'=>'email', 'placeholder'=>'Enter your email address', 'required'=>true, 'value'=>$wpstocks_email, 'label'=>'Email', 'help'=>''),
		       array('id'=>'wpstocks_phone', 'name'=>'wpstocks_phone', 'type'=>'tel', 'placeholder'=>'Enter your phone number', 'required'=>true, 'value'=>$wpstocks_phone, 'label'=>'Phone', 'help'=>''));
  $form_formatter($form_values);
};


$admin_page_formatter = function($page_name, $title, $content)
{
  $wpstocks = get_option('wpstocks', array());
  if (!isset($wpstocks['visited pages']) || in_array($page_name, $wpstocks['visited pages'])) {
    $wpstocks['visited pages'][] = 'about';
    update_option('wpstocks', $wpstocks);
  }

  if (function_exists( 'wp_enqueue_media' )) {
    wp_enqueue_media();
  }else{
    wp_enqueue_style('thickbox');
    wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
  }

  ?>
  <div class="wrap thumbnail">
  <header class="pwt_header">
  <div class="container-fluid">
  <div class="col-xs-1"></div>
  <div class="col-xs-11"><h2><?php echo $title; ?></h2></div>
  </div>
  </header>
  <div class="container-fluid">
  <div class="row">
  <div class="col-sm-12">
  <?php echo $content; ?>
  </div><!-- col -->
  </div><!-- row -->
  </div>
  </div>
  <?php
};

