<?php

require "conf.php";
require "func.php";

$sql = "SELECT * FROM $t_themes WHERE is_current='1'";

$sql = mysql_query($sql) OR die(output(mysql_error()));

$r = mysql_fetch_assoc($sql);

$arr["folder"] = $r["folder"];

echo output($arr);

?>