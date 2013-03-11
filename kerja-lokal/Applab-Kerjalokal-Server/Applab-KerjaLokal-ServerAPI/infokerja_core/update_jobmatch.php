<?php
require "conf.php";
require "func.php";
	
$data = array();
$jobmatch_id = str_clean($_GET["jobmatch_id"]);

$del_key = array("tx_id", "jobmatch_id");
foreach ($_GET as $key => $value)
{ if (!in_array($key, $del_key)) $data[$key] = str_clean($value); }

//echo "<pre>"; print_r($_GET); echo "</pre>";
//echo "<pre>"; print_r($data); echo "</pre>";

$var = "";
foreach ($data as $key => $value)
{ $var .= $key."=\"".$value."\", "; }
//{ $var .= $key."='".$value."', "; }
$var = substr($var,0, strlen($var)-2);

$sql = "UPDATE $t_job_match SET $var WHERE jobmatch_id=\"$jobmatch_id\"";
//echo $sql;
$r = mysql_query($sql) OR die(output(mysql_error()));

echo output(1);

?>