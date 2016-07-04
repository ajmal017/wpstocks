<?php

$url = "http://premiumwebtechnologies.com/projects/wpstocks/wp-content/plugins/wpstocks/api/watched/?username=kdavies&stock=APP";

$res = getRemoteFileCurl($url, "DELETE", array());

echo $res["content"];


function getRemoteFileCurl($url, $method, $postFields, $referrer="", $userAgent="", $username=null, $password=null, $headers=false, $customHeaders=null, $cookie_file_name="cookie.txt"){

  // create a new cURL resource
  $ch = curl_init();
  // set URL and other appropriate options
  curl_setopt($ch, CURLOPT_VERBOSE, 1);
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies/'.$cookie_file_name);  
  curl_setopt($ch, CURLOPT_COOKIEJAR,  'cookies/'.$cookie_file_name);
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
 
 if(strtoupper($method)=='DELETE'){
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 1);
  }
   
  // grab URL and pass it to the browser
  curl_setopt($ch,CURLOPT_FAILONERROR,true);
  $content = curl_exec($ch);

  $error = curl_error($ch);
  preg_match("/[0-9].*/", $error, $matches);
  $error_code = isset($matches[0])?$matches[0]:"";

  // close cURL resource, and free up system resources
  curl_close($ch);

  return array('content'=>$content, 'error'=>$error, 'error_code'=>$error_code);
  
}