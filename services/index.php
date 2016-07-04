<?php

// schema.org
if(is_file("../wp-load.php")){
  ob_start();
  require("../wp-load.php");
  global $wpdb;
  ob_end_clean();
  //    if($wpdb){
  //      $recs = $wpdb->get_results($sql, ARRAY_A);
  //    }
}
else{
  /*
  if(is_file("../services/utils/error/errorhandler.php")){
    include_once("../services/utils/error/errorhandler.php");
    include_once("../services/utils/db/db.ini.php");
  }
  else{
    include_once("../utils/error/errorhandler.php");
    include_once("../utils/db/db.ini.php");
  }
  $link = mysql_connect(DB_SERVER, DB_USER, DB_PASSWORD);
  if(!$link){
    echo "Could not connect to server";
    die();
  }
  if(!mysql_select_db(DB)){
    echo "Could not select DB ".DB;
    die();
  }
  */
}

date_default_timezone_set('GMT');

$method = $_SERVER['REQUEST_METHOD'];

switch($method){

case 'GET':

  // 200 if ok, 404 if not found, 500 if something goes wrong
  if(!isset($_GET['ytp_api_key']) || empty($_GET['ytp_api_key'])){
    http_response_code2(400);    
    echo "Missing YouTube API key";
  }
  else{
    include('youtube.class.php');
    $youtube  = new YouTube();
    header('Content-Type:application/json');
    $res = $youtube->search($_GET['ytp_api_key'], $_GET['ytp_keyword'], (isset($_GET['ytp_channel_id'])?$_GET['ytp_channel_id']:null), (isset($_GET['count'])?$_GET['count']:25));
    $res_decoded = (array)json_decode($res);
    if(isset($res_decoded['error'])){
      http_response_code2($res_decoded['error']->code);    
      echo $res_decoded['error']->message;
    }
    else{
      echo $res;
    }
  }

  break;

case 'POST':

  // 201 if created, with Location set to new panel, 400 if bad request, 500 if something goes wrong, 405 not allowed, 200 if update
  break;

case 'PUT':
  // 201 if created, with Location set to new panel, 400 if bad request, 500 if something goes wrong, 405 not allowed, 200 if update
  // $data = array();
  // $incoming = file_get_contents("php://input");
  // parse_str($incoming, $data);
  break;
case 'DELETE':
  // 204 if delete successful, 404 if not found, 500 if something goes wrong, 405 if not allowed
  break;
}

  function http_response_code2($code = NULL) {

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


?>