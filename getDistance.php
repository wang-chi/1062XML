<?php
/**
*求两个已知经纬度之间的距离,单位为米
*@param lng1,lng2 经度
*@param lat1,lat2 纬度
*@return float 距离，单位米
*@author www.phpernote.com
**/
require("phpsqlajax_dbinfo.php");

function getdistance($lat1,$lng1,$lat2,$lng2){
	//将角度转为狐度
	$radLat1=deg2rad($lat1);//deg2rad()函数将角度转换为弧度
	$radLat2=deg2rad($lat2);
	$radLng1=deg2rad($lng1);
	$radLng2=deg2rad($lng2);
	$a=$radLat1-$radLat2;
	$b=$radLng1-$radLng2;
	$s=2*asin(sqrt(pow(sin($a/2),2)+cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)))*6378.137*1000;
	return round($s,5);
}
// Opens a connection to a MySQL server
$connection=mysqli_connect ('163.17.136.150', $username, $password,$database);
if (!$connection) {
  die('Not connected : ' . mysql_error());
}
// Select all the rows in the markers table
$StationQuery = "SELECT ID, StationName, lat, lng FROM station";
$StationResult = mysqli_query($connection,$StationQuery);
if (!$StationResult) {
  die('Invalid query: ' . mysql_error());
}
// Iterate through the rows, printing XML nodes for each
while ($row = @mysqli_fetch_assoc($StationResult)){
    //echo $row['lat'].','.$row['lng'].'<br>';
    $Slat = $row['lat'];
    $Slng = $row['lng'];
    $Sid = $row['ID'];
    //get AirboxList 
    $AirboxListQuery = "SELECT airboxid, lat, lng FROM airboxlist";
    $AirboxResult = mysqli_query($connection,$AirboxListQuery);
    if (!$StationResult) {
        die('Invalid query: ' . mysql_error());
    }
    while ($row2 = @mysqli_fetch_assoc($AirboxResult)){
        //getdistance(Slat,Slng,Alat,Alng)
        $Alat = $row2['lat'];
        $Alng = $row2['lng'];
        $Aid = $row2['airboxid'];
        $dist = getdistance($Slat,$Slng,$Alat,$Alng);
        $level = round($dist/1000,1);
        if($level<11){
            // 距離1公里內，登入到資料庫中
            $sql = "INSERT INTO distance (airboxid, stationid, distance, level) VALUES ('$Aid', '$Sid', '$dist', $level)";

            if ($connection->query($sql) === TRUE) {
                echo $Sid.' & '.$Aid,'的距離為 '.$dist." 公尺=> successfully<br>";
            } else {
                echo "Error: " . $sql . "<br>" . $connection->error;
            }
        }
    }

}
echo 'finish';
//echo getdistance(23.969,120.981003,23.979985,120.95584);