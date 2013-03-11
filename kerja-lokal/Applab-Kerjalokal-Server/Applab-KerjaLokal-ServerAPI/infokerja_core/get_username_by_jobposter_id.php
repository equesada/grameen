<?php

	require "conf.php";
	require "func.php";

	$jobposter_id = isset($_GET["jobposter_id"]) ? $_GET["jobposter_id"] : 0;
    $sql = "SELECT username FROM job_posters 
			where jobposter_id='$jobposter_id'";
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	$arr["nfields"] = mysql_num_fields($sql);
	$arr["totaldata"] = mysql_num_rows($sql); 
	$arr["results"] = array();
	$temp = array();
	while ($row = mysql_fetch_assoc($sql))
	{
		for($j=0;$j<$arr['nfields'];$j++)
		{
			$temp[mysql_field_name($sql,$j)] = $row[mysql_field_name($sql,$j)];
		}
		array_push($arr["results"], $temp);
	}
	
	echo output($arr);
	//echo "<pre>"; print_r(json_decode(output($arr),true)); echo "</pre>";

?>