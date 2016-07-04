<?php

if (!function_exists('pwt_sideload_image')) {
  // http://theme.fm/2011/10/how-to-upload-media-via-url-programmatically-in-wordpress-2657/
  function pwt_sideload_image($url, $post_id=0) 
  {
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $file = media_sideload_image( $url, $post_id );
    return $file;
  }
}

if (!function_exists('pwt_set_featured_image')) {
  function pwt_set_featured_image($post_id)
  {
    $attachments = get_posts(array('numberposts' => '1', 'post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC'));
    if (sizeof($attachments) > 0) {
      // set image as the post thumbnail
      set_post_thumbnail($post_id, $attachments[0]->ID);
    }  
  }
}

if (!function_exists('pwt_http_response_code2')) {

  function pwt_http_response_code2($code = NULL) 
  {

    if ($code !== NULL) {

      switch ($code) {
      case 100: $text = 'Continue'; break;
      case 101: $text = 'Switching Protocols'; break;
      case 200: $text = 'OK'; break;
      case 201: $text = 'Created'; break;
      case 202: $text = 'Accepted'; break;
      case 203: $text = 'Non-Authoritative Information'; break;
      case 204: $text = 'No Content'; break;
      case 205: $text = 'Reset Content'; break;
      case 206: $text = 'Partial Content'; break;
      case 300: $text = 'Multiple Choices'; break;
      case 301: $text = 'Moved Permanently'; break;
      case 302: $text = 'Moved Temporarily'; break;
      case 303: $text = 'See Other'; break;
      case 304: $text = 'Not Modified'; break;
      case 305: $text = 'Use Proxy'; break;
      case 400: $text = 'Bad Request'; break;
      case 401: $text = 'Unauthorized'; break;
      case 402: $text = 'Payment Required'; break;
      case 403: $text = 'Forbidden'; break;
      case 404: $text = 'Not Found'; break;
      case 405: $text = 'Method Not Allowed'; break;
      case 406: $text = 'Not Acceptable'; break;
      case 407: $text = 'Proxy Authentication Required'; break;
      case 408: $text = 'Request Time-out'; break;
      case 409: $text = 'Conflict'; break;
      case 410: $text = 'Gone'; break;
      case 411: $text = 'Length Required'; break;
      case 412: $text = 'Precondition Failed'; break;
      case 413: $text = 'Request Entity Too Large'; break;
      case 414: $text = 'Request-URI Too Large'; break;
      case 415: $text = 'Unsupported Media Type'; break;
      case 500: $text = 'Internal Server Error'; break;
      case 501: $text = 'Not Implemented'; break;
      case 502: $text = 'Bad Gateway'; break;
      case 503: $text = 'Service Unavailable'; break;
      case 504: $text = 'Gateway Time-out'; break;
      case 505: $text = 'HTTP Version not supported'; break;
      default:
	exit('Unknown http status code "' . htmlentities($code) . '"');
	break;
      }

      $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

      header($protocol . ' ' . $code . ' ' . $text);

      $GLOBALS['http_response_code'] = $code;

    } else {

      $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);

    }

    return $code;

  }

}

if (!function_exists('pwt_getRemoteFileCurl')) {
  function pwt_getRemoteFileCurl($url, $method, $postFields, $referrer="", $userAgent="", $username=null, $password=null, $headers=false, $customHeaders=null, $cookie_file_name="cookie.txt")
  {

    // create a new cURL resource
    $ch = curl_init();
    // set URL and other appropriate options
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies/'.$cookie_file_name);  
    curl_setopt($ch, CURLOPT_COOKIEJAR,  'cookies/'.$cookie_file_name);
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

    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);    // close cURL resource, and free up system resources

    curl_close($ch);

    return array('status'=>$http_status==0?'200':$http_status, 'content'=>$content);
  }

}

if (!function_exists('pwt_image_resize')) {
 function pwt_image_resize($source,$target,$width,$height,$quality) 
 {

    $size = getimagesize($source);

    $ext=strtolower(substr($size['mime'],6));

    $target .=".".$ext;

    if ($ext == 'png') {
      return false;
    }

    //	 print_r($size);
    // scale evenly
    $ratio = $size[0] / $size[1];
    if ($ratio >= 1) {
      $scale = $width / $size[0];
    } else {
      $scale = $height / $size[1];
    }
    // make sure its not smaller to begin with!
    if ($width >= $size[0] && $height >= $size[1]) {
      $scale = 1;
    }

    if ($ext == 'jpg' || $ext == 'jpeg') {
      $im_in = imagecreatefromjpeg ($source);
    } else if ($ext == 'png') {
      $im_in = imagecreatefrompng ($source);
    } else if ($ext == 'gif') {
      $im_in = imagecreatefromgif ($source);
    }

    @$im_out = imagecreatetruecolor($size[0] * $scale, $size[1] * $scale);
    if (!$im_out) {
      $im_out = imagecreatetruecolor(75, 100);
    }

    imagecopyresampled($im_out, $im_in, 0, 0, 0, 0, $size[0] * $scale, $size[1] * $scale, $size[0], $size[1]);

    if ($ext == 'jpg' || $ext == 'jpeg') {
      imagejpeg($im_out, $target, $quality);
    } else if ($ext == 'png') {
      if (!@imagepng($im_out, $target, $quality)) {
	  return false;
      }
    } else if ($ext == 'gif') {
      imagegif($im_out, $target, $quality);
    }

    imagedestroy($im_out);
    imagedestroy($im_in);

    return $ext;
  }
}

