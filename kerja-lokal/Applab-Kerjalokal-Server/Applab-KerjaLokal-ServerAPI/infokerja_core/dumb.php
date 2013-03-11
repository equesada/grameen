<?php
require "conf.php";
require "func.php";

$sql = "UPDATE jobs SET loc_id='1432013043' WHERE loc_id='49'";
$sql = mysql_query($sql) OR die(output(mysql_error()));
$sql = "UPDATE companies SET loc_id='1432013043' WHERE loc_id='49'";
$sql = mysql_query($sql) OR die(output(mysql_error()));
$sql = "UPDATE subscribers SET loc_id='1432013043' WHERE loc_id='49'";
$sql = mysql_query($sql) OR die(output(mysql_error()));

$sql = "UPDATE jobs SET loc_id='1741211176' WHERE loc_id='37'";
$sql = mysql_query($sql) OR die(output(mysql_error()));
$sql = "UPDATE companies SET loc_id='1741211176' WHERE loc_id='37'";
$sql = mysql_query($sql) OR die(output(mysql_error()));
$sql = "UPDATE subscribers SET loc_id='1741211176' WHERE loc_id='37'";
$sql = mysql_query($sql) OR die(output(mysql_error()));

$sql = "UPDATE jobs SET loc_id='164235811' WHERE loc_id='35'";
$sql = mysql_query($sql) OR die(output(mysql_error()));
$sql = "UPDATE companies SET loc_id='164235811' WHERE loc_id='35'";
$sql = mysql_query($sql) OR die(output(mysql_error()));
$sql = "UPDATE subscribers SET loc_id='164235811' WHERE loc_id='35'";
$sql = mysql_query($sql) OR die(output(mysql_error()));

?>