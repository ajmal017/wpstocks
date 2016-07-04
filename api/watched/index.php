<?php

if(is_file("../../../../../wp-load.php")){
  require("../../../../../wp-load.php");
  include_once("../functions.php");
}
else{
  // schema.org
  if(is_file("../../api/utils/error/errorhandler.php")){
    include_once("../../api/utils/error/errorhandler.php");
    include_once("../../api/utils/db/db.ini.php");
    include_once("../../api/functions.php");
  }
  else{
    include_once("../../utils/error/errorhandler.php");
    include_once("../../utils/db/db.ini.php");
    include_once("../../functions.php");
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
//http_response_code(500);    

$method = $_SERVER['REQUEST_METHOD'];
//$method = "POST";
//$_POST = $_GET;

switch($method){

case 'GET':

  // 200 if ok, 404 if not found, 500 if something goes wrong
  //  header('Content-Type:application/json');
  if (!isset($_GET['username']) || !isset($_GET['api']) || !isset($_GET['apikey'])) {
    http_response_code(400);    
    die();
  }

  $stockInfo = getWatchedStock($link, $_GET['api'], $_GET['apikey'], $_GET['username']);
  
  if (empty($stockInfo)) {
    http_response_code(404);    
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
  if (!isset($_POST['username']) || !isset($_POST['stock'])) {
    echo "Wrong parameters";
    http_response_code(400);    
    header('Content-Type:application/json');
    die();
  }

  addWatchedStock($link, $_POST['stock'], $_POST['username']);
  echo json_encode(getLatestStockInformation($link, $_POST['api'], $_POST['apikey'], $_POST['stock'], $_POST['username']));
  http_response_code(201);    

  break;

case 'PUT':
  // 201 if created, with Location set to new panel, 400 if bad request, 500 if something goes wrong, 405 not allowed, 200 if update
  // $data = array();
  // $incoming = file_get_contents("php://input");
  // parse_str($incoming, $data);
  break;

case 'DELETE':

  // 204 if delete successful, 404 if not found, 500 if something goes wrong, 405 if not allowed
  if (!isset($_GET['username']) || !isset($_GET['stock'])) {
    http_response_code(400);    
    die();
  }

  removeWatchedStock($link, $_GET['stock'], $_GET['username']);

  http_response_code(204);    

  break;
}


