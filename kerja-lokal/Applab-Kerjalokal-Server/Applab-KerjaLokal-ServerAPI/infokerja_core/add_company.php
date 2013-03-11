<?php
require "conf.php";
require "func.php";
	
$data = array();
$del_key = array("tx_id");
foreach ($_GET as $key => $value)
{ 
	if ($key == "lat") $key = "pos_lat";
	if ($key == "lng") $key = "pos_lng";
	if (!in_array($key, $del_key)) $data[$key] = str_clean($value); 
}
	
$var_k = "";
$var_v = "";
foreach ($data as $key => $value)
{ 
	$var_k .= $key.", "; 
	$var_v .= "\"$value\", "; 
}
$var_k = substr($var_k,0, strlen($var_k)-2);
$var_v = substr($var_v,0, strlen($var_v)-2);
 
$data=array();

$sql = "INSERT INTO $t_companies ($var_k) VALUES ($var_v)";
//die($sql);
//$sql = "SELECT comp_id FROM $t_companies ORDER BY comp_id DESC LIMIT 0,1"
//$r = mysql_query($sql) OR die(output(mysql_error()));
//$data["comp_id"] = r["comp_id"];
$r = mysql_query($sql) OR die(output(mysql_error()));

$data["comp_id"] = mysql_insert_id();
$data["status"] = "1";
echo output($data);


?>