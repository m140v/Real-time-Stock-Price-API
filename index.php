<?php


/* This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, Version 3.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    */

//author: Mike De'Shazer || @itsdeshazer


// Start by getting the ticker symbol and assigning it to variable $ticker
extract($_REQUEST);

if(isset($ticker)){
//check if $ticker is set
$ticker = strtoupper($ticker);

//URLs (valid on 4/8/2012) 
//These need to be changed as the formats of the pages change over time
$reuters= "http://reuters.com/finance/stocks/overview?symbol=". $ticker;
$nasdaq= "http://www.nasdaq.com/symbol/". $ticker;
$yahoo= "http://finance.yahoo.com/q?s=". $ticker;


//Try to retrieve Reuters price first (most reliable of 3 possible sources)
$reutResult = file_get_contents($reuters);
$nyArr1 = explode( 'font-size: 23px;">', $reutResult);
if($nyArr1[1]){
$nyArr2 = explode( "</span>", $nyArr1[1]);
if($nyArr2[1]){
$nyPrice = $nyArr2[0];
}
}


if($nyPrice){
    // We have Reuter's price data for this stock
     $jsonResponse = '{"price": "'.floatval($nyPrice).'", "source": "Reuters"}';
     echo json_encode($jsonResponse);
    return;

}



else{

//could not get Reuters, so trying Nasdaq
 $nasResult = file_get_contents($nasdaq);   
 //Try to retrieve Nasdaq price:
$nasArr1 = explode( "_LastSale1'>", $nasResult);
if($nasArr1[1]){
$nasArr2 = explode( "</label>", $nasArr1[1]);
if($nasArr2[1]){
$nasPrice = $nasArr2[0];
}
}


if($nasPrice){
    //we have Nasdaq's price
    $nasPrice = str_replace("$", "", $nasPrice);
    $nasPrice = str_replace(" ", "", $nasPrice);
     $jsonResponse = '{"price": "'. $nasPrice.'", "source": "Nasdaq"}';
     echo json_encode($jsonResponse);
    //return;

}



else{
    //could not get Nasdaq or Reutors, so trying Yahoo
    $yahResult = file_get_contents($yahoo);

$ticker = strtolower($ticker);
$yahArr1 = explode( 'id="yfs_l84_'.$ticker.'">', $yahResult);
if($yahArr1[1]){
   // echo $yahArr1[1];
$yahArr2 = explode( " ", $yahArr1[1]);
if($yahArr2[1]){
   
$yahPrice = $yahArr2[0];
}
}


if($yahPrice){
     $jsonResponse = '{"price": "'.floatval($yahPrice).'" , "source": "Yahoo"}';
     echo json_encode($jsonResponse);
    //return;

}

else{
      $jsonResponse = '{"error": "Y"Please make sure you passed a valid stock sticker symbol. (e.g. yoursite.com/?ticker=GOOG). If this error persists, please update this script with the latest version ( https://github.com/m140v/Real-time-Stock-Price-API/). The source site might have been reformatted."}';
     echo json_encode($jsonResponse);
    return;

}

}

}

}


else{
    $jsonResponse = '{"error": "please send a ticker symbol in your request with the key `ticker`."}';
     echo json_encode($jsonResponse);
    return;
    }





?>