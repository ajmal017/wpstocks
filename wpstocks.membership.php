<?php
// http://codex.wordpress.org/Roles_and_Capabilities

function wpstocks_membership()
{
 
  if (is_user_logged_in()) {
    //    print_r(get_users(array()));
    //    print_r(get_users_of_blog());
    //    wpstocks_insert_user(array(), 'http://mycontest.example.com');
    //    wp_create_user('me2', 'pass', 'me2@example.com');
    //    wpstocks_update_user();
    //    print_r(wpstocks_get_userdata(2));
    //    print_r(wp_get_current_user());
    //    $number_posts = count_user_posts(2); // count_many_users_posts()
  }
}

function wpstocks_get_userdata($id)
{
  return get_userdata($id);
}

function wpstocks_update_user($userdata)
{
  
  $userdata = array(
		    'ID'=>1000,
		    'user_pass'=>'pass',
		    'user_login'=>'me3',
		    'user_nicename'=>'me3',
		    'user_url'=>'http://me.example.com',
		    'user_email'=>'me@example.com',
		    'display_name'=>'me3',
		    'nickname'=>'me3',
		    'first_name'=>'fme',
		    'last_name'=>'lme',
		    'description'=>'',
		    'rich_editing'=>true,
		    'role'=>'subscriber',
		    'comment_shortcuts'=>false
		    );
  wp_update_user($userdata);

}

function wpstocks_set_contest_user($user_id)
{
  $userdata = get_userdata($user_id);
  wpstocks_register_with_autoresponder($user_id, $userdata);
}

function wpstocks_register_with_autoresponder($user_id, $userdata)
{
  echo "Called wpstocks_register_with_autoresponder()";
}

function wpstocks_insert_user($userdata, $contest_url)
{
  
  $userdata = array(
		    'user_pass'=>'pass',
		    'user_login'=>'me3',
		    'user_nicename'=>'me3',
		    'user_url'=>'http://me.example.com',
		    'user_email'=>'me3@example.com',
		    'display_name'=>'me3',
		    'nickname'=>'me3',
		    'first_name'=>'fme3',
		    'last_name'=>'lme3',
		    'description'=>'',
		    'rich_editing'=>true,
		    'role'=>'subscriber',
		    'comment_shortcuts'=>false
		    );
  if (!username_exists($userdata['user_login'])) {
    $user_id = wp_insert_user($userdata);
    if (is_wp_error($user)) {
      echo $result->get_error_message();
    }
    else{
     add_user_meta($user_id, 'wpstocks_contest_url', $contest_url, true);
     wpstocks_set_contest_user($user_id);
    }
  }

}

function wpstocks_add_contact_methods($user_contactmethods)
{
  $user_contactmethods['twitter'] = 'Twitter Username';
  $user_contactmethods['phone'] = 'Phone Number';
  return $user_contactmethods;
}

function wpstocks_show_user_profile($user)
{
  $contest_url = get_user_meta($user->ID, 'wpstocks_contest_url', true);
  ?>
  <h3>Contest data</h3>
  <table class='form-table'>
     <tr>
        <th>
          <label for="contest_url">Contest url</label>
        </th>
	<td><input type="text" class="regular-text" name="wpstocks_contest_url" id="wpstocks_contest_url" value="<?php echo esc_html($contest_url); ?>"></td>
     </tr>
  </table>
  </h3>
<?php
}

function wpstocks_update_user_profile($user_id)
{
  if (!current_user_can('edit_user', $user_id)) {
    return false;
  }
  update_user_meta($user_id, 'wpstocks_contest_url', $_POST['wpstocks_contest_url'], true);
}

