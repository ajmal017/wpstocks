<?php

/*
  Plugin Name: WP Stocks
  Plugin URI: 
  Description: 
  Version: 0.01
  Author: premiumwebtechnologies.com
  Author URI: http://premiumwebtechnologies.com
  Copyright 2014 premiumwebtechnologies.com

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
*/


$mem = abs(intval(@ini_get('memory_limit')));
if ($mem and $mem < 128) {
    @ini_set('memory_limit', '128M');
}

setlocale(LC_MONETARY, 'en_US');

if (version_compare(phpversion(), '5.3.10', '<')) {
    echo "You need php version 5.3.10 or greater to run WP Stocks";
} else {

    /*
      Global variables: https://codex.wordpress.org/Global_Variables
    */
    error_reporting(E_ALL);

    define('WPSTOCKS_CURRENT_VERSION', '0.01');
    define('WPSTOCKS_UPDATE_FILE_PATH', 'http://premiumwebtechnologies.com/updates/wpstocks/wpstocks.update.php');

    //date_default_timezone_set(get_option('timezone_string'));

    // $wp_rewrite = new WP_Rewrite();

    define('WPSTOCKS_USE_BOOTSTRAP', true);

    include("wpstocks.functions.index.php");
    include("wpstocks.functions.common.php");
    include("wpstocks.format.php");
    include("wpstocks.admin.pages.php");
    include("wpstocks.meta.php");
    include("wpstocks.functions.php");

    include('src/WPStocksAccounts.php');
    include('src/WPStocksUserPage.php');
    include('src/WPStocksUser.php');
    include('src/WPStocksAccount.php');
    include('src/WPStocksPortfolio.php');
    include('src/WPStocksPortfolioRecord.php');
    include('src/WPStocksStock.php');
    include('src/WPStocksTransaction.php');
    include('src/WPStocksWatchItem.php');
    include('src/WPStocksPage.php');
    include('src/WPStocks.Ajax.php');
    
    add_filter('template_include', 'wpstocks_template', 99);
    add_action("plugins_loaded", 'wpstocks_plugins_loaded');

    // http://www.sitepoint.com/wordpress-pages-use-tags/
    // Tag support for pages
    // tag hooks
    add_action('init', 'wpstocks_tags_support_all');

    add_action('pre_get_posts', 'wpstocks_tags_support_query');
    add_action("init", 'wpstocks_init');

    register_activation_hook(__FILE__, 'wpstocks_on_activation');
    register_deactivation_hook(__FILE__, 'wpstocks_deactivate');
    

}

