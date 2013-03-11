<?php
require "conf.php";
require "func.php";

$data = array();
$del_key = array("tx_id", "jobposter_id");
$rel_id = str_clean($_GET["rel_id"],1);
$status = str_clean($_GET["status"],1);
$data["update_by"] = str_clean($_GET["jobposter_id"],1);

foreach ($_GET as $key => $value)
{ if (!in_array($key, $del_key)) $data[$key] = str_clean($value); }

//echo "<pre>"; print_r($_GET); echo "</pre>";
//echo "<pre>"; print_r($data); echo "</pre>";

$var = "";
foreach ($data as $key => $value)
{ $var .= $key."=\"".$value."\", "; }
$var = substr($var,0, strlen($var)-2);

$sql = "UPDATE $t_rel_subscriber_cat SET $var WHERE rel_id=\"$rel_id\"";
$r = mysql_query($sql) OR die(output(mysql_error()));

echo output(1);


?>