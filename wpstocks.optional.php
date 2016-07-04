<?php

function wpstocks_fb_post_engine_change_excerpt_label( $translation, $original )
{
  if ("fbpostengine_page" == get_post_type()) {
    if ( 'Excerpt' == $original ) {
      return 'Facebook Post Content';
    }else{
      $pos = strpos($original, 'Excerpts are optional hand-crafted summaries of your');
      if ($pos !== false) {
	//      return  'My Excerpt description';
	return "";  
      }
    }
  }
  return $translation;
}

function wpstocks_do_this_hourly()
{
}

function wpstocks_do_this_twicedaily()
{
}

function wpstocks_do_this_daily()
{
}

function wpstocks_getRemoteFileCurl($url, $method, $postFields, $referrer="", $userAgent="", $username=null, $password=null, $headers=true, $customHeaders=null, $cookie_file_name="cookie.txt")
{

  // create a new cURL resource
  $ch = curl_init();
  // set URL and other appropriate options
  curl_setopt($ch, CURLOPT_VERBOSE, 1);
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_COOKIEFILE, plugin_dir_path(__FILE__).'cookies/'.$cookie_file_name);  
  curl_setopt($ch, CURLOPT_COOKIEJAR, plugin_dir_path(__FILE__).'cookies/'.$cookie_file_name);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  if (empty($customHeaders)) {
    $customHeaders = array('Expect: '); // Stop 417 errors
  }
  else{
    $customHeaders[] = 'Expect: ';
  }
  curl_setopt($ch, CURLOPT_HTTPHEADER, $customHeaders); 
  curl_setopt($ch, CURLOPT_HEADER, $headers);
  if (!empty($referrer)) {
    curl_setopt($ch, CURLOPT_REFERER, $referrer);
  }
   
  if (!empty($userAgent)) {
    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
  }
   
  if (!empty($username)) {
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");   
  }
   
  if (strtoupper($method)=='POST') {
    curl_setopt($ch, CURLOPT_POST, 1);
    if (!empty($postFields)) {
      curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    }
  }
   
  // grab URL and pass it to the browser
  $content = curl_exec($ch);

  // close cURL resource, and free up system resources
  curl_close($ch);

  return $content;
  
}

function wpstocks_fancybox_content()
{
    ?>     
    <script> 
    jQuery(document).ready(function() { 
       	    jQuery('#wpfancyboxopener').click(); 
      }); 
    </script> 

    <div id="wpfancybox" style="display:none; max-width:400px; padding:5px; margin:5px;">
	<div>hello world</div>
        <div id="sociallike">
            <!--Facebook Like -->
                <div id="fb_share">
                    <a href="javascript:void(0)" onclick="javascript:window.open('https://www.facebook.com/sharer/sharer.php?u=<?php echo $fzsmpOptions["facebookshare"]; ?>', 'PingStatistics','status=0,toolbar=0,menubar=0,scrollbars=1,location=0,resizable=0,width=500,height=300')"></a>
                </div>
                <div id="facebook-like">
                    <div id="fb-root"></div>
                    <script>(function(d, s, id) {
                        var js, fjs = d.getElementsByTagName(s)[0];
                        if (d.getElementById(id)) return;
                        js = d.createElement(s); js.id = id;
                        js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=500316343358577";
                        fjs.parentNode.insertBefore(js, fjs);
                    }(document, 'script', 'facebook-jssdk'));</script>
                    <div class="fb-like" data-href="<?php echo $fzsmpOptions['facebook']; ?>" data-send="false" data-layout="button_count" data-width="85" data-show-faces="false"></div>
                </div>
                <div id="twitter-like">    
                    <a href="https://twitter.com/share" class="twitter-share-button" data-size="large" data-url="<?php echo $fzsmpOptions['twitter']; ?>" data-count="none">Tweet</a>
                    <script>!function(d,s,id) {var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if (!d.getElementById(id)) {js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
                </div>
        </div>
    </div>
    <a id="wpfancyboxopener" title="TITLE" href="#wpfancybox" class="fancybox" style="display:none;"></a>
    <?php
}

