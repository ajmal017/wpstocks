<?php

if(is_file("../../../../wp-load.php")){
  require("../../../../wp-load.php");
  include_once("functions.php");
}
else{
  // schema.org
  if(is_file("../api/utils/error/errorhandler.php")){
    include_once("../api/utils/error/errorhandler.php");
    include_once("../api/utils/db/db.ini.php");
    include_once("../api/functions.php");
  }
  else{
    include_once("../utils/error/errorhandler.php");
    include_once("../utils/db/db.ini.php");
    include_once("../functions.php");
  }

}

$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);

if(!$link){
  echo "Could not connect to server";
  die();
}

if(!mysqli_select_db($link, DB_NAME)){
  echo "Could not select DB ".DB_NAME;
  die();
}

date_default_timezone_set('GMT');


// Set response to 500 and only set it to 200 if everything goes ok
http_response_code(500);    

$method = $_SERVER['REQUEST_METHOD'];

switch($method){

case 'GET':

  // 200 if ok, 404 if not found, 500 if something goes wrong
  //  header('Content-Type:application/json');
  $stockInfo = null;

  if (!isset($_GET['api']) && !isset($_GET['apikey'])) {
    http_response_code(400);    
    echo "No api and api key found";
  }

  if (isset($_GET['stock'])) {
    $stockInfo = getStock($link, $_GET['api'], $_GET['apikey'], $_GET['stock'], isset($_GET['from'])?$_GET['from']:null, isset($_GET['to'])?$_GET['to']:null, isset($_GET['username'])?$_GET['username']:null);
  }
  elseif (isset($_GET['username'])) {
    $stockInfo = getStock($link, $_GET['api'], $_GET['apikey'], null, isset($_GET['from'])?$_GET['from']:null, isset($_GET['to'])?$_GET['to']:null, $_GET['username']);
  }
  
  if (empty($stockInfo)) {
    http_response_code(401);    
    echo "No stocks found";
  }
  else{
    http_response_code(200);    
    header('Content-Type:application/json');
    echo json_encode($stockInfo);
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


