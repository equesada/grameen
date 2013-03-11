<?php
require "conf.php";
require "func.php";

$data = array();
foreach ($_GET as $key => $value)
{ $data[$key] = str_clean($value); }

//echo "<pre>"; print_r($data); echo "</pre>";

$jobposter_id = $data["jobposter_id"];
$username = $data["username"];
unset ($data["jobposter_id"]);
unset ($data["username"]);

$var = "";
foreach ($data as $key => $value)
{ $var .= $key."='".$value."', "; }
$var = substr($var, 0, strlen($var)-2);

$sql = "SELECT * FROM $t_job_posters WHERE username='".$data["username"]."'";
$r = mysql_query($sql) OR die(output(mysql_error()));
if (mysql_num_rows($r) > 0)
	die(output("Username is already exists"));
else
{
	$sql = "UPDATE $t_job_posters SET $var WHERE jobposter_id='$jobposter_id'";
	//die($sql);
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	echo output(1);
}
?>