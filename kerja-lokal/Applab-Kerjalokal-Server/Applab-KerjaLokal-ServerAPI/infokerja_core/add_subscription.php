<?php
require "conf.php";
require "func.php";
	
$data = array();
$del_key = array("tx_id");
foreach ($_GET as $key => $value)
{ 
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


$sql = "INSERT INTO $t_rel_subscriber_cat ($var_k) VALUES ($var_v)";
//die($sql);
$r = mysql_query($sql) OR die(output(mysql_error()));
$rel_id = mysql_insert_id();
$data = array(
		"rel_id" => $rel_id,
		"status" => "1"
	);
echo output($data);


?>