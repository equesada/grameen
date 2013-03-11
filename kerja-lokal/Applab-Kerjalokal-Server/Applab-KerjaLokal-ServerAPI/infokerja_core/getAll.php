<?php
	require_once "../inc/config.php";
	require_once "../inc/connect.php";
	require_once "../inc/functions.php";
	
	$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
	$rowPerPage = isset($_GET['rowPerPage']) ? (int)$_GET['rowPerPage'] : 5;
	$order = isset($_GET['order']) ? $_GET['order'] : "";
	$ascdesc = isset($_GET['ascdesc']) ? $_GET['ascdesc'] : "ASC";
	$arr = array(
					"status" => "success",
					"description" => "Success Select"
					);
	
	$sql = "SELECT * FROM industry";
	$newSql = getPagingQuery($sql,$rowPerPage,$page,$order,$ascdesc);
	$r = mysql_query($newSql);
	if(!$r)
	{
		$arr["status"] = "error";
		$arr["description"] = "Add industry fail, messsage = ".mysql_error();
		$json = json_encode($arr);
		die($json);
	}
	$numFields = mysql_num_fields($r);
	$numRows = mysql_num_rows($r);
	$arr['numRows'] = $numRows;
	$arr['numFields'] = $numFields;
	$i = 0;
	while($row = mysql_fetch_assoc($r))
	{
		for($j=0;$j<$numFields;$j++)
		{
			$arr['row'.$i][mysql_field_name($r,$j)] = $row[mysql_field_name($r,$j)];
		}
		$i++;
	}
	$arr['pagingLink'] = getPagingLink($sql, $rowPerPage,$page);
	$arr['page'] = $page;
	$arr['rowPerPage'] = $rowPerPage;
	$json = json_encode($arr);
	if (isset($_GET['callback'])) {
		echo $_GET['callback'] . "($json);"; // somefunction({data here});
	// Normal JSON
	} else {
	   echo $json;
	}

?>