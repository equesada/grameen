<?php
require "conf.php";
require "func.php";
$arr = array();
	
$subscriber_id = isset($_GET['subscriber_id']) ? str_clean($_GET['subscriber_id']) : "";
$jobcat_id = isset($_GET['jobcat_id']) ? str_clean($_GET['jobcat_id']) : "";
$date_add = isset($_GET['date_add']) ? str_clean($_GET['date_add']) : date("Y-m-d H:i:s");
$date_active = isset($_GET['date_active']) ? str_clean($_GET['date_active']) : date("Y-m-d", (strtotime($date_add) + 86400));
$date_expired = isset($_GET['date_expired']) ? str_clean($_GET['date_expired']) : date("Y-m-d", (strtotime($date_add) + 7*86400));
$status = isset($_GET['status']) ? str_clean($_GET['status'],1) : "1";

$jobcat_key = isset($_GET['jobcat_key']) ? str_clean($_GET['jobcat_key']) : "";

//$date_add = date("Y-m-d H:i:s");

$sql = "SELECT * FROM $t_rel_subscriber_cat WHERE subscriber_id='$subscriber_id' AND jobcat_id='$jobcat_id' AND ";
$sql .= "status = '1'";		


//die($sql);


//$sql .= "date_active='$date_active' AND date_expired='$date_expired'";
// echo $sql;
$r = mysql_query($sql) OR die(output(mysql_error()));

if (mysql_num_rows($r) > 0)
{
	$r = mysql_fetch_assoc($r);
	if ($date_add >= $r["date_expired"])
	{
		$sql = "INSERT INTO $t_rel_subscriber_cat (subscriber_id, jobcat_id, date_add, date_active, date_expired, status) VALUES ";
		$sql .= "(\"$subscriber_id\", \"$jobcat_id\", \"$date_add\", \"$date_active\", \"$date_expired\", \"$status\")";
		//die($sql);
		$r = mysql_query($sql) OR die(output(mysql_error()));

		$rel_id = mysql_insert_id();
		$data = array(
			"rel_id" => $rel_id,
			"status" => "1"
		);
		echo output($data);
	}
	else
	{
		$arr["status"] = "0";
		$arr["msg"] = "Subscription is already exists and still valid until ".$r["date_expired"];
		echo output($arr);
	}
}
else
{
	//echo "<pre>"; print_r($_GET); echo "</pre>";

	$sql = "INSERT INTO $t_rel_subscriber_cat (subscriber_id, jobcat_id, jobcat_key, date_add, date_active, date_expired, status) VALUES ";
	$sql .= "(\"$subscriber_id\", \"$jobcat_id\", \"$jobcat_key\", \"$date_add\", \"$date_active\", \"$date_expired\", \"$status\")";
    //die($sql);
	$r = mysql_query($sql) OR die(output(mysql_error()));

	$rel_id = mysql_insert_id();
	$data = array(
		"rel_id" => $rel_id,
		"status" => "1"
	);
	echo output($data);
}
?>