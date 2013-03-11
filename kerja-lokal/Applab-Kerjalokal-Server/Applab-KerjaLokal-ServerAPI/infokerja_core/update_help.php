<?php
require "conf.php";
require "func.php";

$data = array();
$del_key = array("help_id", "tx_id");
$help_id = str_clean($_GET["help_id"]);


foreach ($_GET as $key => $value)
{ 
	if (!in_array($key, $del_key)) $data[$key] = str_clean($value); 
}

//echo "<pre>"; print_r($_GET); echo "</pre>";

// $help_id = $data["help_id"];
// unset ($data["help_id"]);

$var = "";
foreach ($data as $key => $value)
{ $var .= $key."='".$value."', "; }
$var = substr($var, 0, strlen($var)-2);


$sql = "UPDATE $t_help SET $var WHERE help_id=\"$help_id\"";
//die($sql);
$sql = mysql_query($sql) OR die(output(mysql_error()));
echo output(1);

?>