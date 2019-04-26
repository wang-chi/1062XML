<?php
require("phpsqlajax_dbinfo.php");
$area = $_POST['area'];
// Opens a connection to a MySQL server
$connection=mysqli_connect ('localhost', $username, $password,$database);
mysqli_set_charset($connection,"utf8");
if (!$connection) {
  die('Not connected : ' . mysql_error());
}

$sql = "SELECT ID, StationName FROM station WHERE city= '$area'";
$result = mysqli_query($connection,$sql);
// $result = mysqli_query($link, $sql) or die("取出資料失敗！".mysqli_error($link));
if (!$result) {
  die('Invalid query: ' . mysql_error());
}
$res = "";//把準備回傳的變數res準備好
while($data=mysqli_fetch_assoc($result)){
    $res .= "<a class='dropdown-item' value='{$data["ID"]}'>{$data['StationName']}</option>";
};
echo $res;//將型號項目丟回給ajax
?>