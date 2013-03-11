<?php
require "conf.php";
require "func.php";
	
$id = isSet($_GET['id']) ? str_clean($_GET['id']) : "";

$sql = "DELETE FROM $t_subscribers WHERE subscriber_id=\"$id\"";
$r = mysql_query($sql) OR die(output(mysql_error()));

echo output(1);

?>