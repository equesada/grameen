<?php

	require "conf.php";
	require "func.php";

	$country_id = isset($_GET["country_id"]) ? str_clean(strtoupper($_GET["country_id"])) : "ID";
    $subscriber_id = isset($_GET["subscriber_id"]) ? str_clean($_GET["subscriber_id"]) : 0;


	$sql = "SELECT * FROM $t_subscribers WHERE country_id='$country_id' AND subscriber_id='$subscriber_id'";
	//die($sql);
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	$arr['nfields'] = mysql_num_fields($sql);
	$row = mysql_fetch_assoc($sql);
	
	for($j=0;$j<$arr['nfields'];$j++)
	{
		$arr[mysql_field_name($sql,$j)] = $row[mysql_field_name($sql,$j)];
	}

	$sql = "SELECT name, parent_id, loc_lat, loc_lng FROM $t_location WHERE loc_id='".$arr["loc_id"]."'";
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	$row = mysql_fetch_assoc($sql);
	$arr["kelurahan_name"] = $row["name"];
	$arr["kelurahan_lat"] = $row["loc_lat"];
	$arr["kelurahan_lng"] = $row["loc_lng"];

	$kecamatan_id = substr($arr["loc_id"],5);
	//echo $kecamatan_id."<hr>";
	$sql = "SELECT name, parent_id FROM $t_location WHERE loc_id='$kecamatan_id'";
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	$row = mysql_fetch_assoc($sql);
	$arr["kecamatan_id"] = $kecamatan_id;
	$arr["kecamatan_name"] = $row["name"];
	
	$kotamadya_id = $row["parent_id"];
	$sql = "SELECT name, parent_id FROM $t_location WHERE loc_id='$kotamadya_id'";
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	$row = mysql_fetch_assoc($sql);
	$arr["kotamadya_id"] = $kotamadya_id;
	$arr["kotamadya_name"] = $row["name"];
	
	$province_id = $row["parent_id"];
	$sql = "SELECT name, parent_id FROM $t_location WHERE loc_id='$province_id'";
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	$row = mysql_fetch_assoc($sql);
	$arr["province_id"] = $province_id;
	$arr["province_name"] = $row["name"];

	$arr['nfields'] = $arr['nfields']+7;
	
	echo output($arr);

?>