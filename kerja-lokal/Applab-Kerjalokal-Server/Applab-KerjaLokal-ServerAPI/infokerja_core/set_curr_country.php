<?php
require "conf.php";
require "func.php";
	
$id = isSet($_GET['id']) ? str_clean($_GET['id']) : "";
$date_update = date("Y-m-d H:i:s");

$sql = "UPDATE $t_country SET is_current=\"0\"";
$r = mysql_query($sql) OR die(output(mysql_error()));

$sql = "UPDATE $t_country SET is_current=\"1\" WHERE country_id=\"$id\"";
$r = mysql_query($sql) OR die(output(mysql_error()));

echo output(1);

?>