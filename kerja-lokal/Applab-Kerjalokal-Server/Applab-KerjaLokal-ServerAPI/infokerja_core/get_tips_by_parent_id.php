<?php
require "conf.php";
require "func.php";

$id = isset($_GET["id"]) ? str_clean($_GET["id"]) : "ID";
$country_id = isset($_GET["country_id"]) ? str_clean(strtoupper($_GET["country_id"])) : "ID";

if (!is_numeric($id))
{
	$sql = "SELECT * FROM $t_tips WHERE country_id='$id'";
	$sql = mysql_query($sql);
}
else
{
	$sql = "SELECT * FROM $t_tips WHERE tips_id='$id' AND country_id='$country_id' ORDER BY tips_id";
	$sql = mysql_query($sql);
}

$arr = array();
while ($rs = mysql_fetch_assoc($sql))
{
	$city = array();
	$city["tips_id"] = $rs["tips_id"];
	$city["tips_title"] = $rs["tips_title"];
	$city["description"] = $rs["description"];
	$city["status"] = $rs["status"];
	$city["date_add"] = $rs["date_add"];
	$city["date_update"] = $rs["date_update"];
	$city["country_id"] = $rs["country_id"];
	
	array_push($arr, $city);
}

echo output($arr);

?>