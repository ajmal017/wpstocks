<?php

namespace premiumwebtechnologies\wpstocks; // use vendorname\subnamespace\classname;

class WPStocksAccounts
{
    /**
     * WPStocksAccount constructor.
     */
    public function __construct()
    {
        if ( isset($_POST['wpstocks_noncename']) && isset( $_POST['funds'] ) ) {
            $id = $_POST['post_ID'];
            if ( ! add_post_meta( $id, 'user_id', $_POST['user_id']*1, true ) ) {
                update_post_meta( $id, 'user_id', $_POST['user_id']*1 );
            }
            if ( ! add_post_meta( $id, 'funds', $_POST['funds']*1, true ) ) {
                update_post_meta( $id, 'funds', $_POST['funds']*1 );
            }
            if ( ! add_post_meta( $id, 'account_status', $_POST['account_status'], true ) ) {
                update_post_meta( $id, 'account_status', $_POST['account_status'] );
            }
            if ( ! add_post_meta( $id, 'account_type', $_POST['account_type'], true ) ) {
                update_post_meta( $id, 'account_type', $_POST['account_type'] );
            }
        }
    }

    public function add_accounts_page_type()
    {
        $args = array(
            'public' => true,
            'query_var' => 'wpstocks_accounts_page',
            'supports' => array(
                'title',
                'editor'
            ),
            'labels' => array(
                'all_items' => 'All WP Stocks Accounts',
                'name' => 'WP Stocks Accounts',
                'singular_name' => 'WP Stocks Account',
                'add_new' => 'Add New WP Stocks Account',
                'add_new_item' => 'Add New WP Stocks Account',
                'edit_item' => 'Edit WP Stocks Account',
                'new_item' => 'New WP Stocks Account',
                'view_item' => 'View WP Stocks Account',
                'search_items' => 'Search WP Stocks Accounts',
                'not_found' => 'No WP Stocks Accounts found',
                'not_found_in_trash' => 'No WP Stocks Accounts found in trash'),
            'has_archive' => true,
            'hierachical' => true,
            'feeds' => true);
        $result = register_post_type("wpstocks_accspage", $args);
        if ( is_wp_error( $result )) {
            die( $result->get_error_message() );
        }

        return true;

    }

    public function add_new_account( $account_name )
    {
        $args = array(
            'post_title'=>$account_name,
            'post_status'=>'publish',
            'comment_status'=>'closed',
            'post_type'=>'wpstocks_accspage'
        );
        $id = wp_insert_post( $args, true );
        if ( is_wp_error( $id ) ) {
            die( $id->get_error_message() );
        }
        return $id;
   
    }

    public function add_meta_form() 
    {
        add_meta_box(
            'wpstocks_account_meta_form',      // Unique ID
            'Meta data',    // Title
            function(){
                global $post;
                $funds = get_post_meta( $post->ID, 'funds', true );
                $status = get_post_meta( $post->ID, 'account_status', true );
                $type = get_post_meta( $post->ID, 'account_type', true );
                $userId = get_post_meta( $post->ID, 'user_id', true);
                $users = get_users( array('role'=>'wpstocks') );
                ob_start();
                wp_nonce_field( plugin_basename(__FILE__), 'wpstocks_noncename' );
                ?>
                <ul>
                    <li>
                        <label>User</label>
                        <div>
                            <select name="user_id">
                                <option value="">None selected</option>
                            <?php
                                foreach ( $users as $user ) {
                                    ?>
                                    <option <?php echo $userId == $user->ID ? 'selected' : ''; ?> value='<?php echo $user->ID;?>'><?php echo $user->display_name; ?></option>
                                    <?php
                                }
                            ?>
                            </select>
                            <?php
                            if (!empty($userId)) {
                                ?>
                                <a href="<?php echo site_url(); ?>/wp-admin/user-edit.php?user_id=<?php echo $userId; ?>">Edit user</a>
                                <?php
                            }
                            ?>
                        </div>
                    </li>
                    <li>
                        <label>Funds</label>
                        <div>
                            <input name='funds' value="<?php echo $funds; ?>" />
                        </div>
                    </li>
                    <li>
                        <label>Account Type</label>
                        <div>
                            <select name='account_type'>
                                <option value='individual' <?php echo $type=='individual'?'selected':'';?>>Individual</option>
                                <option value='joint' <?php echo $type=='joint'?'selected':'';?>>Joint</option>
                                <option value='trust' <?php echo $type=='trust'?'selected':'';?>>Trust</option>
                                <option value='corporate' <?php echo $type=='corporate'?'selected':'';?>>Corporate</option>
                            </select>
                        </div>
                    </li>
                    <li>
                        <label>Status</label>
                        <div>
                            <select name='account_status'>
                                <option value='active' <?php echo $status=='active'?'selected':'';?>>Active</option>
                                <option value='inactive' <?php echo $status=='inactive'?'selected':'';?>>Inactive</option>
                            </select>
                        </div>
                    </li>
                </ul>
<?php
            },
            'wpstocks_accspage',         // Admin page (or post type)
            'side',         // Context
            'default'         // Priority
        );

        return true;

    }
}

