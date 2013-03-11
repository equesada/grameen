<?php
require "conf.php";
require "func.php";
/*
$id = isset($_GET["id"]) ? str_clean($_GET["id"]) : "ID";
$sql = "SELECT * FROM $t_manual WHERE id='$id'";
$sql = mysql_query($sql);

$arr = array();
while ($rs = mysql_fetch_assoc($sql))
{
	$city = array();
	$city["title"] = $rs["title"];
	//echo $city["title"];
	$city["text"] = $rs["text"];
	
	array_push($arr, $city);
}

echo output($arr);
*/
$id = isset($_GET["id"]) ? str_clean(($_GET["id"]),1) : 0;
$sql = "SELECT * FROM $t_manual WHERE id='$id'";
//$sql = "SELECT * FROM jobs_category";
//die($sql);
$sql = mysql_query($sql) OR die(output(mysql_error()));
$arr['nfields'] = mysql_num_fields($sql);
$row = mysql_fetch_assoc($sql);
for($j=0;$j<$arr['nfields'];$j++)
{
	$arr[mysql_field_name($sql,$j)] = $row[mysql_field_name($sql,$j)];
}
echo output($arr);
?>