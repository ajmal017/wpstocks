<?php

global $post;

switch (strtolower($post->post_title)) {
    case 'login':
        $page = "login";
        break;
    case 'home':
        $page = "home";
        break;
    case 'registration':
        $page = "registration";
        break;
    case 'control panel':
        $page = "controlPanel";
        break;
}

?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title></title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="<?php echo plugin_dir_url(__FILE__); ?>js/gsdom.js"></script>
    <script src="<?php echo plugin_dir_url(__FILE__); ?>js/wpstocksclient.js"></script>


    <!-- Add fancyBox -->
    <link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__); ?>js/vendor/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
    <script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__); ?>js/vendor/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>

    <!-- Optionally add helpers - button, thumbnail and/or media -->
    <link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__); ?>js/vendor/fancybox/source/helpers/jquery.fancybox-buttons.css?v=1.0.5" type="text/css" media="screen" />
    <script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__); ?>js/vendor/fancybox/source/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>
    <script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__); ?>js/vendor/fancybox/source/helpers/jquery.fancybox-media.js?v=1.0.6"></script>

    <link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__); ?>js/vendor/fancybox/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7" type="text/css" media="screen" />

    <link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__); ?>styles/app.css" type="text/css" media="screen" />

    <script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__); ?>js/vendor/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>

    <style>
        body{
            margin: 10px;
        }
    </style>
    <script type="text/javascript">
        /* <![CDATA[ */
        <?php
        $wpstocks_URLs = get_option('wpstocks_URLs');
        include(ABSPATH . "wp-includes/pluggable.php");
        $userID = '';
        /*
    if ( is_user_logged_in() ) {
      $current_user = wp_get_current_user();
      if (is_object($current_user)) {
        $userID = $current_user->ID;
      }
    }
        */
        $current_user = wpstocks_get_user( false );
        if (is_object($current_user)) {
            $userID = $current_user->ID;
        }
        ?>
        var wpstocks_js_parameters = {"username":"<?php echo $userID; ?>","plugin_url":"<?php echo plugin_dir_url(__FILE__); ?>", "ajax_url":"<?php echo admin_url().'admin-ajax.php';?>", "loginPage":"<?php echo $wpstocks_URLs['login']; ?>"};
        /* ]]> */
    </script>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-10 col-sm-offset-1">
            <?php
            if ( is_object( $current_user ) && ! empty( $current_user->get_logo() ) ) {
                ?>
                <img class="wpstocks_logo_media_image pull-right" src="<?php echo esc_attr( $current_user->get_logo() ) ; ?>" />
                <?php
            }
            ?>
        </div>
        <div class="col-sm-10 col-sm-offset-1">
            <?php echo $post->post_content; ?>
        </div>
        <div class="col-sm-10 col-sm-offset-1">
            <div class="row">
                <div class="col-md-12  wpstocks_footer">
                    <div>
                        <?php
                        $footer_page = new \premiumwebtechnologies\wpstocks\WPStocksPage( 'footer' );
                        // todo move to function
                        echo str_replace( array( '%officephone%', '%companyname%' ),  array( $current_user->get_office_phone(), $current_user->get_company() ), $footer_page->get_page_content() );
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>