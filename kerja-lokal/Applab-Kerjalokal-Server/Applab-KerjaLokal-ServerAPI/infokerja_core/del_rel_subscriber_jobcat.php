<?php
require "conf.php";
require "func.php";
	
$rel_id =  isset($_GET["id"]) ? str_clean($_GET["id"]) : "";

$sql = "DELETE FROM $t_rel_subscriber_cat WHERE rel_id=\"$rel_id\"";

$r = mysql_query($sql) OR die(output(mysql_error()));

echo output(1);

?>