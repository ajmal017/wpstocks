<?php
  
class YouTube{

  function YouTube(){
  }     

  public function parseSearchResults($search_results, $thumb_dir, $thumb_url, $thumb_width){

    $results = array();

    if(is_object($search_results)){
      $videos = $search_results->items;

      foreach($videos as $video){
	$description = $video->snippet->description;
	$text = substr(strip_tags($description), 0, 100);
	$content_short = substr($text,0,strrpos($text," "));
	$img_url = $video->snippet->thumbnails->default->url;
	$temp_file_name = tempnam(sys_get_temp_dir(), 'yt');
	if(1){
	  $title = $video->snippet->title;
	  $video_id = $video->id->videoId;
	  $url = "http://www.youtube.com/watch?v=$video_id";
	  $embed_code= "<iframe width=\"420\" height=\"315\" src=\"http://www.youtube.com/embed/$video_id?rel=0\" frameborder=\"0\" allowfullscreen></iframe>";
	  $results[] = array('url'=>$url, 'tiny_img'=>null, 'price'=>null, 'type'=>null, 'title'=>str_replace(array("&#39;", "&amp;", "&quot;"), array("'", "&", '"'), strip_tags($title)), 'description'=>str_replace(array("&#39;", "&amp;", "&quot;"), array("'", "&", '"'), strip_tags($description)), 'preview'=>$content_short, 'youtube_id'=>$video_id, 'youtube_embed_code'=>$embed_code, 'merchant_url'=>$url);
	}

      }
    }
    return $results;
  }


  public function image_resize($source,$target,$width,$height,$quality) {

    $size = getimagesize($source);

    $ext=strtolower(substr($size['mime'],6));

    $target .=".".$ext;

    if($ext == 'png'){
      return false;
    }

    //	 print_r($size);
    // scale evenly
    $ratio = $size[0] / $size[1];
    if ($ratio >= 1){
      $scale = $width / $size[0];
    } else {
      $scale = $height / $size[1];
    }
    // make sure its not smaller to begin with!
    if ($width >= $size[0] && $height >= $size[1]){
      $scale = 1;
    }

    if ($ext == 'jpg' || $ext == 'jpeg'){
      $im_in = imagecreatefromjpeg ($source);
    } else if ($ext == 'png') {
      $im_in = imagecreatefrompng ($source);
    } else if ($ext == 'gif') {
      $im_in = imagecreatefromgif ($source);
    }

    @$im_out = imagecreatetruecolor($size[0] * $scale, $size[1] * $scale);
    if(!$im_out){
      $im_out = imagecreatetruecolor(75, 100);
    }

    imagecopyresampled($im_out, $im_in, 0, 0, 0, 0, $size[0] * $scale, $size[1] * $scale, $size[0], $size[1]);

    if ($ext == 'jpg' || $ext == 'jpeg'){
      imagejpeg($im_out, $target, $quality);
    } else if ($ext == 'png') {
      if(!@imagepng($im_out, $target, $quality)){
	  return false;
      }
    } else if ($ext == 'gif') {
      imagegif($im_out, $target, $quality);
    }

    imagedestroy($im_out);
    imagedestroy($im_in);

    return $ext;
  }

  public function search($youtube_api_key, $keywords, $channel_id, $n){
  
    $keywords = urlencode($keywords);  
    if(empty($channel_id)){
      return $this->getRemoteFileCurl("https://www.googleapis.com/youtube/v3/search?key=$youtube_api_key&q=$keywords&part=snippet&maxResults=$n", "GET", array());
    }
    else{
      return $this->getRemoteFileCurl("https://www.googleapis.com/youtube/v3/search?channelId=$channel_id&key=$youtube_api_key&q=$keywords&part=snippet&maxResults=$n", "GET", array());
    }
  }

  public function getRemoteFileCurl($url, $method, $postFields, $referrer="", $userAgent="", $username=null, $password=null, $headers=false, $customHeaders=null){

    // create a new cURL resource
    $ch = curl_init();
    // set URL and other appropriate options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies/cookie.txt');  
    curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies/cookie.txt');
    //   curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    if(empty($customHeaders)){
      $customHeaders = array('Expect: '); // Stop 417 errors
    }
    else{
      $customHeaders[] = 'Expect: ';
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $customHeaders); 
    curl_setopt($ch, CURLOPT_HEADER, $headers);
    if(!empty($referrer)){
      curl_setopt($ch, CURLOPT_REFERER, $referrer);
    }
   
    if(!empty($userAgent)){
      curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
    }
   
    if(!empty($username)){
      curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");   
    }
   
    if(strtoupper($method)=='POST'){
      curl_setopt($ch, CURLOPT_POST, 1);
      if(!empty($postFields)){
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
      }
    }
   
    // grab URL and pass it to the browser
    $content = curl_exec($ch);

    // close cURL resource, and free up system resources
    curl_close($ch);

    return $content;
  
  }

}


?>