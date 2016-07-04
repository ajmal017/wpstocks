<?php

namespace premiumwebtechnologies\wpstocks; // use vendorname\subnamespace\classname;

    /**
     * Created by PhpStorm.
     * User: kevindavies
     * Date: 22/06/16
     * Time: 7:58 PM
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

class WPStocksUserPage
{
    private $user;

    /**
     * WPStocksUserPage constructor.
     * @param $user
     */
    public function __construct( $user )
    {
        $this->user = $user;
    }

    public function renderFields() : bool
    {
        ?>
        <h3>Additional</h3>
        <table class="form-table">
            <tr>
                <th><label for="company">Company Name</label></th>
                <td>
                    <input type="text" class="regular-text" name="company"
                           value="<?php echo is_object( $this->user ) ? esc_attr(get_the_author_meta('company', $this->user->ID)) : ''; ?>"
                           id="company"/><br/>
                    <span class="description"></span>
                </td>
            </tr>

            <tr>
                <th><label for="logo">Company logo</label></th>
                <td>
                    <?php
                    // hat tip: http://stackoverflow.com/questions/13847714/wordpress-3-5-custom-media-upload-for-your-theme-options
                   // Don't forget to use wp_enqueue_media (new in 3.5) if you'r not on the post edit page
                    wp_enqueue_media();
                    if ( is_object( $this->user ) && ! empty( get_the_author_meta( 'logo', $this->user->ID ) ) ) {
                        ?>
                        <img width="200px" class="wpstocks_logo_media_image" src="<?php echo esc_attr( get_the_author_meta( 'logo', $this->user->ID)); ?>" />
                        <?php
                    }
                    ?>
                    <br/>
                    <input type="button" class="wpstocks_upload_logo button button-secondary"  value="Upload logo"/>
                    <input class="wpstocks_logo_media_url" type="hidden" name="wpstocks_logo_url" value="">

                    <span class="description"></span>
                </td>
            </tr>
            
            <tr>
                <th><label for="secondaryemail">Secondary Email</label></th>
                <td>
                    <input type="email" class="regular-text" name="secondaryemail"
                           value="<?php echo is_object( $this->user ) ? esc_attr(get_the_author_meta('secondaryemail', $this->user->ID)) : ''; ?>"
                           id="secondaryemail"/><br/>
                    <span class="description"></span>
                </td>
            </tr>
            <tr>
                <th><label for="fax">Fax</label></th>
                <td>
                    <input type="tel" class="regular-text" name="fax"
                           value="<?php echo is_object( $this->user ) ? esc_attr(get_the_author_meta('fax', $this->user->ID ) ) : ''; ?>"
                           id="wpstocks_fax"/><br/>
                    <span class="description"></span>
                </td>
            </tr>
            <tr>
                <th><label for="officephone">Office Phone</label></th>
                <td>
                    <input type="tel" class="regular-text" name="officephone"
                           value="<?php echo is_object( $this->user ) ? esc_attr(get_the_author_meta('officephone', $this->user->ID)) : ''; ?>"
                           id="wpstocks_officephone"/><br/>
                    <span class="description"></span>
                </td>
            </tr>
            <tr>
                <th><label for="mobile">Mobile</label></th>
                <td>
                    <input type="tel" class="regular-text" name="mobile"
                           value="<?php echo is_object( $this->user ) ? esc_attr(get_the_author_meta('mobile', $this->user->ID)) : ''; ?>"
                           id="wpstocks_mob"/><br/>
                    <span class="description"></span>
                </td>
            </tr>
            <tr>
                <th><label for="homephone">Home Phone</label></th>
                <td>
                    <input type="tel" class="regular-text" name="homephone"
                           value="<?php echo is_object( $this->user ) ? esc_attr(get_the_author_meta('homephone', $this->user->ID)) : ''; ?>"
                           id="wpstocks_home"/><br/>
                    <span class="description"></span>
                </td>
            </tr>
            <tr>
                <th><label for="address">Address line 1</label></th>
                <td>
                    <textarea rows="5" cols="45" style="width: 350px" class="regular-text" name="address1"
                           id="wpstocks_address1"><?php echo is_object( $this->user ) ? esc_attr(get_the_author_meta('address1', $this->user->ID)) : ''; ?></textarea><br/>
                    <span class="description"></span>
                </td>
            </tr>
            <tr>
                <th><label for="city">Address line 2</label></th>
                <td>
                    <input type="input" class="regular-text" name="address2"
                           value="<?php echo is_object( $this->user ) ? esc_attr(get_the_author_meta('address2', $this->user->ID)) : ''; ?>"
                           id="wpstocks_address2"/><br/>
                    <span class="description"></span>
                </td>
            </tr>
            <tr>
                <th><label for="city">Address line 3</label></th>
                <td>
                    <input type="input" class="regular-text" name="address3"
                           value="<?php echo is_object( $this->user ) ? esc_attr(get_the_author_meta('address3', $this->user->ID)) : ''; ?>"
                           id="wpstocks_address3"/><br/>
                    <span class="description"></span>
                </td>
            </tr>
            <tr>
                <th><label for="city">Address line 4</label></th>
                <td>
                    <input type="input" class="regular-text" name="address4"
                           value="<?php echo is_object( $this->user ) ? esc_attr(get_the_author_meta('address4', $this->user->ID)) : ''; ?>"
                           id="wpstocks_address4"/><br/>
                    <span class="description"></span>
                </td>
            </tr>
            <tr>
                <th><label for="city">City</label></th>
                <td>
                    <input type="input" class="regular-text" name="city"
                           value="<?php echo is_object( $this->user ) ? esc_attr(get_the_author_meta('city', $this->user->ID)) : ''; ?>"
                           id="wpstocks_city"/><br/>
                    <span class="description"></span>
                </td>
            </tr>
            <tr>
                <th><label for="country">Country</label></th>
                <td>
                    <input type="input" class="regular-text" name="country"
                           value="<?php echo is_object( $this->user ) ? esc_attr(get_the_author_meta('country', $this->user->ID)) : ''; ?>"
                           id="wpstocks_country"/><br/>
                    <span class="description"></span>
                </td>
            </tr>
        </table>
        <?php
        if  (is_object( $this->user ) ) {
            ?>
            <a href="<?php echo site_url(); ?>?wpstocks_page=control-panel&userId=<?php echo $this->user->ID; ?>">Go to
                Control Panel</a>
            <?php
        }
        return true;
    }

    public function saveFields() : bool
    {
        
        if (!current_user_can('manage_options')) {
            return false;
        }

        update_user_meta($this->user->id, 'company', $_POST['company']);
        update_user_meta($this->user->id, 'country', $_POST['country']);
        update_user_meta($this->user->id, 'city', $_POST['city']);
        update_user_meta($this->user->id, 'tel', $_POST['tel']);
        update_user_meta($this->user->id, 'fax', $_POST['fax']);
        update_user_meta($this->user->id, 'address1', $_POST['address1']);
        update_user_meta($this->user->id, 'address2', $_POST['address2']);
        update_user_meta($this->user->id, 'address3', $_POST['address3']);
        update_user_meta($this->user->id, 'address4', $_POST['address4']);
        update_user_meta($this->user->id, 'secondaryemail', $_POST['secondaryemail']);
        update_user_meta($this->user->id, 'officephone', isset( $_POST['officephone'] ) ? $_POST['officephone'] :'' );
        update_user_meta($this->user->id, 'homephone', isset( $_POST['homephone'] ) ? $_POST['homephone'] :'' );
        update_user_meta($this->user->id, 'mobile', isset( $_POST['mobile'] ) ? $_POST['mobile'] :'');

        if ( !empty( $_POST['wpstocks_logo_url'] ) ) {
            update_user_meta($this->user->id, 'logo', $_POST['wpstocks_logo_url']);
        }

        return true;

    }

}
