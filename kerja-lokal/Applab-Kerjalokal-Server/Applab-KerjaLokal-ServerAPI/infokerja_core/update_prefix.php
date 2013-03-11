<?php
require "conf.php";
require "func.php";
	
$opprefix = str_clean($_GET["opprefix"]);

$sql = "UPDATE $t_tb_prefix SET opprefix='$opprefix'";

$r = mysql_query($sql) OR die(output(mysql_error()));

echo output(1);

?>