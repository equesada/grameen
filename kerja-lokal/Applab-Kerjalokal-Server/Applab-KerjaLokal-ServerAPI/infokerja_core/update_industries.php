<?php
require "conf.php";
require "func.php";

$data = array();
$del_key = array("industry_id", "tx_id");
$industry_id = str_clean($_GET["industry_id"]);


foreach ($_GET as $key => $value)
{
	if (!in_array($key, $del_key)) $data[$key] = str_clean($value); 
}

//echo "<pre>"; print_r($_GET); echo "</pre>";

// $industry_id = $data["industry_id"];
// unset ($data["industry_id"]);

$var = "";
foreach ($data as $key => $value)
{ $var .= $key."='".$value."', "; }
$var = substr($var, 0, strlen($var)-2);


$sql = "UPDATE $t_industry SET $var WHERE industry_id=\"$industry_id\"";
//die($sql);
$sql = mysql_query($sql) OR die(output(mysql_error()));
echo output(1);

?>