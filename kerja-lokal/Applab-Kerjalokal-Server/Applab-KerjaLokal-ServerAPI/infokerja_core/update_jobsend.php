<?php
require "conf.php";
require "func.php";
	
$data = array();
$del_key = array("jobsend_id", "tx_id");
$jobsend_id = str_clean($_GET["jobsend_id"]);

foreach ($_GET as $key => $value)
{ 
	if (!in_array($key, $del_key)) $data[$key] = str_clean($value); 
}

//echo "<pre>"; print_r($_GET); echo "</pre>";
//echo "<pre>"; print_r($data); echo "</pre>";

$var = "";
foreach ($data as $key => $value)
{ 
	$var .= $key."=\"".$value."\", "; 
}
$var = substr($var,0, strlen($var)-2);

$sql = "UPDATE $t_jobs_send SET $var WHERE jobsend_id=\"$jobsend_id\"";
//die($sql);
$r = mysql_query($sql) OR die(output(mysql_error()));

echo output(1);

?>