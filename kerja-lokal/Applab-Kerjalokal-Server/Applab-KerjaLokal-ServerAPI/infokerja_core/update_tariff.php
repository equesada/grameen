<?php
require "conf.php";
require "func.php";
	
$tarif = str_clean($_GET["tarif"]);

$sql = "UPDATE $t_tarif SET tarif='$tarif'";

$r = mysql_query($sql) OR die(output(mysql_error()));

echo output(1);

?>