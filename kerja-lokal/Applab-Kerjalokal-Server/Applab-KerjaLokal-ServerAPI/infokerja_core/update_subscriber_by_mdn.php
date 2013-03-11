<?php
require "conf.php";
require "func.php";
	
//$number_mdn = isSet($_GET['number_mdn']) ? str_clean($_GET['number_mdn']) : "";
$number_mdn = isset($_GET["mdn"]) ? str_clean($_GET["mdn"]) : 0;

//$sql = "DELETE FROM $t_subscribers WHERE mdn=\"$number_mdn\"";
$sql = "UPDATE $t_subscribers SET status=1 WHERE mdn=\"$number_mdn\"";
//die($sql);
$r = mysql_query($sql) OR die(output(mysql_error()));

echo output(1);

?>