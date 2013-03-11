<?php
require "conf.php";
require "func.php";

$key = isset($_GET["key"]) ? str_clean($_GET["key"]) : "";
$value = isset($_GET["value"]) ? str_clean($_GET["value"]) : "";
$update_key = isset($_GET["update_key"]) ? str_clean($_GET["update_key"]) : "";
$update_value = isset($_GET["update_value"]) ? str_clean($_GET["update_value"]) : "";

$sql = "UPDATE $t_rel_subscriber_cat";

if (($key != "") && ($value != "") && ($update_key != "") && ($update_value != ""))
{
	$sql .= " SET ";
	$key = explode("|",$key);
	$value = explode("|",$value);
	$update_key = explode("|",$update_key);
	$update_value = explode("|",$update_value);
	$a = 0;
	foreach ($update_key as $key2)
	{ 
		$sql .= $key2." = \"$update_value[$a]\""; 
		$a++;
		if ($a < sizeof($update_key)) $sql .= ", ";
	}
	$sql .= " WHERE ";
	$a = 0;
	foreach ($key as $key2)
	{ 
		$sql .= $key2." = \"$value[$a]\""; 
		$a++;
		if ($a < sizeof($key)) $sql .= " AND ";
	}
}

//die($sql);
$r = mysql_query($sql) OR die(output(mysql_error()));

echo output(1);

?>