
<?php

if(!function_exists('wpap_drop_tables')){

  function wpap_drop_tables(){
    global $wpdb;

    $sql = "DROP TABLE `table`";
    $wpdb->query($sql);

  }

}

wpap_drop_tables();

delete_option('option');

remove_role("wpstocks");

?>