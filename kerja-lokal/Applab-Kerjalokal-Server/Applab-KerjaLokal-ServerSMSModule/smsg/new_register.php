<?
	$reply = ($_GET["message"] == "") ? "" : $_GET["message"]; 
	$mdn = ($_GET["msisdn"] == "") ? "" : $_GET["msisdn"]; 
	
	
	$number = substr($mdn,3);
	$number_mdn = '0'.$number;
	
	$reply = substr($reply,10);
	$reply = strtoupper($reply);		
	
	
	$sent = CORE_URL."get_rel_subscriber_cat_by_jobcat.php?jobcat_key=".urlencode($reply)."";			
	$sent = file_get_contents($sent);
	$sent = json_decode($sent, TRUE);
	$jobcat_ID = $sent["jobcat_id"];		
	$jobcat_key = $sent["jobcat_key"];			
	$totaldata = $sent["totaldata"];			
	
	
	if($totaldata != 0)
	{	
		$json  = CORE_URL."add_subscriber.php?tx_id=".urlencode($tx_id)."&name=".urlencode('SMS')."&loc_id=".urlencode('164586577')."&mdn=".urlencode($number_mdn)."&date_add=".urlencode(date("Y-m-d H:i:s"))."&date_update=".urlencode(date("Y-m-d H:i:s"))."&status=1";            
		//echo str_replace("10.99.4.5", "110.138.141.255", $json)."<hr>";	
		//die($json);
		$json = file_get_contents($json);			
		$json = json_decode($json, TRUE);	
		
		
		$subscriber_id = $json["subscriber_id"];	//ID SUBSCRIBER		
		
		
		$json1 = CORE_URL."add_subscriber_jobcat_key.php?tx_id=".urlencode($tx_id)."&subscriber_id=".urlencode($subscriber_id)."";
		$json1 .= "&jobcat_id=".urlencode($jobcat_ID)."&jobcat_key=".urlencode($jobcat_key)."&date_add=".urlencode($date_add)."";
		$json1 .= "&date_send=".urlencode($date_send)."&date_active=".urlencode($date_active)."&status=1&date_expired=".urlencode($date_expired)."";
		//echo str_replace("10.99.4.5", "110.138.141.255", $json1)."<hr>";
		
		$json1 = file_get_contents($json1);			
		$json1 = json_decode($json1, TRUE);	
		
		$rel_id = $json1["rel_id"];	
	}
	else
	{
		
		$a = file_get_contents("http://10.99.1.5:8085/sendsms.php?msisdn=$number_mdn&message=".urlencode("Anda belum terdaftar.Ktk REG KERJA(supir/sales/admin) sms ke 818 utk mendaftar.ch:REG KERJA SUPIR.Rp3Rb/mg,1SMS/hr.Info kerja lain ktk KERJA INFO.CS: 02160908000")."&appsid=GRAMEEN&msgid=".time());
		
	}
	
?>