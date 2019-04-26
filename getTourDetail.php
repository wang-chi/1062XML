<?php
require("phpsqlajax_dbinfo.php");
$name = $_POST['name'];
// Opens a connection to a MySQL server
$connection=mysqli_connect ('localhost', $username, $password,$database);
mysqli_set_charset($connection,"utf8");
if (!$connection) {
  die('Not connected : ' . mysql_error());
}

$sql = "SELECT @rownum := @rownum + 1 AS rowid, TrainNo, ArrivalTime, DepartureTime, TourName, tourdate FROM tour,(SELECT @rownum := 0) r
 WHERE StationName= '$name'";
$result = mysqli_query($connection,$sql);
if (!$result) {
  die('Invalid query: ' . mysql_error());
}
$res = "";//把準備回傳的變數res準備好
while($data=mysqli_fetch_assoc($result)){
    // $res .= "<a class='dropdown-item' value='{$data["ID"]}'>{$data['StationName']}</option>";
    $res .= "<tr><th scope='row'>".$data['rowid']."</th><td>".$data['TrainNo']."</td><td>".$data['TourName']."</td><td>".$data['ArrivalTime']."</td><td>".$data['DepartureTime']."</td><td>".$data['tourdate']."</td></tr>";
};
echo $res;//將型號項目丟回給ajax
?>