<?php
require "conf.php";
require "func.php";
	
$subscriber_id = isset($_GET["subscriber_id"]) ? str_clean($_GET["subscriber_id"],1) : "";
$pulsa = isset($_GET["pulsa"]) ? str_clean($_GET["pulsa"],1) : "";

$sql = "SELECT * FROM $t_subscriber_pulsa WHERE subscriber_id='$subscriber_id'";
//die($sql);
$sql = mysql_query($sql) OR die(output(mysql_error()));
$r = mysql_fetch_assoc($sql);

$sisa = $r["pulsa"] - $pulsa;

if ($sisa < 0)
{   	
    echo output("Pulsa tidak cukup");
    //return json_decode($sql);
}
else
{
	$sql = "UPDATE $t_subscriber_pulsa SET pulsa='$sisa' WHERE subscriber_id='$subscriber_id'";
	$r = mysql_query($sql) OR die(output(mysql_error()));
	echo output(1);
}
//die($sql);

?>