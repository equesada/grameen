<?php
require "conf.php";
require "func.php";

$id = isset($_GET["id"]) ? str_clean($_GET["id"],1) : "1";

$sql = "SELECT * FROM $t_location WHERE zipcode='$id'";
//die($sql);
$sql = mysql_query($sql) OR die(output(mysql_error()));

$arr = array();
$rs = mysql_fetch_assoc($sql);

$arr["zipcode"] = $rs["zipcode"];
$arr["loc_id"] = $rs["loc_id"];

echo output($arr);

?>