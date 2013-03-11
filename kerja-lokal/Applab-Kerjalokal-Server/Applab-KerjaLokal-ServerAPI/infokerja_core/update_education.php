<?php
require "conf.php";
require "func.php";

$data = array();
$del_key = array("edu_id", "tx_id");
$edu_id = str_clean($_GET["edu_id"]);


foreach ($_GET as $key => $value)
{ 
	if (!in_array($key, $del_key)) $data[$key] = str_clean($value); 
}

//echo "<pre>"; print_r($_GET); echo "</pre>";

// $edu_id = $data["edu_id"];
// unset ($data["edu_id"]);

$var = "";
foreach ($data as $key => $value)
{ $var .= $key."='".$value."', "; }
$var = substr($var, 0, strlen($var)-2);


$sql = "UPDATE $t_education SET $var WHERE edu_id=\"$edu_id\"";
//die($sql);
$sql = mysql_query($sql) OR die(output(mysql_error()));
echo output(1);

?>