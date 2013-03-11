<?php
require "conf.php";
require "func.php";

$sql = "SELECT * FROM $t_location WHERE loc_type='KOTA' ORDER BY name";
$sql = mysql_query($sql) OR die(output(mysql_error()));
$arr = array();
while ($rs = mysql_fetch_assoc($sql))
{
	$val = array();
	$val["loc_id"] = $rs["loc_id"];
	$val["loc_type"] = $rs["loc_type"];
	$val["parent_id"] = $rs["parent_id"];
	$val["name"] = $rs["name"];
	$val["loc_lat"] = $rs["loc_lat"];
	$val["loc_lng"] = $rs["loc_lng"];
	$val["status"] = $rs["status"];
	array_push($arr, $val);
}
echo output($arr);
?>