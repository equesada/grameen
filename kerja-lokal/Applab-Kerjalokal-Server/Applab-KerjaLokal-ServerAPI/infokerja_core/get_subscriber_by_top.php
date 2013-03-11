<?php
require "conf.php";
require "func.php";

$id = isset($_GET["id"]) ? str_clean($_GET["id"],1) : "1";
$mentor_id = isset($_GET["mentor_id"]) ? str_clean($_GET["mentor_id"]) : 0;

$sql = "SELECT subscriber_id FROM $t_subscribers WHERE mentor_id='$mentor_id' ORDER BY subscriber_id DESC LIMIT 1";
//die($sql);
$sql = mysql_query($sql) OR die(output(mysql_error()));

$arr = array();
$rs = mysql_fetch_assoc($sql);

$arr["subscriber_id"] = $rs["subscriber_id"];

echo output($arr);

?>