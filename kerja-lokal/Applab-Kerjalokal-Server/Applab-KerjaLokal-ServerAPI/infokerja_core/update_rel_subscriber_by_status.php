<?php
require "conf.php";
require "func.php";

$subscriber_id = isSet($_GET['subscriber_id']) ? str_clean($_GET['subscriber_id']) : "";
//$status = isset($_GET["status"]) ? str_clean($_GET["status"],2) : "";
$status = isSet($_GET['status']) ? str_clean($_GET['status']) : "";

$sql = "UPDATE $t_rel_subscriber_cat SET status=\"$status\" WHERE subscriber_id=\"$subscriber_id\"";
//die($sql);
$r = mysql_query($sql) OR die(output(mysql_error()));

echo output(1);

?>