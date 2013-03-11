<?php

require "conf.php";
require "func.php";

$sql = "SELECT * FROM $t_country WHERE is_current='1'";
$sql = mysql_query($sql) OR die(output(mysql_error()));
$r = mysql_fetch_assoc($sql);
$arr["country_id"] = $r["country_id"];
$arr["country_name"] = $r["country_name"];

echo output($arr);

?>