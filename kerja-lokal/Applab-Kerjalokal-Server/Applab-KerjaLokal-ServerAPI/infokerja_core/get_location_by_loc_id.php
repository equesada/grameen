<?php
require "conf.php";
require "func.php";

$id = isset($_GET["id"]) ? str_clean($_GET["id"],1) : "1";

$sql = "SELECT * FROM $t_location WHERE loc_id='$id'";
//die($sql);
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