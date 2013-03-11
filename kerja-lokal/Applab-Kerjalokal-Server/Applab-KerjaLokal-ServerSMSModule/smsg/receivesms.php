<?php
	include 'conf.php';
	include 'func.php';
	
	$tx_id = get_uuid("smsg");
	
	echo "<pre>"; print_r($_GET);
	
	$msisdn = ($_GET["msisdn"] == "") ? "0" : trim($_GET["msisdn"]);
	if (substr($msisdn,0,1) != "+") $msisdn = "+".$msisdn;
	$msisdn = substr($msisdn,1);

	$shortcode = (!isset($_GET["shortcode"])) ? "0" : urldecode($_GET["shortcode"]); 
	if (substr($shortcode,0,1) != "+") $shortcode = "+".$shortcode;
	$shortcode = substr($shortcode,1);
	
	$message = ($_GET["message"] == "") ? "" : $_GET["message"]; 	
	//$appsid = ($_GET["appsid"] == "") ? "" : $_GET["appsid"];
	//$tstamp = ($_GET["tstamp"] == "") ? now() : $_GET["tstamp"];
		
	$sql = "INSERT INTO sms_received (msisdn, shortcode, message, tstamp) VALUES (\"$msisdn\", \"$shortcode\", \"$message\", \"".date("Y-m-d H:i:s")."\")";
	
	//echo $sql."<hr>"; 
	$sql = mysql_query($sql); 
	
	//FOR NUMBER MDN
	$number = substr($msisdn,2);
	$number_mdn = $number;	//NOMOR MDN MENGGUNAKAN TANPA 0
	$number_mdn0 = "0".$number;	//NOMOR MDN MENGGUNAKAN TANPA 0
	
	//FOR MESSAGE
	$arr_message = explode(" ",strtoupper($message));
	print_r($arr_message);

	$dtime = isSet($_GET["dtime"]) ? ($_GET["dtime"]." ".date("H:i:s")) : date("Y-m-d H:i:s");
	$dtime_micro = dtime_micro($dtime);
	$curr_date = date("Y-m-d", strtotime($dtime));
	
	if (strpos($mdn_test, $number_mdn) > 0)
	{
		write_logfile($number_mdn, "SMSG:MO: $message");
		switch ($arr_message[0])
		{
			case "REG" : 
				if (sizeof($arr_message) == 3) 
					include_once("reg.php"); 
				else
				{
					$data = CORE_URL."add_logsmsg.php?tx_id=$tx_id&mdn=$number_mdn0&sms_type=MO:REG&message=".urlencode($message)."&dtime_add=".urlencode($dtime_micro);
					echo $data."<hr>";				
					$data = get_data($data);
					echo $a = send_sms($number_mdn, $msg_notif[1], $tx_id);
				}
				break;
			case "UNREG" : include_once("unreg.php"); break;
			case $sms_keyword[1] : 
				if (sizeof($arr_message) <= 3) 
					include_once("kerja.php"); 
				else
				{
					$data = CORE_URL."add_logsmsg.php?tx_id=$tx_id&mdn=$number_mdn0&sms_type=MO:REG&message=".urlencode($message)."&dtime_add=".urlencode($dtime_micro);
					echo $data."<hr>";				
					$data = get_data($data);
					echo $a = send_sms($number_mdn, $msg_notif[1], $tx_id);
				}
				break;
			default : echo $a = send_sms($number_mdn, $msg_notif[1], $tx_id); break;
		}
	}
	echo "</pre>"; 
?>