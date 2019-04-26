<?php
require("phpsqlajax_dbinfo.php");
header("Content-Type: text/html; charset=utf-8");
// Opens a connection to a MySQL server
$connection=mysqli_connect ('locahost', $username, $password,$database);
if (!$connection) {
  die('Not connected : ' . mysql_error());
}

// Select all the rows in the markers table
$Sid = $_GET['sid'];
//$Sid = '100';//改成get
$StationQuery = "SELECT airboxid, distance, level FROM distance WHERE stationid = ".$Sid." AND level <= 10";
$StationResult = mysqli_query($connection,$StationQuery);
if (!$StationResult) {
  die('Invalid query: ' . mysql_error());
}
// Iterate through the rows, printing XML nodes for each
$Aid = array();
$Aid_1 = array();
$Aid_5 = array();
while ($row = @mysqli_fetch_assoc($StationResult)){
    $AirboxID = $row['airboxid'];
    array_push($Aid, $AirboxID);
    $Level = (int)$row['level'];
    if($Level<=1){
        array_push($Aid_1, $AirboxID);
    }
    if($Level<=5){
        array_push($Aid_5, $AirboxID);
    }
}
//print_r($Aid);
$arrlength = count($Aid);
//declare element
$PM25_1 = array();
$Temperature_1 = array();
$Humidity_1 = array();
$PM25_5 = array();
$Temperature_5 = array();
$Humidity_5 = array();
$PM25_10 = array();
$Temperature_10 = array();
$Humidity_10 = array();
$c = 0;
for($x = 0; $x < $arrlength; $x++) {    
    $url = 'https://pm25.lass-net.org/data/last.php?device_id='.$Aid[$x];
    $handle = fopen($url,"rb");
    $content = "";
    while (!feof($handle)) {
        $content .= fread($handle, 10000);
    }
    fclose($handle);
    $content = json_decode($content);
    $content = $content->{'feeds'};
    if($content!=null){
        $c++;
        $AirBox = $content[0]->{'AirBox'};
        $s_d0 = $AirBox->{'s_d0'};
        $s_h0 = $AirBox->{'s_h0'};
        $s_t0 = $AirBox->{'s_t0'};
        $timestamp = $AirBox->{'timestamp'};
        // put data to array
        array_push($PM25_10, $s_d0);
        array_push($Humidity_10, $s_h0);
        array_push($Temperature_10, $s_t0);
        if(in_array($Aid[$x],$Aid_1)){
            //該airbox是否在範圍1公里內的陣列中
            array_push($PM25_1, $s_d0);
            array_push($Humidity_1, $s_h0);
            array_push($Temperature_1, $s_t0);
        }
        if(in_array($Aid[$x],$Aid_5)){
            //該airbox是否在範圍1公里內的陣列中
            array_push($PM25_5, $s_d0);
            array_push($Humidity_5, $s_h0);
            array_push($Temperature_5, $s_t0);
        }

    }
}

$avg_pm10 = array_sum($PM25_10)/$c;
$avg_pm5 = array_sum($PM25_5)/$c;
$avg_pm1 = array_sum($PM25_1)/$c;
echo "<result>";
echo "<avg_pm1>".sprintf("%.3f", $avg_pm1)."</avg_pm1>";
echo "<avg_pm5>".sprintf("%.3f", $avg_pm5)."</avg_pm5>";
echo "<avg_pm10>".sprintf("%.3f", $avg_pm10)."</avg_pm10>";
echo "</result>";