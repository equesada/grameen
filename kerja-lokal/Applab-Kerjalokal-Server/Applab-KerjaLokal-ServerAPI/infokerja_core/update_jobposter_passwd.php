<?php
require "conf.php";
require "func.php";

$data = array();
foreach ($_GET as $key => $value)
{ $data[$key] = str_clean($value); }

//echo "<pre>"; print_r($data); echo "</pre>";

$jobposter_id = $data["jobposter_id"];
$lookup = $data["lookup"];
unset ($data["jobposter_id"]);
unset ($data["lookup"]);
$var = "";
foreach ($data as $key => $value)
{ $var .= $key."='".$value."', "; }
$var = substr($var, 0, strlen($var)-2);
$sql = "SELECT * FROM $t_job_posters WHERE username='$lookup' or email='$lookup'";
$r = mysql_query($sql) OR die(output(mysql_error()));
if (mysql_num_rows($r) > 0 )
{
	$sql = "UPDATE $t_job_posters SET $var WHERE jobposter_id='$jobposter_id'";
	//die($sql);
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	echo output(1);	
} else {
	die(output("Username or email not exists"));
}
?>