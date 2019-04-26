<?php
require("phpsqlajax_dbinfo.php");
$name = $_POST['name'];
// Opens a connection to a MySQL server
$connection=mysqli_connect ('localhost', $username, $password,$database);
mysqli_set_charset($connection,"utf8");
if (!$connection) {
  die('Not connected : ' . mysql_error());
}

$sql = "SELECT lat, lng FROM station WHERE StationName= '$name'";
$result = mysqli_query($connection,$sql);
// $result = mysqli_query($link, $sql) or die("取出資料失敗！".mysqli_error($link));
if (!$result) {
  die('Invalid query: ' . mysql_error());
}
$res = "";//把準備回傳的變數res準備好
while($data=mysqli_fetch_assoc($result)){
    $LatLng[0] = $data['lat'];
    $LatLng[1] = $data['lng'];
};
echo $LatLng[0].",".$LatLng[1];
//print_r($LatLng);//將型號項目丟回給ajax
?>