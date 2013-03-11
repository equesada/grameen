<?php	
	
	require "conf.php";
	require "func.php";
	
	$id = isset($_GET["id"]) ? str_clean(($_GET["id"]),1) : 0;
	$sql = "SELECT mdn FROM $t_subscribers";
	//$sql = "SELECT * FROM jobs_category";
	//die($sql);
	$sql = mysql_query($sql) OR die(output(mysql_error()));	
	$arr["nfields"] = mysql_num_fields($sql);
	$arr["totaldata"] = mysql_num_rows($sql); 
	$arr["results"] = array();
	$temp = array();
	$jobcat_key = null;
	
	
	while ($row = mysql_fetch_assoc($sql))
	{
		for($j=0;$j<$arr['nfields'];$j++)
		{
			$temp[mysql_field_name($sql,$j)] = $row[mysql_field_name($sql,$j)];				
		}		

		array_push($arr["results"], $temp);						
		//print_r($new);
		//$jobcat_key .= $temp["jobcat_key"].'_'.$temp["date_expired"]."_";
	}	

	echo output($arr);
	
?>
