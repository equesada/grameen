<?php
require "conf.php";
require "func.php";

$lat = isset($_GET["gps_lat"]) ? str_clean($_GET["gps_lat"],1) : "0";
$lng = isset($_GET["gps_lng"]) ? str_clean($_GET["gps_lng"],1) : "0";

$sql = "select location.*, sqrt(pow((loc_lat-".$lat."),2) + pow((loc_lng-".$lng."),2)) as distance  from location where loc_lat<>0 order by distance limit 1;";
//echo $sql;
$sql = mysql_query($sql) OR die(output(mysql_error()));

$arr = array();
$rs = mysql_fetch_assoc($sql);

$arr["loc_id"] = $rs["loc_id"];
$arr["loc_type"] = $rs["loc_type"];
$arr["parent_id"] = $rs["parent_id"];
$arr["name"] = $rs["name"];
$arr["loc_lat"] = $rs["loc_lat"];
$arr["loc_lng"] = $rs["loc_lng"];
$arr["zipcode"] = $rs["zipcode"];
$arr["status"] = $rs["status"];
$arr["country_id"] = $rs["country_id"];


echo output($arr);

?>