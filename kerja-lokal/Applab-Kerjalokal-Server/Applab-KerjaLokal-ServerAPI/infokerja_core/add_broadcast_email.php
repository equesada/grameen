<?php
require "conf.php";
require "func.php";
	
$data = array();
foreach ($_GET as $key => $value)
{ $data[$key] = str_clean($value); }
//$data["tx_id"] = substr($data["tx_id"],0,strpos($data["tx_id"],"_"));

$var_k = "";
$var_v = "";
foreach ($data as $key => $value)
{ 
	$var_k .= $key.", "; 
	$var_v .= "\"$value\", "; 
}
$var_k = substr($var_k,0, strlen($var_k)-2);
$var_v = substr($var_v,0, strlen($var_v)-2);


$sql = "INSERT INTO $t_broadcast_email ($var_k) VALUES ($var_v)";
//die($sql);
$r = mysql_query($sql) OR die(output(mysql_error()));

echo output(1);


?>