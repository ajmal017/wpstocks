<?php

namespace premiumwebtechnologies\wpstocks; // use vendorname\subnamespace\classname;

/**
 * Created by PhpStorm.
 * User: kevindavies
 * Date: 1/07/16
 * Time: 9:30 PM
 */



// Configure this file by going to Preferences -> Editor -> File and Code Templates
// To set coding standard go to Preferences -> Editor -> Inspections -> PHP -> PHP Code Sniffer validation
// [in coding standards drop down select desired coding standard]
// To set PHP version to use click on External Libraries on left pane and select Configure PHP include paths.
// To verify this file go to Code -> Inspect code
// To view error log go to /Applications/MAMP/logs
// To view error log go to /Applications/MAMP/logs
// To install a composer package right click on project name, click on composer and select init. Once done select
// composer again and select add dependency.
// To add a bookmark click fn+F3
// To show bookmarks click fn+cmd+F3
// To go to a bookmark click ctl+[0=9]
// To format cmd+alt+L
// To duplicate a line(s): cmd+d

// To generate constructors etc go to Code -> Generate (cmd+N)
// To extract code into a new method click ctl+alt+M.

class WPStocksPage
{

    private $page_content;
    private $type; // control, footer

    /**
     * WPStocksPage constructor.
     */
    public function __construct( $type )
    {
        $this->type = $type;
        $this->set_page_content();
    }

    /**
     * @return mixed
     */
    public function set_page_content()
    {
        $pages = get_posts( array( 'post_type'=>'wpstocks_page') );
        try {
            if (!$pages) {
                throw new \Exception('No WPStocks pages found');
            }
            $pages_parsed = array();
            foreach ($pages as $page) {
                $pages_parsed[strtolower($page->post_title)] = $page;
            }
            $post_content = '';
            switch ($this->type) {
                case 'control page':
                    $page_content = isset($pages_parsed['control page']) ? $pages_parsed['control page']->post_content : '';
                    break;
                case 'footer':
                    $page_content = isset($pages_parsed['footer']) ? $pages_parsed['footer']->post_content : '';
                    break;
                default:
                    $page_content = 'Not found';
            }
        }
        catch( \Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        $this->page_content = $page_content;
    }

    /**
     * @return mixed
     */
    public function get_page_content()
    {
        return $this->page_content;
    }




}