<?php

function wpstocks_setWPStocksURLs()
{

    delete_option('wpstocks_URLs');

    // Map each wp posts page
    $wpposts = get_posts(array('post_type' => 'wpstocks_page'));

    $loginPage = null;
    $registrationPage = null;
    $controlPanelPage = null;

    foreach ($wpposts as $wppost) {
        switch (strtolower($wppost->post_title)) {
            case 'login':
                $page = "login";
                $loginPage = $wppost->guid;
                break;
            case 'registration':
                $page = "registration";
                $registrationPage = $wppost->guid;
                break;
            case 'control panel':
                $page = "controlPanel";
                $controlPanelPage = $wppost->guid;
                break;
        }
    }

    update_option('wpstocks_URLs', array('login' => $loginPage, 'registration' => $registrationPage, 'control panel' => $controlPanelPage));

}
function wpstocks_getBalance( $account )
{
    return $account->get_balance( null );
}

function wpstocks_get_user( $mustBeAdmin ) {

    if ( $mustBeAdmin && !current_user_can( 'manage_options' ) ) {
        echo "Must be administrator to do this";
        http_response_code( 403 );
        die();
    }

    // Get the user we are acting on behalf of
    @session_start();
    if ( isset( $_SESSION['wpstocks_user'] ) ) {
        $wpstocks_user = $_SESSION['wpstocks_user'];
        if ( 'WPStocksUser' != get_class( $wpstocks_user ) ) {
            $wpstocks_user = new \premiumwebtechnologies\wpstocks\WPStocksUser( $wpstocks_user->ID );
        }
    } else {
        $current_user = wp_get_current_user(); // user with admin privileges - user acting on behalf of himself
        $wpstocks_user = new \premiumwebtechnologies\wpstocks\WPStocksUser( $current_user->ID );
    }

    return $wpstocks_user;
}

function wpstocks_insert_user ( $password, $username, $firstname, $lastname, $email )
{

    $userdata = array ( 
        'user_pass' => $password,
        'user_login' => $username,
        'user_nicename' => $username,
        'user_url' => '',
        'user_email' => $email,
        'display_name' => $username,
        'nickname' => $username,
        'first_name' => $firstname,
        'last_name' => $lastname,
        'description' => 'unconfirmed',
        'rich_editing' => false,
        'role' => 'wpstocks',
        'comment_shortcuts' => false
     );

    if  ( !username_exists ( $userdata['user_login'] ) ) {
        $user = wp_insert_user ( $userdata );
        if  ( is_wp_error ( $user ) ) {
            http_response_code ( 500 );
        } else {
            $confirmationUrl = site_url (  ) . '?confirm=' . md5 ( $username );
            echo "Sending email to $email";
            $message = "Hi $firstname,\nPlease visit $confirmationUrl to complete your registration.";
            if  ( mail ( $email, 'Registration', $message ) ) {
                echo "mail sent";
            } else {
                echo "mail not sent";
            }
            http_response_code ( 201 );
        }
    } else {
        echo "User exists";
        http_response_code ( 409 );
    }
}

function parsePortfolio( $portfolio ) {

    $portfolio_records = $portfolio->get_portfolio_records();
    $parsed_portfolio_records = array();
    foreach ( $portfolio_records as $portfolio_record) {
        $parsed_portfolio_records[] = parsePortfolioRecord( $portfolio_record );
    }
    return array(
        'user_id' => $portfolio->get_user_id(),
        'records' => $parsed_portfolio_records
    );
}

function parsePortfolioRecord( $portfolio_record ) {
    return array(
        'stock_price' => ltrim( $portfolio_record->get_stock_price(), '$' ), // todo change so that it also removes pound signs etc
        'name' => $portfolio_record->get_name(),
        'type' => $portfolio_record->get_type(),
        'stock_name' => $portfolio_record->get_stock_name(),
        'stock_quantity' => $portfolio_record->get_stock_quantity(),
        'stock_symbol' => $portfolio_record->get_stock_name(),
        'amount' => $portfolio_record->get_amount(),
        'accountNumber' => $portfolio_record->get_account_number(),
        'currency' => $portfolio_record->get_currency(),
        'accountName' => $portfolio_record->get_account_name()
    );
}

function parseTransaction( $transaction, $balance ) {
    $stock = $transaction->get_stock();
    $debit =  trim( money_format( "%n", 0 ) , '$' );
    $credit = trim( money_format( "%n", 0 ) , '$' );
    if ( 'debit' == $transaction->get_type() ) {
        $debit = $transaction->get_amount();
    } else if ( 'credit' == $transaction->get_type())  {
        $credit = $transaction->get_amount();
    }
    return  array(
        'status'=>$transaction->get_status(),
        'ttnumber'=>$transaction->get_ttnumber(),
        'reference'=>$transaction->get_reference(),
        'date'=>$transaction->get_date(),
        'stock_quantity'=>$transaction->get_stock_quantity(),
        'currency'=>$transaction->get_currency(),
        'stock_name'=>empty( $stock) ? '' : $stock->get_name(),
        'stock_symbol'=>empty( $stock ) ? '' : $stock->get_symbol(),
        'stock_price'=>$transaction->get_stock_price(),
        'name'=>$transaction->get_name(),
        'amount'=>$transaction->get_amount(),
        'debit'=>$debit,
        'credit'=>$credit,
        'balance'=>$balance,
        'type'=>$transaction->get_type() );
}

function wpstocks_shortcode( $atts )
{
    //  $atts = shortcode_atts( array('attr'=>'value'), $atts, 'SC' );
    ob_start();
    ?>
    <?php
    return ob_get_clean();
}

// add tag support to pages
function wpstocks_tags_support_all()
{
    register_taxonomy_for_object_type( 'post_tag', 'page' );
}

// ensure all tags are included in queries
function wpstocks_tags_support_query( $wp_query )
{
    if ($wp_query->get( 'tag' ) ) $wp_query->set( 'post_type', 'any' );
}

function wpstocks_on_activation()
{
    wpstocks_setupDatabase();
    remove_role( 'wpstocks' );
    add_role( "wpstocks", "WP Stocks", array( 'read' ) );
    wpstocks_createPages();
}

function wpstocks_createPages()
{
    wpstocks_createRegistrationPage();
}

function wpstocks_createRegistrationPage()
{
    $content = wpstocks_registrationFormHTML();
    wp_insert_post(array('post_title' => 'Registration', 'post_content' => $content, 'post_type' => 'wpstocks_page', 'post_status' => 'publish'));
}

function wpstocks_setupDatabase()
{
    global $wpdb;
    $sql = $wpdb->prepare("CREATE TABLE IF NOT EXISTS `wpstocks_stocks` (
  `symbol` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;", "");
    $wpdb->query($sql);

    $sql = $wpdb->prepare("ALTER TABLE `wpstocks_stocks`
 ADD PRIMARY KEY (`symbol`);", "");
    $wpdb->query($sql);

    $sql = $wpdb->prepare("CREATE TABLE IF NOT EXISTS `wpstocks_stock_info` (
  `symbol` varchar(20) NOT NULL,
  `lastPrice` float NOT NULL,
  `date` datetime NOT NULL,
  `netChange` float NOT NULL,
  `percentChange` float NOT NULL,
  `open` float NOT NULL,
  `close` float DEFAULT NULL,
  `low` float NOT NULL,
  `high` float NOT NULL,
  `volume` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;", "");
    $wpdb->query($sql);

    $sql = $wpdb->prepare("ALTER TABLE `wpstocks_stock_info`
 ADD PRIMARY KEY (`symbol`,`date`);", "");
    $wpdb->query($sql);

    $sql = $wpdb->prepare("CREATE TABLE IF NOT EXISTS `wpstocks_watched` (
  `symbol` varchar(20) NOT NULL,
  `username` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;", "");
    $wpdb->query($sql);

    $sql = $wpdb->prepare("ALTER TABLE `wpstocks_watched`
 ADD PRIMARY KEY (`symbol`,`username`);", "");
    $wpdb->query($sql);

    $sql = $wpdb->prepare("CREATE TABLE IF NOT EXISTS `wpstocks_trades` (
  `symbol` varchar(20) NOT NULL,
  `buy` float NOT NULL,
  `date` datetime NOT NULL,
  `username` varchar(50) NOT NULL,
  `numberOfShares` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;", "");
    $wpdb->query($sql);

    $sql = $wpdb->prepare("ALTER TABLE `wpstocks_trades`
 ADD PRIMARY KEY (`symbol`,`buy`,`date`,`username`);", "");
    $wpdb->query($sql);

}

function wpstocks_deactivate(  )
{
    $posts = get_posts(  array( 'post_type' => 'wpstocks_page' ) );
    foreach ( $posts as $post ) {
        wp_delete_post( $post->ID, true );
    }
}


function wpstocks_add_page_type()
{
    $wpstocks_page_args = array(
        'public' => true,
        'query_var' => 'wpstocks_page',
        'supports' => array(
            'title',
            'editor'
        ),
        'labels' => array(
            'all_items' => 'All WP Stocks pages',
            'name' => 'WP Stocks',
            'singular_name' => 'WP Stocks',
            'add_new' => 'Add New WP Stocks page',
            'add_new_item' => 'Add New WP Stocks page',
            'edit_item' => 'Edit WP Stocks page ',
            'new_item' => 'New WP Stocks page ',
            'view_item' => 'View WP Stocks page ',
            'search_items' => 'Search WP Stocks pages',
            'not_found' => 'No WP Stocks pages found',
            'not_found_in_trash' => 'No WP Stocks pages found in trash'),
        'has_archive' => true,
        'hierachical' => true,
        'feeds' => true);
    register_post_type("wpstocks_page", $wpstocks_page_args);

}


