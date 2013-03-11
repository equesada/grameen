<?php
require "conf.php";
require "func.php";

$data = array();
$del_key = array("tx_id");
$subscriber_id = str_clean($_GET["subscriber_id"],1);
//$update_by = str_clean($_GET["update_by"],1);
$status = str_clean($_GET["status"],2);

foreach ($_GET as $key => $value)
{ if (!in_array($key, $del_key)) $data[$key] = str_clean($value); }

echo "<pre>"; print_r($_GET); echo "</pre>";
echo "<pre>"; print_r($data); echo "</pre>";

$var = "";
foreach ($data as $key => $value)
{ $var .= $key."=\"".$value."\", "; }
$var = substr($var,0, strlen($var)-2);

$sql = "UPDATE $t_log_sms_reminder SET $var WHERE subscriber_id=\"$subscriber_id\" AND sms_type=\"$sms_type\"";
$r = mysql_query($sql) OR die(output(mysql_error()));

echo output(1);


?>