<?php
require_once "conf.php";
require_once "func.php";

$data = array();
$del_key = array("tx_id");
$_GET["pin"] = isset($_GET["pin"]) ? str_clean($_GET["pin"]) : mt_rand(100000, 999999);
foreach ($_GET as $key => $value)
{ 
	if ($key == "lat") $key = "pos_lat";
	if ($key == "lng") $key = "pos_lng";
	if (!in_array($key, $del_key)) $data[$key] = str_clean($value); 
}

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

$sql = "INSERT INTO $t_mentors ($var_k) VALUES ($var_v)";
$r = mysql_query($sql) OR die(output(mysql_error()));

	$subscriber_id = mysql_insert_id();
	$data = array(
		"subscriber_id" => $subscriber_id,
		"pin" => $_GET["pin"],
		"status" => "1"
	);
	echo output($data);

?>