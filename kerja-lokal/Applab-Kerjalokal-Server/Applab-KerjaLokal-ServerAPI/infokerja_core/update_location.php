<?php
require "conf.php";
require "func.php";

/*
$id = isset($_GET['id']) ? str_clean($_GET['id']) : "";
$name = isset($_GET['name']) ? str_clean($_GET['name']) : "";
$type = isset($_GET['type']) ? str_clean($_GET['type']) : "KOTA";
$zipcode = isset($_GET['zipcode']) ? str_clean($_GET['zipcode']) : "";
$lat = isset($_GET['lat']) ? str_clean($_GET['lat']) : "";
$lng = isset($_GET['lng']) ? str_clean($_GET['lng']) : "";
$date_update = date("Y-m-d H:i:s");
*/

$data = array();
$del_key = array("tx_id", "loc_id");
$loc_id = str_clean($_GET["loc_id"]);

foreach ($_GET as $key => $value)
{ 
	if ($key == "lat") $key = "pos_lat";
	if ($key == "lng") $key = "pos_lng";
	if (!in_array($key, $del_key)) $data[$key] = str_clean($value); 
}

if (isset($_GET['kelurahan']))
	if (str_clean($_GET['kelurahan']) != "0")
		$data["loc_id"] = str_clean($_GET['kelurahan']);
	else
		if (isset($_GET['zip']))
			if (str_clean($_GET['zip']) != "0")
				$data["loc_id"] = str_clean($_GET['zip']);

//echo "<pre>"; print_r($_GET); echo "</pre>";
//echo "<pre>"; print_r($data); echo "</pre>";

$var = "";
foreach ($data as $key => $value)
{ $var .= $key."=\"".$value."\", "; }
$var = substr($var,0, strlen($var)-2);

$sql = "UPDATE $t_location SET $var WHERE loc_id=\"$loc_id\"";
//die($sql);
$r = mysql_query($sql) OR die(output(mysql_error()));

echo output(1);

/*

if($type ==  "KELURAHAN")
{
	$sql = "UPDATE $t_location SET name=\"$name\", date_update=\"$date_update\", loc_lat=\"$lat\", loc_lng=\"$lng\", zipcode=\"$zipcode\" WHERE loc_id=\"$id\"";
}
else
{
	$sql = "UPDATE $t_location SET name=\"$name\", date_update=\"$date_update\" WHERE loc_id=\"$id\"";
}

$r = mysql_query($sql) OR die(output(mysql_error()));

echo output(1);
*/
?>