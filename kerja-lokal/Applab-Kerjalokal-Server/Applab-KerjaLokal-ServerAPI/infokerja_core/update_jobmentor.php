<?php
require "conf.php";
require "func.php";
	
$data = array();
$del_key = array("tx_id", "mentor_id");
$mentor_id = str_clean($_GET["mentor_id"],1);
$mdn = str_clean($_GET["mdn"],1);

foreach ($_GET as $key => $value)
{ if (!in_array($key, $del_key)) $data[$key] = str_clean($value); }

//echo "<pre>"; print_r($_GET); echo "</pre>";
//echo "<pre>"; print_r($data); echo "</pre>";

$var = "";
foreach ($data as $key => $value)
{ $var .= $key."=\"".$value."\", "; }
$var = substr($var,0, strlen($var)-2);

$sql = "UPDATE $t_mentors SET $var WHERE mentor_id=\"$mentor_id\"";
//die($sql);
$r = mysql_query($sql) OR die(output(mysql_error()));

echo output(1);
/*
$sql = "SELECT * FROM $t_mentors WHERE mdn=\"$mdn\"";
$sql = mysql_query($sql) OR die(output(mysql_error()));
if (mysql_num_rows($sql) > 0)
	echo output("Phone number has already exists in database");
else
{
	$sql = "UPDATE $t_subscribers SET $var WHERE mentor_id=\"$mentor_id\"";
	$r = mysql_query($sql) OR die(output(mysql_error()));
    //print_r($sql);

}
*/
?>