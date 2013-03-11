<?php
require "conf.php";
require "func.php";
	
$val = str_clean($_GET['val']);
$val =  str_replace("\\","", $val);
$val = json_decode($val,1);
//echo "<pre>"; print_r($val); echo "</pre>";
//die();
$callback = str_clean($_GET['callback']);

$name = $val["name"];
$del = $val["del"];
$is_current = $val["is_current"];
$date_update = date("Y-m-d H:i:s");


$sql = "UPDATE themes SET is_current='0'";
mysql_query($sql) OR die(output(mysql_error()));
$sql = "UPDATE themes SET is_current='1' WHERE id =".$_GET['val'];
mysql_query($sql) OR die(output(mysql_error()));

if ($del != null)
	foreach ($del as $id)
	{
		$sql = "DELETE FROM themes WHERE id=\"".$id."\"";
		$r = mysql_query($sql) OR die(output(mysql_error()));
	}
//die($sql);
echo output(1);

?>