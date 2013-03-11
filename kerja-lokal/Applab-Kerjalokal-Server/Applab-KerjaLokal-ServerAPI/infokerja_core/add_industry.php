<?php
require "conf.php";
require "func.php";

$title = isset($_GET['title']) ? str_clean($_GET['title']) : "";
$desc = isset($_GET['desc']) ? str_clean($_GET['desc']) : "";
$country_id = isset($_GET['country_id']) ? str_clean($_GET['country_id']) : "";
$status_id = isset($_GET['status_id']) ? str_clean($_GET['status_id']) : "";
$date_update = date("Y-m-d H:i:s");


$sql = "INSERT INTO $t_industry (industry_id, industry_title, description, date_add, date_update, status, country_id) VALUES ";
$sql .= "(\"\", \"$title\", \"$desc\", \"$date_update\", \"$date_update\", \"$status_id\", \"$country_id\")";
$r = mysql_query($sql) OR die(output(mysql_error()));

echo output(1);

?>