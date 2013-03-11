<?php
require "conf.php";
require "func.php";
$arr = array();
	
$subscriber_id = isset($_GET['subscriber_id']) ? str_clean($_GET['subscriber_id']) : "";
//$jobcat_id = isset($_GET['jobcat_id']) ? str_clean($_GET['jobcat_id']) : "";
$jobcat_key = isset($_GET['jobcat_key']) ? str_clean($_GET['jobcat_key']) : "";
$date_add = isset($_GET['date_add']) ? str_clean($_GET['date_add']) : date("Y-m-d H:i:s");
$date_active = isset($_GET['date_active']) ? str_clean($_GET['date_active']) : date("Y-m-d", (strtotime($date_add) + 86400));
$date_expired = isset($_GET['date_expired']) ? str_clean($_GET['date_expired']) : date("Y-m-d", (strtotime($date_add) + 7*86400));
$status = isset($_GET['status']) ? str_clean($_GET['status'],1) : "1";

$jobcat_id = "";
if($jobcat_key == "SALES"){
	$jobcat_id = 1;	
}else if($jobcat_key == "STAF"){
	$jobcat_id = 2;	
}else if($jobcat_key == "KASIR"){
	$jobcat_id = 3;	
}else if($jobcat_key == "BURUH"){
	$jobcat_id = 4;	
}else if($jobcat_key == "PABRIK"){
	$jobcat_id = 5;	
}else if($jobcat_key == "PEMBANTU"){
	$jobcat_id = 6;	
}else if($jobcat_key == "PELAYAN"){
	$jobcat_id = 7;	
}else if($jobcat_key == "MONTIR"){
	$jobcat_id = 8;	
}else if($jobcat_key == "SUPIR"){
	$jobcat_id = 9;	
}else if($jobcat_key == "SATPAM"){
	$jobcat_id = 10;	
}else if($jobcat_key == "PERAWAT"){
	$jobcat_id = 11;	
}else if($jobcat_key == "GURU"){
	$jobcat_id = 12;	
}else if($jobcat_key == "KOKI"){
	$jobcat_id = 13;	
}else if($jobcat_key == "LAINNYA"){
	$jobcat_id = 14;	
}


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