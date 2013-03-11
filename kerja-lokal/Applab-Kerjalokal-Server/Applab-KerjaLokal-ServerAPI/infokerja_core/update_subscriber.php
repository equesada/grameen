<?php
require "conf.php";
require "func.php";
	
$data = array();
$del_key = array("tx_id", "subscriber_id", "region", "kelurahan", "zip");
//$del_key = array("tx_id", "name", "street", "rt", "rw", "place_birth", "birthday");
$subscriber_id = str_clean($_GET["subscriber_id"]);

/*
$name = str_clean($_GET["name"]);
$gender_id = str_clean($_GET["gender"]);
$education = str_clean($_GET["edu_id"]);
$birth_place = str_clean($_GET["place_birth"]);
$birthday = $_GET["birthday"];
//echo $birthday;
$id_card = str_clean($_GET["idcard"]);
*/
foreach ($_GET as $key => $value)
{ 
	if ($key == "lat") $key = "pos_lat";
	if ($key == "lng") $key = "pos_lng";
	if (!in_array($key, $del_key)) $data[$key] = str_clean($value); 
}

//echo "<pre>"; print_r($data); echo "</pre>";

if (isset($_GET['kelurahan']))
	if (str_clean($_GET['kelurahan']) != "0")
		$data["loc_id"] = str_clean($_GET['kelurahan']);
	else
		if (isset($_GET['zip']))
			if (str_clean($_GET['zip']) != "0")
				$data["loc_id"] = str_clean($_GET['zip']);
				

$var = "";
foreach ($data as $key => $value)
{ 
	$var .= $key."=\"".$value."\", "; 
}
$var = substr($var,0, strlen($var)-2);
//echo "<pre>"; print_r($var); echo "</pre>";

$sql = "UPDATE $t_subscribers SET $var WHERE subscriber_id=\"$subscriber_id\"";
//die($sql);

$r = mysql_query($sql)OR die(output(mysql_error()));

echo output(1);

?>