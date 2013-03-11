<?php
require "conf.php";
require "func.php";

$data = array();
$del_key = array("comp_id", "tx_id");
$comp_id = str_clean($_GET["comp_id"]);

foreach ($_GET as $key => $value)
{ 
	if ($key == "lat") $key = "pos_lat";
	if ($key == "lng") $key = "pos_lng";
	if (!in_array($key, $del_key)) $data[$key] = str_clean($value); 
}

//echo "<pre>"; print_r($_GET); echo "</pre>";

// $comp_id = $data["comp_id"];
// unset ($data["comp_id"]);

$var = "";
foreach ($data as $key => $value)
{ $var .= $key."='".$value."', "; }
$var = substr($var, 0, strlen($var)-2);


$sql = "UPDATE $t_companies SET $var WHERE comp_id=\"$comp_id\"";
//die($sql);
$sql = mysql_query($sql) OR die(output(mysql_error()));
echo output(1);

?>