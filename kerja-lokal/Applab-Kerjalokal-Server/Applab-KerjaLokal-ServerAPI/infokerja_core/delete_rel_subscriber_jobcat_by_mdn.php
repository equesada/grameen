<?php
require "conf.php";
require "func.php";
	
$number_mdn = isSet($_GET['number_mdn']) ? str_clean($_GET['number_mdn']) : "";

$sql = "DELETE FROM $t_rel_subscriber_cat WHERE mdn=\"$number_mdn\"";
$r = mysql_query($sql) OR die(output(mysql_error()));

echo output(1);

?>