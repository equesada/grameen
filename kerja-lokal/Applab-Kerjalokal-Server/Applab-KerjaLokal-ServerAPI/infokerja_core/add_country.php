<?php
require "conf.php";
require "func.php";

$data = array();
$country_id = str_clean($_GET["country_id"]);

$del_key = array("tx_id");
foreach ($_GET as $key => $value)
{ if (!in_array($key, $del_key)) $data[$key] = str_clean($value); }

$data["date_update"] = "";
$data["is_current"] = "0";

$sql = "SELECT * FROM $t_country WHERE country_id='$country_id'";
$r = mysql_query($sql) OR die(output(mysql_error()));
if (mysql_num_rows($r) > 0)
	echo output("Country ID has already exists.");
else
{
	$var_k = "";
	$var_v = "";
	foreach ($data as $key => $value)
	{ 
		$var_k .= $key.", "; 
		$var_v .= "\"$value\", "; 
	}
	$var_k = substr($var_k,0, strlen($var_k)-2);
	$var_v = substr($var_v,0, strlen($var_v)-2);

	$sql = "INSERT INTO $t_country ($var_k) VALUES ($var_v)";
	//die($sql);
	$r = mysql_query($sql) OR die(output(mysql_error()));
/*
	$sql = "INSERT INTO $t_country (country_id, country_name, date_add) VALUES (\"$country_id\", \"$country_name\", \"$date_add\")";
	$r = mysql_query($sql) OR die(output(mysql_error()));
*/

	echo output(1);
}
?>