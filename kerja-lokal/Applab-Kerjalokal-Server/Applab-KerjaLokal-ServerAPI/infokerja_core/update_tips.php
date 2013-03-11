<?php
require "conf.php";
require "func.php";

$data = array();
$del_key = array("tips_id", "tx_id");
$tips_id = str_clean($_GET["tips_id"]);


foreach ($_GET as $key => $value)
{ 
	if (!in_array($key, $del_key)) $data[$key] = str_clean($value); 
}

//echo "<pre>"; print_r($_GET); echo "</pre>";

// $tips_id = $data["tips_id"];
// unset ($data["tips_id"]);

$var = "";
foreach ($data as $key => $value)
{ $var .= $key."='".$value."', "; }
$var = substr($var, 0, strlen($var)-2);


$sql = "UPDATE $t_tips SET $var WHERE tips_id=\"$tips_id\"";
//die($sql);
$sql = mysql_query($sql) OR die(output(mysql_error()));
echo output(1);

?>