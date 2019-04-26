<?php
require("phpsqlajax_dbinfo.php");
// 顯示火車站站點的markers XML
function parseToXML($htmlStr)
{
  
$xmlStr=str_replace('<','&lt;',$htmlStr);
$xmlStr=str_replace('>','&gt;',$xmlStr);
$xmlStr=str_replace('"','&quot;',$xmlStr);
$xmlStr=str_replace("'",'&#39;',$xmlStr);
$xmlStr=str_replace("&",'&amp;',$xmlStr);
return $xmlStr;
}

// Opens a connection to a MySQL server
$connection=mysqli_connect ('localhost', $username, $password,$database);
mysqli_set_charset($connection,"utf8");
if (!$connection) {
  die('Not connected : ' . mysql_error());
}

// // Set the active MySQL database

// $db_selected = mysqli_connect($database, $connection);
// if (!$db_selected) {
//   die ('Can\'t use db : ' . mysql_error());
// }

// Select all the rows in the markers table
$query = "SELECT d.stationid, m.`name`,COUNT(*) as 'airboxs', m.address, m.lat, m.lng, m.type, pm25.pm25_1,pm25.pm25_5, pm25.pm25_10
FROM distance d, markers m, station s
LEFT JOIN
(SELECT A1.sid,pm25_1,pm25_5,pm25_10
FROM
(SELECT a.id as aid, s.ID as sid, AVG(a.pm25) as pm25_1
FROM airbox as a, station as s,distance as d
WHERE s.ID = d.stationid AND a.id = d.airboxid AND d.`level` <=1
GROUP BY s.ID) as A1,
(SELECT a.id as aid, s.ID as sid, AVG(a.pm25) as pm25_5
FROM airbox as a, station as s,distance as d
WHERE s.ID = d.stationid AND a.id = d.airboxid AND d.`level` <=5
GROUP BY s.ID) as A5,
(SELECT a.id as aid, s.ID as sid, AVG(a.pm25) as pm25_10
FROM airbox as a, station as s,distance as d
WHERE s.ID = d.stationid AND a.id = d.airboxid AND d.`level` <=10
GROUP BY s.ID) as A10
WHERE A1.sid = A5.sid AND A5.sid = A10.sid) as pm25
ON  pm25.sid = s.ID
WHERE s.ID = d.stationid AND m.`name` = s.StationName 
GROUP BY d.stationid";
/*
$query = "SELECT d.stationid, m.`name`,COUNT(*) as 'airboxs', m.address, m.lat, m.lng, m.type
          FROM distance d, markers m, station s 
          WHERE s.ID = d.stationid AND m.`name` = s.StationName
          GROUP BY d.stationid";
          */
//$query = "SELECT id, name, address,lat,lng,type FROM markers";
$result = mysqli_query($connection,$query);
if (!$result) {
  die('Invalid query: ' . mysql_error());
}
header("Content-type: text/xml; charset=UTF-8");
// Start XML file, echo parent node
echo '<markers>';

// Iterate through the rows, printing XML nodes for each
while ($row = @mysqli_fetch_assoc($result)){
  // Add to XML document node
  echo '<marker ';
  echo 'stationid="' .$row['stationid']. '" ';
  echo 'name="' . parseToXML($row['name']). '" ';
  echo 'address="' . parseToXML($row['address']). '" ';
  echo 'lat="' . $row['lat'] . '" ';
  echo 'lng="' . $row['lng'] . '" ';
  echo 'type="' . $row['type'] . '" ';
  echo 'airbox="' . $row['airboxs'] . '" ';
  echo 'pm25_1="' . round($row['pm25_1'],3). '" ';
  echo 'pm25_5="' . round($row['pm25_5'],3) . '" ';
  echo 'pm25_10="' . round($row['pm25_10'],3). '" ';
  echo '/>';
}

// End XML file
echo '</markers>';

?>
