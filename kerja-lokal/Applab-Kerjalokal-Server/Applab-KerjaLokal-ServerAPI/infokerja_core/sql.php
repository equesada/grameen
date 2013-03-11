<?php
require "conf.php";
require "func.php";

$sql = $_GET["sql"];
$sql = urldecode($sql);
//echo $sql."<hr>";
/*
$sql = urldecode($sql);
echo $sql."<hr>";
*/
$sql = mysql_query($sql) OR die(output(mysql_error()));

if (substr($_GET["sql"],0,6) == "SELECT")
{
	$arr['nrows'] = mysql_num_rows($sql);
	$arr['nfields'] = mysql_num_fields($sql);
	$arr["results"] = array();
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
	echo output($arr);
}
else

	echo output(1);

?>