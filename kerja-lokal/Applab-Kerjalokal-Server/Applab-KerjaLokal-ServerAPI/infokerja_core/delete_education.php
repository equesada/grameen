<?php
	require_once "conf.php";
	require_once "func.php";
	
	$arr = array(
					"status" => "1",
					"msg" => "Success Delete"
					);
	$sql = 'DELETE FROM education WHERE edu_id='.(int)$_GET['id'];
	$r = mysql_query($sql);
	if(!$r)
	{
		$arr["status"] = "0";
		$arr["msg"] = "Delete Education id ".$_GET['id']." fail, messsage = ".mysql_error();
	}
	$json = json_encode($arr);
if (isset($_GET['callback'])) {
    echo $_GET['callback'] . "($json);"; // somefunction({data here});
// Normal JSON
} else {
   echo $json;
}

?>