<?php

	require "conf.php";
	require "func.php";

	$mdn = isset($_GET["mdn"]) ? str_clean($_GET["mdn"]) : "";
	$type = isset($_GET["type"]) ? str_clean($_GET["type"]) : "";
	$prefix = substr($mdn,1,strpos($mdn,"9")-1);
	//echo $prefix;
	$arr = array();
	
	$sql = "SELECT * FROM $t_tb_prefix";
	
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	$r = mysql_fetch_assoc($sql);
	$opprefix = $r["opprefix"];
	//echo  $r["opprefix"];
	$opprefix = explode(",",$opprefix);
	//print_r($opprefix);
	
	if (in_array(substr($mdn,1,3), $opprefix)) {
		//echo "dapet "."0"."".substr($mdn,1,3);
		echo output(1);
	} else if (in_array(substr($mdn,1,2), $opprefix)) {
		//echo "dapet "."0"."".substr($mdn,1,2);
		echo output(1);
	} else {
		//$kodarea='021';
		echo output("Invalid MDN");
	}
	
	
	
	
	
	
	
	
	//echo "<pre>"; print_r($opprefix); die($mdn."->".$kodarea);
	/*
	if (in_array($prefix, $opprefix))
		echo output(1);
	else
		echo output("Invalid MDN");
	*/
?>