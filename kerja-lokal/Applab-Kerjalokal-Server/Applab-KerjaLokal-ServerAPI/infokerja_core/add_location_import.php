<?php
require "conf.php";
require "func.php";
	
$loc_id = isset($_GET['loc_id']) ? str_clean($_GET['loc_id']) : "0";
$country_id = isset($_GET['country_id']) ? str_clean($_GET['country_id']) : "ID";
$parent_id = isset($_GET['parent_id']) ? str_clean($_GET['parent_id']) : "0";
$type = isset($_GET['type']) ? str_clean($_GET['type']) : "KOTA";
$name = isset($_GET['name']) ? str_clean($_GET['name']) : "-";
$status = isset($_GET['status']) ? str_clean($_GET['status']) : 1;
$lat = isset($_GET['lat']) ? str_clean($_GET['lat']) : "";
$lng = isset($_GET['lng']) ? str_clean($_GET['lng']) : "";
$zipcode = isset($_GET['zipcode']) ? str_clean($_GET['zipcode']) : "";
$date_add = date("Y-m-d H:i:s");
	
	if($type !=  "KELURAHAN")
	{
		$sql = 'INSERT INTO '.$t_location.' (loc_id, loc_type, country_id, parent_id, name, status, date_add) VALUES ("'.$loc_id.'", "'.$type.'", "'.$country_id.'", "'.$parent_id.'", "'.$name.'", "'.$status.'", "'.$date_add.'")';
	}
	else
	{
		$sql = 'INSERT INTO '.$t_location.' (loc_id, loc_type, country_id, parent_id, name, loc_lat, loc_lng, status, date_add) VALUES ("'.$loc_id.'", "'.$type.'", "'.$country_id.'", "'.$parent_id.'", "'.$name.'", "'.$lat.'", "'.$lng.'", "'.$status.'", "'.$date_add.'")';	
	}
	$r = mysql_query($sql) OR die(output(mysql_error()));

	echo output(1);
	//echo "<pre>"; print_r(json_decode(output($r),1)); echo "</pre>";

?>