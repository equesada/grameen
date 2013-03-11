<?php
	include 'conf.php';
	function get_uuid($param="")
	{
		
		$uuid = 
			//sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			sprintf( '%04x%04x%04x%04x%04x%04x%04x%04x',
			// 32 bits for "time_low"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

			// 16 bits for "time_mid"
			mt_rand( 0, 0xffff ),

			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 4
			mt_rand( 0, 0x0fff ) | 0x4000,

			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			mt_rand( 0, 0x3fff ) | 0x8000,

			// 48 bits for "node"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		);
		//return ($param == "") ? $uuid : $uuid."_".$param;
		$uuid = ($param == "") ? $uuid : $uuid."_".$param;
		$uuid = date("Ym").$uuid;
		return $uuid;
		
	}
	
	$tx_id = get_uuid();
	
	echo "<pre>"; print_r($_GET); echo "</pre>";
	
	$msisdn = ($_GET["msisdn"] == "") ? "0" : $_GET["msisdn"];
	if (substr($msisdn,0,1) != "+") $msisdn = "+".$msisdn;
	//$from = ($_GET["msisdn"] == "") ? "0" : $_GET["msisdn"];
	$from = (!isset($_GET["shortcode"])) ? "0" : urldecode($_GET["shortcode"]); 
	$message = ($_GET["message"] == "") ? "" : $_GET["message"]; 	
	//$appsid = ($_GET["appsid"] == "") ? "" : $_GET["appsid"];
	//$tstamp = ($_GET["tstamp"] == "") ? now() : $_GET["tstamp"];
		
	//$sql = "INSERT INTO sms_received (msisdn, msgid, message, status, tstamp) VALUES (\"$msisdn\", \"$msgid\", \"$message\", \"$status\", \"$tstamp\")";
	
	$sql = "INSERT INTO sms_received (msisdn, shortcode, message, tstamp) VALUES (\"$msisdn\", \"$from\", \"$message\", \"".date("Y-m-d H:i:s")."\")";
	
	//$sql = "INSERT INTO sms_received (msisdn, message, tstamp) VALUES (\"$msisdn\", \"$message\", \"".date("Y-m-d H:i:s")."\")";
	//echo $sql."<hr>"; 
	$sql = mysql_query($sql); 
	
	$msisdn = substr($msisdn,1);
	 
	$sms  = CORE_URL."get_sms.php?msisdn=".urlencode($msisdn)."&message=".urlencode($message).""; 		
	//echo str_replace("10.99.4.5", "110.138.141.255", $sms)."<hr>";			
	$sms = file_get_contents($sms);		
	$sms = json_decode($sms, TRUE);	
	
	$reply = $sms["message"];				
	$number = $sms["msisdn"]; 
	//echo $reply."<hr>";
	
	//FOR NUMBER MDN
	$number = substr($number,3);
	$number_mdn = $number;	//NOMOR MDN MENGGUNAKAN TANPA 0
	$number_mdn0 = "0".$number;	//NOMOR MDN MENGGUNAKAN TANPA 0
	
	//FOR MESSAGE
	//$arr = str_split($reply, 9);
	$arr = explode(" ",$reply);
	$var = strtoupper($arr[0]);
	
	switch ($var)
	{
		case "REG" : include_once("reg.php"); break;
		case "UNREG" : include_once("unreg.php"); break;
		case "KERJA" : include_once("kerja.php"); break;
		default : $a = file_get_contents("http://10.99.1.5:8085/sendsms.php?msisdn=$number_mdn&message=".urlencode("Maaf keyword yg Anda masukkan salah.Ketik KERJA INFO sms ke 818 utk pilihan pekerjaan.CS: 02160908000")."&appsid=GRAMEEN&msgid=".time()); break;
//		default : $a = file_get_contents("http://10.99.4.5:8085/sendsms.php?msisdn=$number_mdn&message=".urlencode("Maaf keyword yg Anda masukkan salah.Ketik KERJA INFO sms ke 818 utk pilihan pekerjaan.CS: 02160908000")."&appsid=GRAMEEN&msgid=".time()); break;
	}
	//echo $var."<hr>";
	//die();

?>