<?php
require "conf.php";
require "func.php";

$title = isset($_GET['title']) ? str_clean($_GET['title']) : "";
$desc = isset($_GET['desc']) ? str_clean($_GET['desc']) : "";
$creator = isset($_GET['creator']) ? str_clean($_GET['creator']) : "";
$is_active = isset($_GET['is_active']) ? str_clean($_GET['is_active']) : "";
$is_current = isset($_GET['is_current']) ? str_clean($_GET['is_current']) : "";
$country_id = isset($_GET['country_id']) ? str_clean($_GET['country_id']) : "";
$status_id = isset($_GET['status_id']) ? str_clean($_GET['status_id']) : "";
$date_update = date("Y-m-d H:i:s");


$sql = "INSERT INTO $t_themes (id, name, date_add, date_update, creator, folder, is_active, is_current, country_id) VALUES ";
$sql .= "(\"\", \"$title\", \"$date_update\", \"$date_update\", \"$creator\", \"$desc\", \"$is_active\", \"$is_current\", \"$country_id\")";
$r = mysql_query($sql) OR die(output(mysql_error()));

echo output(1);

?>