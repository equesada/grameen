<?php
	require_once "../inc/config.php";
	require_once "../inc/connect.php";
	
	$arr = array(
					"status" => "succes",
					"description" => "Success Delete"
					);
	$sql = 'DELETE FROM industry WHERE industry_id='.(int)$_GET['id'];
	$r = mysql_query($sql);
	if(!$r)
	{
		$arr["status"] = "error";
		$arr["description"] = "Delete industry id ".$_GET['id']." fail, messsage = ".mysql_error();
	}
	$json = json_encode($arr);
if (isset($_GET['callback'])) {
    echo $_GET['callback'] . "($json);"; // somefunction({data here});
// Normal JSON
} else {
   echo $json;
}

?>