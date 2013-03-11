<?php
require "conf.php";
require "func.php";
	
$rel_id = isSet($_GET['rel_id']) ? str_clean($_GET['rel_id']) : "";

//$sql = "DELETE FROM $t_subscribers WHERE mdn=\"$number_mdn\"";
$sql = "UPDATE $t_rel_subscriber_cat SET status=1 WHERE rel_id=\"$rel_id\"";
$r = mysql_query($sql) OR die(output(mysql_error()));

echo output(1);

?>