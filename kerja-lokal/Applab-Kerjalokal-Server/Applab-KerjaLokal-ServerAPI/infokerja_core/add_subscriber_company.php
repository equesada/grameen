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

$sql = "INSERT INTO $t_rel_subscriber_company ($var_k) VALUES ($var_v)";
//die($sql);
$r = mysql_query($sql) OR die(output(mysql_error()));

echo output(1);

?>