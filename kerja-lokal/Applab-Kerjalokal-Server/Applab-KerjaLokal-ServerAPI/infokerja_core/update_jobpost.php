<?php
require "conf.php";
require "func.php";
	
$data = array();
$del_key = array("job_id", "region", "kelurahan", "zip", "tx_id");
$job_id = str_clean($_GET["job_id"]);

foreach ($_GET as $key => $value)
{ 
	if ($key == "lat") $key = "pos_lat";
	if ($key == "lng") $key = "pos_lng";
	if (!in_array($key, $del_key)) $data[$key] = str_clean($value); 
}

if (isset($_GET['kelurahan']))
	if (str_clean($_GET['kelurahan']) != "0")
		$data["loc_id"] = str_clean($_GET['kelurahan']);
	else
		if (isset($_GET['zip']))
			if (str_clean($_GET['zip']) != "0")
				$data["loc_id"] = str_clean($_GET['zip']);


//echo "<pre>"; print_r($_GET); echo "</pre>";
//echo "<pre>"; print_r($data); echo "</pre>";

$var = "";
foreach ($data as $key => $value)
{ 
	$var .= $key."=\"".$value."\", "; 
}
$var = substr($var,0, strlen($var)-2);

$sql = "UPDATE $t_jobs SET $var WHERE job_id=\"$job_id\"";
//die($sql);
$r = mysql_query($sql) OR die(output(mysql_error()));

echo output(1);

?>