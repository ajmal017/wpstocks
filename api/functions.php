<?php

// Test links
//http://localhost:8888/wp/wp-content/plugins/wpstocks/api/?stock=IBM&api=barchart&apikey=489911a5880b02d7093e260e0158fd39 GET
//http://localhost:8888/wp/wp-content/plugins/wpstocks/api/?username=kdavies&api=barchart&apikey=489911a5880b02d7093e260e0158fd39 GET
//http://localhost:8888/wp/wp-content/plugins/wpstocks/api/?stock=IBM&from=3 October 2015&to=&api=barchart&apikey=489911a5880b02d7093e260e0158fd39 GET
//http://localhost:8888/wp/wp-content/plugins/wpstocks/api/watched/?username=kdavies&api=barchart&apikey=489911a5880b02d7093e260e0158fd39 GET
//http://premiumwebtechnologies.com/projects/wpstocks/wp-content/plugins/wpstocks/api/?stock=IBM&api=barchart&apikey=489911a5880b02d7093e260e0158fd39 GET
//http://premiumwebtechnologies.com/projects/wpstocks/wp-content/plugins/wpstocks/api/?username=kdavies&api=barchart&apikey=489911a5880b02d7093e260e0158fd39 GET
//http://premiumwebtechnologies.com/projects/wpstocks/wp-content/plugins/wpstocks/api/?stock=IBM&from=3 October 2015&to=&api=barchart&apikey=489911a5880b02d7093e260e0158fd39 GET
//http://premiumwebtechnologies.com/projects/wpstocks/wp-content/plugins/wpstocks/api/watched/?username=kdavies&api=barchart&apikey=489911a5880b02d7093e260e0158fd39 GET
//http://premiumwebtechnologies.com/projects/wpstocks/wp-content/plugins/wpstocks/api/watched/?username=kdavies&stock=IBM POST

function getStock($link, $api, $apiKey, $stock, $from, $to, $username)
{

  $stockInfo = array();

  if (!empty($from) || empty($stock)) {
    $stockInfo = getStockInformationFromDB($link, $stock, $from, empty($to)?$to:date("Y-m-d"), $username);
  }

  if (empty($to)) {
    $latestStockInformation = getLatestStockInformation($link, $api, $apiKey, $stock, $username);
    $stockInfo = array_merge(empty($latestStockInformation)?array():$latestStockInformation, empty($stockInfo)?array():$stockInfo);
  }

  return formatStockInfo( $stockInfo );

}

function getWatchedStock($link, $api, $apiKey, $username)
{
  $usernameSafe = mysqli_real_escape_string($link, $username);
  $sql = "SELECT `symbol` FROM `wpstocks_watched` where `username`='$usernameSafe'";
  $watchedStocks = array();
  $res = mysqli_query($link, $sql);
  $stockSymbols = array();
  while ($row=mysqli_fetch_assoc($res)) {
    $stockSymbols[] = $row['symbol'];
  }
  /*
	  $stockInfo[] = array('symbol'=>$result->symbol, 'name'=>$result->name, 'lastPrice'=>$result->lastPrice, 'date'=>date("Y-m-d H:i:s", strtotime($result->tradeTimestamp)), 'netChange'=>$result->netChange, 'percentChange'=>$result->percentChange, 'open'=>$result->open, 'high'=>$result->high, 'low'=>$result->low, 'close'=>$result->close, 'volume'=>$result->volume);
   */
  $watchedStocks = getLatestStockInformation($link, $api, $apiKey, implode(",",$stockSymbols), $username);
  return formatStockInfo($watchedStocks);
}

function removeWatchedStock($link, $stock, $username)
{
  $usernameSafe = mysqli_real_escape_string($link, $username);
  $stockSafe = mysqli_real_escape_string($link, $stock);
  $sql = "DELETE FROM `wpstocks_watched` where `username`='$usernameSafe' AND `symbol`='$stockSafe'";
  mysqli_query($link, $sql);
}

function addWatchedStock($link, $stock, $username)
{
  $usernameSafe = mysqli_real_escape_string($link, $username);
  $stockSafe = mysqli_real_escape_string($link, $stock);
  $sql = "INSERT INTO `wpstocks_watched` (`symbol`, `username`) VALUES ( '$stockSafe', '$usernameSafe')";
  mysqli_query($link, $sql);
}

function getLatestStockInformation($link, $api, $apiKey, $stock, $username)
{

  $stockInfo = getStockInformationFromDB($link, $stock, date("Y-m-d"), date("Y-m-d"), $username);

  if (empty($stockInfo)) {
    switch ($api) {
    case 'barchart':
      // Note: symbols can be combined eg IBM,GOOGLE
      /*
{"status":{"code":200,"message":"Success."},"results":[{"symbol":"IBM","exchange":"NYSE","name":"International Business Machines","dayCode":"9","serverTimestamp":"2015-10-09T21:51:57-05:00","mode":"d","lastPrice":152.39,"tradeTimestamp":"2015-10-09T21:36:57-05:00","netChange":0.11,"percentChange":0.07,"unitCode":"2","open":152.46,"high":153.15,"low":151.27,"close":152.39,"flag":"s","volume":3531000}]}
       */
      $raw = null;
      if (empty($stock)) {
	if (!empty($username)) {
	  $usernameSafe = mysqli_real_escape_string($link, $username);
	  $sql = "SELECT DISTINCT `wpstocks_trades`.`symbol` FROM `wpstocks_trades` WHERE `username`='$usernameSafe' ORDER BY `date`";
	  $res = mysqli_query($link, $sql);
	  if (mysqli_num_rows($res) > 0) {
	    $stockSymbols = array();
	    while ($row=mysqli_fetch_assoc($res)) {
	      $stockSymbols[] = $row['symbol'];
	    }
	    $raw = json_decode(file_get_contents("http://marketdata.websol.barchart.com/getQuote.json?key=$apiKey&symbols=".implode(",",$stockSymbols)));
	  }
	}
      }
      else{
	$stock = urlencode($stock);
	$raw = json_decode(file_get_contents("http://marketdata.websol.barchart.com/getQuote.json?key=$apiKey&symbols=$stock"));
      }


      if (!empty($raw) && $raw->status->code==200) {
	$results = $raw->results;
	foreach ($results as  $result) {
	  $stockInfo[] = array('symbol'=>$result->symbol, 'name'=>$result->name, 'lastPrice'=>$result->lastPrice, 'date'=>date("Y-m-d H:i:s", strtotime($result->tradeTimestamp)), 'netChange'=>$result->netChange, 'percentChange'=>$result->percentChange, 'open'=>$result->open, 'high'=>$result->high, 'low'=>$result->low, 'close'=>$result->close, 'volume'=>$result->volume, 'currency'=>'USD');
	}
      }
      break;
    }

    if (!empty($stockInfo)) {
      foreach ($stockInfo as $stockRecord) {
	if (!empty($stockRecord['close'])) {
	  $stockSafe = mysqli_real_escape_string($link, $stockRecord['symbol']);
	  $nameSafe = mysqli_real_escape_string($link, $stockRecord['name']);
	  $lastPriceSafe = mysqli_real_escape_string($link, $stockRecord['lastPrice']);
	  $dateSafe = mysqli_real_escape_string($link, $stockRecord['date']);
	  $netChangeSafe = mysqli_real_escape_string($link, $stockRecord['netChange']);
	  $percentChangeSafe = mysqli_real_escape_string($link, $stockRecord['percentChange']);
	  $openSafe = mysqli_real_escape_string($link, $stockRecord['open']);
	  $closeSafe = mysqli_real_escape_string($link, $stockRecord['close']);
	  $highSafe = mysqli_real_escape_string($link, $stockRecord['high']);
	  $lowSafe = mysqli_real_escape_string($link, $stockRecord['low']);
	  $volumeSafe = mysqli_real_escape_string($link, $stockRecord['volume']);
	  $sql = "INSERT INTO `wpstocks_stocks` (`symbol`, `name`) VALUES('$stockSafe', '$nameSafe')";
	  mysqli_query($link, $sql);
	  $sql = "INSERT INTO `wpstocks_stock_info` (`symbol`, `lastPrice`, `date`, `netChange`, `percentChange`, `open`, `close`, `low`, `high`, `volume`) VALUES('$stockSafe', '$lastPriceSafe', '$dateSafe', '$netChangeSafe', '$percentChangeSafe', '$openSafe', '$closeSafe', '$lowSafe', '$highSafe', '$volumeSafe')";
	  mysqli_query($link, $sql);
	}
      }
    }

  }

  return  $stockInfo;

}


function formatStockInfo( $stockInfo )
{
    $i = 0;
    for ($i = 0; $i < count( $stockInfo); $i++ ) {
        $stockInfo[ $i ][ "lastPrice" ] = trim( money_format( "%n", $stockInfo[ $i ][ "lastPrice" ] ), '$' );
        $stockInfo[ $i ][ "high" ] = trim( money_format( "%n", $stockInfo[ $i ][ "high" ] ), '$' );
        $stockInfo[ $i ][ "low" ] = trim( money_format( "%n", $stockInfo[ $i ][ "low" ] ), '$' );
        $stockInfo[ $i ][ "close" ] = trim( money_format( "%n", $stockInfo[ $i ][ "close" ] ), '$' );
        $stockInfo[ $i ][ "open" ] = trim( money_format( "%n", $stockInfo[ $i ][ "open" ] ), '$' );
        $stockInfo[ $i ][ "currency" ] = 'USD';
    }
    return $stockInfo;
}

function getStockInformationFromDB($link, $stock, $from, $to, $username)
{

  $stocks = null;

  $from = empty($from)? date("Y-m-d") : date("Y-m-d", strtotime($from));
  $to = empty($to)? date("Y-m-d") : date("Y-m-d", strtotime($to));

  if (empty($stock) && !empty($username)) {
    $usernameSafe = mysqli_real_escape_string($link, $username);
    $sql = "SELECT `wpstocks_stocks`.`symbol`, `name`, `wpstocks_stock_info`.`date`, `buy`, `netChange`, `percentChange`, `open`, `close`, `low`, `high`, `volume`,`numberOfShares` FROM `wpstocks_stocks`, `wpstocks_trades`, `wpstocks_stock_info` WHERE `username`='$usernameSafe' AND `wpstocks_stocks`.`symbol` = `wpstocks_trades`.`symbol` AND `wpstocks_stocks`.`symbol` = `wpstocks_stock_info`.`symbol` AND `wpstocks_stock_info`.`date` >= '$from' AND `wpstocks_stock_info`.`date` <= '$to' ORDER BY `date`";
  }

  if (!empty($stock)) {
    $stockSafe = mysqli_real_escape_string($link, $stock);
    $sql = "SELECT `wpstocks_stocks`.`symbol`, `name`, `lastPrice`, `date`, `netChange`, `percentChange`, `open`, `close`, `low`, `high`, `volume` FROM `wpstocks_stocks`, `wpstocks_stock_info` WHERE `wpstocks_stocks`.`symbol` = '$stockSafe' AND `wpstocks_stocks`.`symbol` = `wpstocks_stock_info`.`symbol` AND `date` >= '$from' AND `date` <= '$to' ORDER BY `date`";
  }

  $res = mysqli_query($link, $sql);
  echo mysqli_error($link);
  $stocks = array();
  if (mysqli_num_rows($res) == 0) {
    return null;
  }
  while ($row = mysqli_fetch_assoc($res)) {
    $stocks[] = $row;
  }
  return $stocks;

}