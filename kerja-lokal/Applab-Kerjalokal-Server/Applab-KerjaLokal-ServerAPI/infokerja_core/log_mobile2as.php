<?php
require "conf.php";
require "func.php";

$mdn = isset($_GET["mdn"]) ? str_clean($_GET["mdn"]) : "";
$lang = isset($_GET["lang"]) ? str_clean($_GET["lang"]) : "";
$cid = isset($_GET["cid"]) ? str_clean($_GET["cid"]) : "";
$cmd = isset($_GET["cmd"]) ? str_clean($_GET["cmd"]) : "";
$status = isset($_GET["status"]) ? str_clean($_GET["status"]) : "";
$pageidsrc = isset($_GET["pageidsrc"]) ? str_clean($_GET["pageidsrc"]) : "";
$pageidreq = isset($_GET["pageidreq"]) ? str_clean($_GET["pageidreq"]) : "";
$request = isset($_GET["request"]) ? str_clean($_GET["request"]) : "";
$response = isset($_GET["response"]) ? str_clean($_GET["response"]) : "";
	
$time = date("Y-m-d H:i:s");

$sql = "INSERT INTO $t_log_dms (mdn, time, lang, cid, cmd, status, pageidsrc, pageidreq, request, response) VALUES";
$sql .= " ('$mdn','$time','$lang','$cid', '$cmd', '$status','$pageidsrc','$pageidreq','$request','$response')";
$sql = mysql_query($sql) OR die(output(mysql_error()));

echo output(1);

?>