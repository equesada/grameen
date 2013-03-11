<?php

	require "conf.php";
	require "func.php";
	
	list($birthday_date_from, $birthday_date_to) = explode(":", $_GET["birthday"]);
	
	list($submit_date_from, $submit_date_to) = explode(":", $_GET["submit_date"]);
	list($edit_date_from, $edit_date_to) = explode(":", $_GET["edit_date"]);
	
	$loc_title = isset($_GET["loc_title"]) ? ($_GET["loc_title"] == "null" ? '' : str_clean($_GET["loc_title"])) : '';
	
	$jobcat_id = isset($_GET["jobcat_id"]) ? ($_GET["jobcat_id"] == "null" ? '' : str_clean($_GET["jobcat_id"])) : '';
	//echo $jobcat_id."<hr>";

	$country_id = isset($_GET["country_id"]) ? str_clean(strtoupper($_GET["country_id"])) : "ID";
	$ndata = isset($_GET["ndata"]) ? str_clean($_GET["ndata"],1) : 0;
	$page = isset($_GET["page"]) ? str_clean($_GET["page"],1) : 0;
	//if($_GET["order"] == "subscriber_name" OR $_GET["order"] == "loc_title") $d = ""; else $d = $t_subscribers.".";
	//$order = isset($_GET["order"]) ? $d.str_clean($_GET["order"]) : "subscriber_name";
	
	$order = isset($_GET["order"]) ? str_clean($_GET["order"]) : "loc_title";
	
	$ascdesc = isset($_GET["ascdesc"]) ? str_clean($_GET["ascdesc"]) : "asc";
	$callback = isset($_GET['callback']);
	
	$status = isset($_GET["status"]) ? ($_GET["status"] == "0" ? "0" : str_clean($_GET["status"])) : "";
	$subscriber_name = isset($_GET["subscriber_name"]) ? ($_GET["subscriber_name"] == "_" ? "" : str_clean($_GET["subscriber_name"])) : "";
	$gender = isset($_GET["gender"]) ? ($_GET["gender"] == "0" ? "" : str_clean($_GET["gender"])) : "";	
	$education = isset($_GET["education"]) ? ($_GET["education"] == "0" ? "" : str_clean($_GET["education"])) : "";	
	$jobseeker_name = isset($_GET["jobseeker_name"]) ? ($_GET["jobseeker_name"] == "null" ? '' : str_clean($_GET["jobseeker_name"])) : '';
	$mdn = isset($_GET["mdn"]) ? ($_GET["mdn"] == "null" ? '' : str_clean($_GET["mdn"])) : '';
	$mentor = isset($_GET["mentor"]) ? ($_GET["mentor"] == "null" ? '' : str_clean($_GET["mentor"])) : '';
	//echo $education."<hr>";
	//$loc_title = isset($_GET["loc_title"]) ? ($_GET["loc_title"] == "_" ? "" : str_clean($_GET["loc_title"])) : 0;
	$salary = isset($_GET["salary"]) ? ($_GET["salary"] == "_" ? "" : str_clean($_GET["salary"])) : "";	
	$date_add = isset($_GET["date_add"]) ? ($_GET["date_add"] == "_" ? "" : str_clean($_GET["date_add"])) : "";
	
	
	$birthday_date_from = isset($birthday_date_from) ? ($birthday_date_from == "null" ? '' : str_clean($birthday_date_from)) : '';
	$birthday_date_to = isset($birthday_date_to) ? ($birthday_date_to == "null" ? '' : str_clean($birthday_date_to)) : '';
	
	$submit_date_from = isset($submit_date_from) ? ($submit_date_from == "null" ? '' : str_clean($submit_date_from)) : '';
	$submit_date_to = isset($submit_date_to) ? ($submit_date_to == "null" ? '' : str_clean($submit_date_to)) : '';
	
	$edit_date_from = isset($edit_date_from) ? ($edit_date_from == "null" ? '' : str_clean($edit_date_from)) : '';
	$edit_date_to = isset($edit_date_to) ? ($edit_date_to == "null" ? '' : str_clean($edit_date_to)) : '';

	$arr["totaldata"] =0;
	$arr['ndata'] = 0;
	$arr['pagingLink'] = "";
	$arr['nrows'] = 0;
	$arr['nfields'] = 0;
	$arr['npage'] = 0;
	$arr['page'] = 0;
	$arr['results'] = array();
	
	switch ($order)
	{ 
		case "subscriber_id" : $order = "$t_subscribers.subscriber_id"; break;
		case "subscriber_name" : $order = "$t_subscribers.name"; break;
		case "mdn" : $order = "$t_subscribers.mdn"; break;
		case "jobcat_id" : $order = "$t_rel_subscriber_cat.jobcat_id"; break;
		case "loc_title" : $order = "loc_title"; break;
		case "status" : $order = "$t_subscribers.status"; break;
		case "gender" : $order = "$t_subscribers.gender"; break;
		case "edu_id" : $order = "$t_subscribers.edu_id"; break;
		case "date_add" : $order = "$t_subscribers.date_add"; break;
		case "date_update" : $order = "$t_subscribers.date_update"; break;
	}
	//echo $order."<hr>";
	
	$search = "";
	//if ($subscriber_name != "") $search .= "AND $t_subscribers.name LIKE \"%$subscriber_name%\"";
	
	
	if ($gender != "") $search .= "AND $t_subscribers.gender = \"$gender\"";
	if ($education != "") $search .= "AND $t_subscribers.edu_id = \"$education\"";
	if (!empty($jobseeker_name)) $search .= "AND $t_subscribers.name LIKE \"%$jobseeker_name%\" ";
	if (!empty($mdn)) $search .= "AND $t_subscribers.mdn LIKE \"%$mdn%\" ";
	if (!empty($mentor)) $search .= "AND $t_subscribers.name LIKE \"%$mentor%\" ";
	
	if (!empty($birthday_date_from)) $search .= "AND $t_subscribers.birthday BETWEEN '$birthday_date_from' AND '$birthday_date_to' ";
	if (!empty($submit_date_from)) $search .= "AND $t_subscribers.date_add BETWEEN '$submit_date_from 00:00:01'  AND '$submit_date_to 00:00:01' ";
	if (!empty($edit_date_from)) $search .= "AND $t_subscribers.date_update BETWEEN '$edit_date_from 00:00:01'  AND '$edit_date_to 00:00:01' ";
	
	//if (!empty($jobcat_id)) $search .= "AND $t_rel_subscriber_cat.jobcat_id = '$jobcat_id' ";			
	
	if (!empty($status) && ($status != "")) $search .= " AND $t_subscribers.status = $status ";
	
	//echo $search;
	
	if($jobcat_id != ""){
		//echo "jobcat id<hr>";
		$sql = "SELECT *, $t_subscribers.name AS subscriber_name, $t_location.name AS loc_title, $t_education.edu_title,";
		$sql .= "$t_subscribers.date_add AS subscriber_date_add, ";
		$sql .= "$t_subscribers.date_update AS subscriber_date_update, ";
		$sql .= "$t_rel_subscriber_cat.jobcat_id AS jobcat_id, ";
		$sql .= "$t_subscribers.status AS status FROM $t_subscribers ";
		$sql .= "INNER JOIN $t_rel_subscriber_cat ON $t_subscribers.subscriber_id = $t_rel_subscriber_cat.subscriber_id ";
		$sql .= "INNER JOIN $t_education ON $t_education.edu_id = $t_subscribers.edu_id ";	
		//$sql .= "INNER JOIN $t_rel_subscriber_cat ON  $t_subscribers.subscriber_id = $t_rel_subscriber_cat.subscriber_id ";
		$sql .= "INNER JOIN $t_location ON $t_subscribers.loc_id = $t_location.loc_id WHERE $t_rel_subscriber_cat.jobcat_id = '$jobcat_id' ";
		
		//echo $sql."<hr>";
		
		if ($search != "") $sql .= $search;

		$sql = mysql_query($sql) OR die(output(mysql_error()));
		$arr["totaldata"] = mysql_num_rows($sql);
		$arr['ndata'] = $ndata == 0 ? $arr["totaldata"] : $ndata;
/*
		$sql = "SELECT *, $t_subscribers.name AS subscriber_name, $t_location.name AS loc_title, $t_education.edu_title,";
		$sql .= "$t_subscribers.date_add AS subscriber_date_add, ";
		$sql .= "$t_subscribers.date_update AS subscriber_date_update, ";
		$sql .= "$t_subscribers.status AS status FROM $t_subscribers ";
		$sql .= "INNER JOIN $t_rel_subscriber_cat ON $t_subscribers.subscriber_id = $t_rel_subscriber_cat.subscriber_id ";
		$sql .= "INNER JOIN $t_education ON $t_education.edu_id = $t_subscribers.edu_id ";
		//$sql .= "INNER JOIN $t_rel_subscriber_cat ON  $t_subscribers.subscriber_id = $t_rel_subscriber_cat.subscriber_id ";
		$sql .= "INNER JOIN $t_location ON $t_subscribers.loc_id = $t_location.loc_id WHERE $t_rel_subscriber_cat.jobcat_id = '$jobcat_id' ";
		if ($search != "") $sql .= $search;
		
		
		$sql = mysql_query($sql) OR die(output(mysql_error()));		
*/
		$temp = array();
		$arr['nfields'] = mysql_num_fields($sql);
		while($row = mysql_fetch_assoc($sql))
		{
			for($j=0;$j<$arr['nfields'];$j++)
			{
				$val[mysql_field_name($sql,$j)] = $row[mysql_field_name($sql,$j)];
			}
			//array_push($arr["results"], $val);
			array_push($temp, $val);
			//echo "<pre>"; print_r($val); echo "</pre><hr>";
		}
		
		$a = 0;
		foreach ($temp as $job)
		{
			$kecamatan_id = $job["parent_id"];
			//echo $kecamatan_id."<hr>";
			$sql = "SELECT name, parent_id FROM $t_location WHERE loc_id='$kecamatan_id'";
			//die($sql);
			$sql = mysql_query($sql) OR die(output(mysql_error()));
			
			$row = mysql_fetch_assoc($sql);
			$temp[$a]["kecamatan_id"] = $kecamatan_id;
			$temp[$a]["kecamatan_name"] = $row["name"];
			
			$kotamadya_id = $row["parent_id"];
			$sql = "SELECT name, parent_id FROM $t_location WHERE loc_id='$kotamadya_id'";
			$sql = mysql_query($sql) OR die(output(mysql_error()));
			$row = mysql_fetch_assoc($sql);
			$temp[$a]["kotamadya_id"] = $kotamadya_id;
			$temp[$a]["kotamadya_name"] = $row["name"];
			
			$province_id = $row["parent_id"];
			$sql = "SELECT name, parent_id FROM $t_location WHERE loc_id='$province_id'";
			$sql = mysql_query($sql) OR die(output(mysql_error()));
			$row = mysql_fetch_assoc($sql);
			$temp[$a]["province_id"] = $province_id;
			$temp[$a]["province_name"] = $row["name"];
			$a++;
		}
		$b = 0;
		//echo "<pre>"; print_r($temp); echo "</pre><hr>";	
		
		$temp2 = array();
		foreach ($temp as $job)
		{
			//$a = array_search("dki jakarta", array_map('strtolower', $job));
			//echo $loc_title."<hr>";
			$a = array_find(strtolower($loc_title), array_map('strtolower', $job), false, "province_name, kotamadya_name, kecamatan_name, loc_title");
			//echo $a."<hr>";
			if (($a == "province_name") || ($a == "kotamadya_name") || ($a == "kecamatan_name") || ($a == "loc_title") || ($a != ""))
			{
				//echo $a."<hr>";
				array_push($temp2, $job);
			}
		}
		//echo "<pre>"; print_r($temp2); echo "</pre><hr>";

		$temp3 = array();
		switch ($order)
		{ 
			case "$t_jobs.comp_name" : $order = "company_name"; break;
			case "$t_companies.loc_id" : $order = "loc_id"; break;
			case "$t_jobs.status" : $order = "status"; break;
			case "$t_jobs.date_add" : $order = "date_add"; break;
			//case "province_name" : $order = "province_name"; break;
			//default : $order = $_GET["order"];
		}

		//foreach($temp2 as $job) $temp3[] = $job[$order];
		foreach($temp2 as $key => $row) { $temp3[$key]  = $row[$order]; }
		if ($ascdesc == "ASC")
			{ array_multisort($temp3, SORT_ASC, $temp2); }
		else
			{ array_multisort($temp3, SORT_DESC, $temp2); }
		$a = 0;
		
		//echo $b;
		//echo "<pre>"; print_r($temp2); echo "</pre>";

		$arr["totaldata"] = sizeof($temp2);
		$arr['ndata'] = $ndata == 0 ? $arr["totaldata"] : $ndata;

		//$arr['nrows'] = mysql_num_rows($sql);
		$arr['npage'] = $ndata > 0 ? ceil($arr["totaldata"] / $ndata) : 1;
		$arr['nopage'] = $page;
		if ($page != 0)
			if ($arr['page'] < $arr['npage'])
			{ $arr['nrows'] = $ndata; }
			else
			{ $arr['nrows'] = $arr["totaldata"] % $ndata; }
		else
			$arr['nrows'] = $arr["totaldata"];
		$bottom_array = $ndata * ($page-1);
		$upper_array = $bottom_array + $arr['nrows'];
		//echo $bottom_array."-".$upper_array."<hr>";
		for($a = $bottom_array; $a < $upper_array; $a++)
		{
			array_push($arr["results"], $temp2[$a]);
			//echo $a."<hr>";
		}
		$arr['results'] = $temp2;
		
		
		$arr['nrows'] = mysql_num_rows($sql);
		$arr['nfields'] = mysql_num_fields($sql);
		$arr['npage'] = $ndata > 0 ? ceil($arr["totaldata"] / $ndata) : 1;
		$arr['offset'] = ($page > 0) ? ($page - 1) * $ndata : 0;
		$arr['nopage'] = $page;
		$arr['nopage'] = $page;
		//$arr['results'] = array();
		//print_r($sql); exit();	
		//echo output($arr);
		
	}
	else
	{
		
		
		

		$sql = "SELECT *, $t_subscribers.name AS subscriber_name, $t_location.name AS loc_title, $t_education.edu_title,";
		$sql .= "$t_subscribers.date_add AS subscriber_date_add, ";
		$sql .= "$t_subscribers.date_update AS subscriber_date_update, ";
		$sql .= "$t_subscribers.status AS status FROM $t_subscribers ";
		$sql .= "INNER JOIN $t_education ON $t_education.edu_id = $t_subscribers.edu_id ";	
		$sql .= "INNER JOIN $t_location ON $t_subscribers.loc_id = $t_location.loc_id WHERE $t_subscribers.country_id='$country_id' ";
		
		
		
		if ($search != "") $sql .= $search;

		$sql = mysql_query($sql) OR die(output(mysql_error()));
		$arr["totaldata"] = mysql_num_rows($sql);
		$arr['ndata'] = $ndata == 0 ? $arr["totaldata"] : $ndata;

		$sql = "SELECT *, $t_subscribers.name AS subscriber_name, $t_location.name AS loc_title, $t_education.edu_title,";
		$sql .= "$t_subscribers.date_add AS subscriber_date_add, ";
		$sql .= "$t_subscribers.date_update AS subscriber_date_update, ";
		$sql .= "$t_subscribers.status AS status FROM $t_subscribers ";
		$sql .= "INNER JOIN $t_education ON $t_education.edu_id = $t_subscribers.edu_id ";
		
		$sql .= "INNER JOIN $t_location ON $t_subscribers.loc_id = $t_location.loc_id WHERE $t_subscribers.country_id='$country_id' ";
		if ($search != "") $sql .= $search;
			
			
		//echo $sql."<hr>";
		
		
		
		
		if ($loc_title == "")
		{
			$sql = getPagingQuery($sql,$ndata,$page,$order,$ascdesc);
			$sql = mysql_query($sql) OR die(output(mysql_error()));
			
			$arr['nrows'] = mysql_num_rows($sql);
			$arr['nfields'] = mysql_num_fields($sql);
			$arr['npage'] = $ndata > 0 ? ceil($arr["totaldata"] / $ndata) : 1;
			$arr['nopage'] = $page;
			$arr['results'] = array();
		
			while($row = mysql_fetch_assoc($sql))
			{
				for($j=0;$j<$arr['nfields'];$j++)
				{
					$val[mysql_field_name($sql,$j)] = $row[mysql_field_name($sql,$j)];
				}
				
				$kecamatan_id = substr($val["loc_id"],5);
				$sql2 = "SELECT name, parent_id FROM $t_location WHERE loc_id='$kecamatan_id'";
				$sql2 = mysql_query($sql2) OR die(output(mysql_error()));
				$row2 = mysql_fetch_assoc($sql2);
				$val["kecamatan_id"] = $kecamatan_id;
				$val["kecamatan_name"] = $row2["name"];
				

				$kotamadya_id = $row2["parent_id"];
				$sql2 = "SELECT name, parent_id FROM $t_location WHERE loc_id='$kotamadya_id'";
				$sql2 = mysql_query($sql2) OR die(output(mysql_error()));
				$row2 = mysql_fetch_assoc($sql2);
				$val["kotamadya_id"] = $kotamadya_id;
				$val["kotamadya_name"] = $row2["name"];
				
				$province_id = $row2["parent_id"];
				$sql2 = "SELECT name, parent_id FROM $t_location WHERE loc_id='$province_id'";
				$sql2 = mysql_query($sql2) OR die(output(mysql_error()));
				$row2 = mysql_fetch_assoc($sql2);
				$val["province_id"] = $province_id;
				$val["province_name"] = $row2["name"];
				
				array_push($arr["results"], $val);
				//echo "<pre>"; print_r($val); echo "</pre><hr>";
			}
			
		}else{
			//echo "jajal";
			$sql = mysql_query($sql) OR die(output(mysql_error()));
			//echo $sql."<hr>";
			$temp = array();
			$arr['nfields'] = mysql_num_fields($sql);
			while($row = mysql_fetch_assoc($sql))
			{
				for($j=0;$j<$arr['nfields'];$j++)
				{
					$val[mysql_field_name($sql,$j)] = $row[mysql_field_name($sql,$j)];
				}
				//array_push($arr["results"], $val);
				array_push($temp, $val);
				//echo "<pre>"; print_r($val); echo "</pre><hr>";
			}
			
			$a = 0;
			foreach ($temp as $job)
			{
				$kecamatan_id = $job["parent_id"];
				//echo $kecamatan_id."<hr>";
				$sql = "SELECT name, parent_id FROM $t_location WHERE loc_id='$kecamatan_id'";
				//die($sql);
				$sql = mysql_query($sql) OR die(output(mysql_error()));
				
				$row = mysql_fetch_assoc($sql);
				$temp[$a]["kecamatan_id"] = $kecamatan_id;
				$temp[$a]["kecamatan_name"] = $row["name"];
				
				$kotamadya_id = $row["parent_id"];
				$sql = "SELECT name, parent_id FROM $t_location WHERE loc_id='$kotamadya_id'";
				$sql = mysql_query($sql) OR die(output(mysql_error()));
				$row = mysql_fetch_assoc($sql);
				$temp[$a]["kotamadya_id"] = $kotamadya_id;
				$temp[$a]["kotamadya_name"] = $row["name"];
				
				$province_id = $row["parent_id"];
				$sql = "SELECT name, parent_id FROM $t_location WHERE loc_id='$province_id'";
				$sql = mysql_query($sql) OR die(output(mysql_error()));
				$row = mysql_fetch_assoc($sql);
				$temp[$a]["province_id"] = $province_id;
				$temp[$a]["province_name"] = $row["name"];
				$a++;
			}
			$b = 0;
			//echo "<pre>"; print_r($temp); echo "</pre><hr>";	
			
			$temp2 = array();
			foreach ($temp as $job)
			{
				//$a = array_search("dki jakarta", array_map('strtolower', $job));
				$a = array_find(strtolower($loc_title), array_map('strtolower', $job), false, "province_name, kotamadya_name, kecamatan_name, loc_title");
				//echo $a."<hr>";
				if (($a == "province_name") || ($a == "kotamadya_name") || ($a == "kecamatan_name") || ($a == "loc_title") || ($a != ""))
				{
					//echo $a."<hr>";
					array_push($temp2, $job);
				}
			}
			//echo "<pre>"; print_r($temp2); echo "</pre><hr>";

			$temp3 = array();
			switch ($order)
			{ 
				case "$t_jobs.comp_name" : $order = "company_name"; break;
				case "$t_companies.loc_id" : $order = "loc_id"; break;
				case "$t_jobs.status" : $order = "status"; break;
				case "$t_jobs.date_add" : $order = "date_add"; break;
				//case "province_name" : $order = "province_name"; break;
				//default : $order = $_GET["order"];
			}

			//foreach($temp2 as $job) $temp3[] = $job[$order];
			foreach($temp2 as $key => $row) { $temp3[$key]  = $row[$order]; }
			if ($ascdesc == "ASC")
				{ array_multisort($temp3, SORT_ASC, $temp2); }
			else
				{ array_multisort($temp3, SORT_DESC, $temp2); }
			$a = 0;
			
			//echo $b;
			//echo "<pre>"; print_r($temp2); echo "</pre>";

			$arr["totaldata"] = sizeof($temp2);
			$arr['ndata'] = $ndata == 0 ? $arr["totaldata"] : $ndata;

			//$arr['nrows'] = mysql_num_rows($sql);
			$arr['npage'] = $ndata > 0 ? ceil($arr["totaldata"] / $ndata) : 1;
			$arr['nopage'] = $page;
			if ($page != 0)
				if ($arr['page'] < $arr['npage'])
				{ $arr['nrows'] = $ndata; }
				else
				{ $arr['nrows'] = $arr["totaldata"] % $ndata; }
			else
				$arr['nrows'] = $arr["totaldata"];
			$bottom_array = $ndata * ($page-1);
			$upper_array = $bottom_array + $arr['nrows'];
			//echo $bottom_array."-".$upper_array."<hr>";
			for($a = $bottom_array; $a < $upper_array; $a++)
			{
				array_push($arr["results"], $temp2[$a]);
				//echo $a."<hr>";
			}
			$arr['results'] = $temp2;
			
			
			$arr['nrows'] = mysql_num_rows($sql);
			$arr['nfields'] = mysql_num_fields($sql);
			$arr['npage'] = $ndata > 0 ? ceil($arr["totaldata"] / $ndata) : 1;
			$arr['offset'] = ($page > 0) ? ($page - 1) * $ndata : 0;
			$arr['nopage'] = $page;
			$arr['page'] = $page;
			//$arr['results'] = array();
			//print_r($sql); exit();		
		
		}	
	}

	echo output($arr);
	//echo "<pre>"; print_r(json_decode(output($arr),true)); echo "</pre>";

?>