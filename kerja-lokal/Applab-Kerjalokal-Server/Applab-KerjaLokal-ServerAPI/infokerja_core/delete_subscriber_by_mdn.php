<?php
require "conf.php";
require "func.php";
	
$number_mdn = isset($_GET["mdn"]) ? str_clean($_GET["mdn"]) : 0;

$sql = "DELETE FROM $t_subscribers WHERE mdn=\"$number_mdn\"";
$r = mysql_query($sql) OR die(output(mysql_error()));

echo output(1);

?>