<?php

	include 'conf.php';
	
	
	$tstamp = ($_GET["tstamp"] == "") ? date("Y-m-d H:i:s") : $_GET["tstamp"];
	$tstamp = date("Y-m-d H:i:s");
	$msisdn = ($_GET["msisdn"] == "") ? "0" : $_GET["msisdn"];
	//$message = ($_GET["message"] == "") ? "" : $_GET["message"]; 
	//$smscid = ($_GET["smscid"] == "") ? "" : $_GET["smscid"];
	//$appsid = ($_GET["appsid"] == "") ? "" : $_GET["appsid"];
	$status = ($_GET["status"] == "") ? "" : $_GET["status"];
	$msgid = ($_GET["msgid"] == "") ? "" : $_GET["msgid"];
	$msgid = str_replace("||"," ",$msgid);
	
	//$sql = "INSERT INTO sms_dlr (msisdn, message, msgid, status, tstamp) VALUES (\"$msisdn\", \"$msgid\", \"$message\", \"$status\", \"$tstamp\")";
	$sql = "INSERT INTO sms_dlr (msisdn, status, tstamp, msgid) VALUES (\"$msisdn\", \"$status\", \"$tstamp\", \"$msgid\")";

	echo $sql;
	//die($sql);
	$sql = mysql_query($sql);
	
	
	
	
?>