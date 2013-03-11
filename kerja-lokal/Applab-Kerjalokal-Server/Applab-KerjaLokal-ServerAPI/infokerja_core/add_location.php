<?php
require "conf.php";
require "func.php";
	
$country_id = isset($_GET['country_id']) ? str_clean($_GET['country_id']) : "ID";
$parent_id = isset($_GET['parent_id']) ? str_clean($_GET['parent_id']) : "0";
$type = isset($_GET['type']) ? str_clean($_GET['type']) : "KOTA";
$name = isset($_GET['name']) ? str_clean($_GET['name']) : "-";
$status = isset($_GET['status']) ? str_clean($_GET['status']) : 1;
$lat = isset($_GET['lat']) ? str_clean($_GET['lat']) : "";
$lng = isset($_GET['lng']) ? str_clean($_GET['lng']) : "";
$zipcode = isset($_GET['zipcode']) ? str_clean($_GET['zipcode']) : "";
$date_add = date("Y-m-d H:i:s");
	
$sql = "SELECT lower(name) FROM $t_location WHERE lower(name)='".strtolower($name)."' AND loc_type='".$type."'";
$r = mysql_query($sql) OR die(output(mysql_error()));

if (mysql_num_rows($r) == 0)
{
	if($type !=  "KELURAHAN")
	{
		$sql = 'INSERT INTO '.$t_location.' (loc_type, country_id, parent_id, name, status, date_add) VALUES ("'.$type.'", "'.$country_id.'", "'.$parent_id.'", "'.$name.'", "'.$status.'", "'.$date_add.'")';
	}
	else
	{
		$sql = 'INSERT INTO '.$t_location.' (loc_type, country_id, parent_id, name, loc_lat, loc_lng, status, date_add) VALUES ("'.$type.'", "'.$country_id.'", "'.$parent_id.'", "'.$name.'", "'.$lat.'", "'.$lng.'", "'.$status.'", "'.$date_add.'")';	
	}
	$r = mysql_query($sql) OR die(output(mysql_error()));

	echo output(1);
}
else
	echo output("The location has already exists in database");
?>