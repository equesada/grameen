<?php
require "conf.php";
require "func.php";

$id = isset($_GET["id"]) ? str_clean($_GET["id"]) : "ID";
$country_id = isset($_GET["country_id"]) ? str_clean(strtoupper($_GET["country_id"])) : "ID";

if (!is_numeric($id))
{
	$sql = "SELECT * FROM $t_help WHERE country_id='$id'";
	$sql = mysql_query($sql);
}
else
{
	$sql = "SELECT * FROM $t_help WHERE help_id='$id' AND country_id='$country_id' ORDER BY help_id";
	$sql = mysql_query($sql);
}

$arr = array();
while ($rs = mysql_fetch_assoc($sql))
{
	$city = array();
	$city["help_id"] = $rs["help_id"];
	$city["help_title"] = $rs["help_title"];
	$city["description"] = $rs["description"];
	$city["status"] = $rs["status"];
	$city["date_add"] = $rs["date_add"];
	$city["date_update"] = $rs["date_update"];
	$city["country_id"] = $rs["country_id"];
	
	array_push($arr, $city);
}

echo output($arr);

?>