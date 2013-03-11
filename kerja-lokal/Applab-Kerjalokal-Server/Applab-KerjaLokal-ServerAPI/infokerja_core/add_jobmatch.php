<?php
require "conf.php";
require "func.php";
	
$data = array();

foreach ($_GET as $key => $value)
{ $data[$key] = str_clean($value); }
unset($data["jobmatch_id"]); 

//echo "<pre>"; print_r($_GET); echo "</pre>";
//echo "<pre>"; print_r($data); echo "</pre>";

$var_k = "";
$var_v = "";
foreach ($data as $key => $value)
{ 
	$var_k .= $key.", "; 
	$var_v .= "\"$value\", "; 
}
$var_k = substr($var_k,0, strlen($var_k)-2);
$var_v = substr($var_v,0, strlen($var_v)-2);

$sql = "INSERT INTO $t_job_match ($var_k) VALUES ($var_v)";
//die($sql);
$r = mysql_query($sql) OR die(output(mysql_error()));

echo output(1);

?>