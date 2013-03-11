<?php
require "conf.php";
require "func.php";

$id = isset($_GET["id"]) ? str_clean($_GET["id"]) : "ID";
$country_id = isset($_GET["country_id"]) ? str_clean(strtoupper($_GET["country_id"])) : "ID";

if (!is_numeric($id))
{
	$sql = "SELECT * FROM $t_location WHERE country_id='$id' AND parent_id='0'";
	$sql = mysql_query($sql);
}
else
{
	$sql = "SELECT * FROM $t_location WHERE parent_id='$id' AND country_id='$country_id' ORDER BY name";
	$sql = mysql_query($sql);
}

$arr = array();
while ($rs = mysql_fetch_assoc($sql))
{
	$city = array();
	$city["loc_id"] = $rs["loc_id"];
	$city["loc_type"] = $rs["loc_type"];
	$city["parent_id"] = $rs["parent_id"];
	$city["name"] = $rs["name"];
	$city["loc_lat"] = $rs["loc_lat"];
	$city["loc_lng"] = $rs["loc_lng"];
	$city["zipcode"] = $rs["zipcode"];
	$city["status"] = $rs["status"];
	array_push($arr, $city);
}

echo output($arr);

?>