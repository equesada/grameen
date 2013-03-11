<?php

	require "conf.php";
	require "func.php";

	$country_id = isset($_GET["country_id"]) ? str_clean(strtoupper($_GET["country_id"])) : "ID";
	$id = isset($_GET["id"]) ? str_clean(($_GET["id"]),1) : 0;


	$sql = "SELECT *, $t_jobs.pos_lat AS pos_lat, $t_jobs.pos_lng AS pos_lng, $t_jobs.status AS status, 
		$t_jobs.description AS description, $t_jobs.loc_id AS loc_id, $t_jobs.comp_id AS comp_id, 
		$t_jobs.date_add AS date_add 
		FROM ($t_jobs INNER JOIN $t_job_posters ON $t_jobs.jobposter_id = $t_job_posters.jobposter_id) 
		JOIN $t_companies ON $t_job_posters.comp_id = $t_companies.comp_id 
		WHERE $t_jobs.country_id='$country_id' AND job_id='$id'";
	//die($sql);
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	$arr['nfields'] = mysql_num_fields($sql);
	$row = mysql_fetch_assoc($sql);
	
	for($j=0;$j<$arr['nfields'];$j++)
	{
		$arr[mysql_field_name($sql,$j)] = $row[mysql_field_name($sql,$j)];
	}

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

	$arr['nfields'] = $arr['nfields']+6;
	

	echo output($arr);
	//echo "<pre>"; print_r(json_decode(output($arr), true)); echo "</pre>";

?>