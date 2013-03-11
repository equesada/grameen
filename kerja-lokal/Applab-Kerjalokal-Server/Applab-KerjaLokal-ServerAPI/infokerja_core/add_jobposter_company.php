<?php
require "conf.php";
require "func.php";
	
$data = array();
$del_key = array("tx_id");
foreach ($_GET as $key => $value)
{ 
	if ($key == "lat") $key = "pos_lat";
	if ($key == "lng") $key = "pos_lng";
	if (!in_array($key, $del_key)) $data[$key] = str_clean($value); 
	if ($key == "userlevel")
		if (($value == "superadmin") || ($value == "admin")) 
		{
			$sql = "SELECT MAX(jobposter_id) as jobposter_id FROM job_posters_company WHERE userlevel = 'superadmin' OR userlevel = 'admin'";
			$sql = mysql_query($sql) OR die(output(mysql_error()));
			$r = mysql_fetch_assoc($sql);
			$data["jobposter_id"] = $r["jobposter_id"] + 1;
			$data["comp_id"] = 6;
		}
}
	
$var_k = "";
$var_v = "";
foreach ($data as $key => $value)
{ 
	$var_k .= $key.", "; 
	$var_v .= "\"$value\", "; 
}
$var_k = substr($var_k,0, strlen($var_k)-2);
$var_v = substr($var_v,0, strlen($var_v)-2);


$sql = "SELECT * FROM job_posters_company WHERE username='".$data["username"]."'";
$r = mysql_query($sql) OR die(output(mysql_error()));
if (mysql_num_rows($r) > 0)
	die(output("Username is already exists"));
else
{
	$sql = "INSERT INTO job_posters_company ($var_k) VALUES ($var_v)";
	//die($sql);
	$r = mysql_query($sql) OR die(output(mysql_error()));

	echo output(1);
}

?>