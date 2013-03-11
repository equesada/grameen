<?php

	require "conf.php";
	require "func.php";

	$country_id = isset($_GET["country_id"]) ? str_clean(strtoupper($_GET["country_id"])) : "ID";
	$mentor_id = isset($_GET["mentor_id"]) ? str_clean($_GET["mentor_id"]) : 0;
	$status = isset($_GET["status"]) ? str_clean($_GET["status"]) : 1;


	$sql = "SELECT * FROM $t_subscribers WHERE country_id='$country_id' AND mentor_id='$mentor_id' AND status='$status'";
	
    //die($sql);
    
    $sql = mysql_query($sql);
	$arr["totaldata"] = mysql_num_rows($sql);
	//$arr['ndata'] = $ndata == 0 ? $arr["totaldata"] : $ndata;		//EDIT BY YUDHA - KARENA DI DMS SUBSCRIBER TDK MUNCUL

	//die($sql);
	//$sql = "SELECT * FROM $t_subscribers WHERE country_id='$country_id' AND mentor_id='$mentor_id'";
	//$sql = mysql_query($sql) OR die(output(mysql_error()));
	//$arr['nfields'] = mysql_num_fields($sql);
	//$row = mysql_fetch_assoc($sql);
	
	$arr['nrows'] = mysql_num_rows($sql);
	$arr['nfields'] = mysql_num_fields($sql);
	//$arr['npage'] = $ndata > 0 ? ceil($arr["totaldata"] / $ndata) : 1;	//EDIT BY YUDHA - KARENA DI DMS SUBSCRIBER TDK MUNCUL
	//$arr['page'] = $page;													//EDIT BY YUDHA - KARENA DI DMS SUBSCRIBER TDK MUNCUL
	$arr['results'] = array();
    
	while($row = mysql_fetch_assoc($sql))      //DEFAULT!  
	{
		for($j=0;$j<$arr['nfields'];$j++)
		{
			$val[mysql_field_name($sql,$j)] = $row[mysql_field_name($sql,$j)];
		}
		array_push($arr["results"], $val);
	}
    
    /*
    $i = 0;
	while($row = mysql_fetch_assoc($sql))
	{
		for($j=0;$j<$arr['nfields'];$j++)
		{
			$val[mysql_field_name($sql,$j)] = $row[mysql_field_name($sql,$j)];
		}
		array_push($arr["results"], $val);
		$i++;
	}
    */
	echo output($arr);
    //echo "<pre>"; print_r(json_decode(output($arr),1)); echo "</pre>";
?>