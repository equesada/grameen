<?php
require "conf.php";
require "func.php";
	
$data = array();
$country_id = str_clean($_GET['country_id']);

$del_key = array("tx_id", "country_id");
foreach ($_GET as $key => $value)
{ if (!in_array($key, $del_key)) $data[$key] = str_clean($value); }

/*
if ($data["new_id"] != "") $data["country_id"] = $data["new_id"];
unset($data["id"]);
unset($data["new_id"]);

$data["date_update"] = date("Y-m-d H:i:s");
*/
$var = "";
foreach ($data as $key => $value)
{ $var .= $key."=\"".$value."\", "; }
$var = substr($var,0, strlen($var)-2);

$sql = "UPDATE $t_country SET $var WHERE country_id=\"$country_id\"";
$r = mysql_query($sql) OR die(output(mysql_error()));

//die($sql);
echo output(1);

?>