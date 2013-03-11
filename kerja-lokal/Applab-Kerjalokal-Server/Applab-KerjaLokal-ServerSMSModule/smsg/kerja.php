<?php
	function potong_pulsa($tx_id, $number_mdn, $jobcat_ID, $jobcat_key)
	{
		$tarif = CORE_URL."get_tarif.php?tx_id=".urlencode($tx_id)."&key=id&value=1";
		echo $tarif."<hr>";
		$tarif = get_data($tarif);
		$tarif = $tarif["results"][0]["tarif"];

		$check_mdn = curl_init();
		curl_setopt($check_mdn, CURLOPT_URL,            BASE_URL."check_mdn.php?mdn=$number_mdn&tx_id=$tx_id" );
		curl_setopt($check_mdn, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($check_mdn, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($check_mdn, CURLOPT_HEADER, 		0);
		curl_setopt($check_mdn, CURLOPT_TIMEOUT,        10);
		$check_mdn = curl_exec($check_mdn);
		echo $check_mdn."<hr>";
		
		//$tarif = ($check_mdn == "pre") ? "1100000000" : "1000000000";
		$tarif = ($check_mdn == "pre") ? ($tarif * 1.1) : ($tarif * 1);

		//$status_pulsa = file_get_contents("http://10.99.4.1/grameen/infokerja/potong_pulsa.php?mdn=$number_mdn&tarif=2&pospre=$check_mdn&tx_id=$tx_id");
		$status_pulsa = curl_init();
		curl_setopt($status_pulsa, CURLOPT_URL,            BASE_URL."potong_pulsa2.php?mdn=$number_mdn&tarif=$tarif&pospre=$check_mdn&tx_id=$tx_id&jobcat_ID=$jobcat_ID&jobcat_key=$jobcat_key" );
		curl_setopt($status_pulsa, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($status_pulsa, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($status_pulsa, CURLOPT_HEADER, 		0);
		curl_setopt($status_pulsa, CURLOPT_TIMEOUT,        10);
		$status_pulsa = curl_exec($status_pulsa);
		echo $status_pulsa."<hr>";
		//$status_pulsa = potong_pulsa(substr($number_mdn,1), 2, $check_mdn, $tx_id);
		//$status_pulsa = potong_pulsa(substr($number_mdn,1), 10, $check_mdn, $tx_id, $jobcat_ID, $jobcat_key, $cid);		
		
		$cdr = explode("|",$status_pulsa);
		$status_pulsa1 = $cdr[0] == "1" ? "1" : $status_pulsa;
		//echo $status_pulsa."<hr>";
		$log_id = $cdr[1];
		$mdn  = $number_mdn; 
		$pospre = ($cdr[3] == 'pos') ? '1':'2';
		//$tarif = ($cdr[3] == 'pos') ? '10':'11';
		$billing_status = $cdr[5];
		$status_code = $cdr[6];
		$status_code_desc = $cdr[7];
		//$jobcat_ID = $cdr[8];
		//$jobcat_key = $cdr[9];
		
		
		//$check_mdn = file_get_contents("http://10.99.4.1/grameen/infokerja/write_cdr.php?mdn=$number_mdn&tx_id=$tx_id");
		$write_cdr = curl_init();
		curl_setopt($write_cdr, CURLOPT_URL,            BASE_URL."write_cdr.php?log_id=$log_id&mdn=$number_mdn&pospre=$pospre&tarif=$tarif&billing_status=$billing_status&status_code=$status_code&service_name=SUBSCRIPTION&status_code_desc=".urlencode($status_code_desc)."&jobcat_ID=$jobcat_ID&jobcat_key=".urlencode($jobcat_key));
		curl_setopt($write_cdr, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($write_cdr, CURLOPT_TIMEOUT,        10);
		$write_cdr = curl_exec($write_cdr);
		
		return $status_pulsa1;
	}
	
	function renew($tx_id, $rel_id, $dtime, $subscriber_id, $jobcat_id, $msg, $number_mdn)
	{
		$curr_date = date("Y-m-d", strtotime($dtime));
		$dtime_micro = dtime_micro($curr_date);
		$date_active = $curr_date;
		$date_expired = date("Y-m-d", (strtotime($curr_date) + (7 * 86400)));
		if (DOUBLECONF == "0")
		{
			$data = CORE_URL."update_rel_subscriber.php?tx_id=$tx_id&key=rel_id".
				"&value=$rel_id&update_key=status|date_update|date_active|dtime_active|date_expired|update_by|update_by_name|update_notes".
				"&update_value=1|".urlencode($dtime)."|".$curr_date."|".urlencode($dtime)."|".$date_expired."|".$subscriber_id."|SUBS_".$subscriber_id."|".urlencode("MT:REG OK");
			echo $data."<hr>";
			$data = get_data($data);
		}
		else
		{
			$data = CORE_URL."update_rel_subscriber.php?tx_id=$tx_id&key=rel_id".
				"&value=$rel_id&update_key=status|date_update|date_active|dtime_active|date_expired|update_by|update_by_name|update_notes".
				"&update_value=2|".urlencode($dtime)."|".$curr_date."|".urlencode($dtime)."|".$date_expired."|".$subscriber_id."|SUBS_".$subscriber_id."|".urlencode("MT:REG OK");
			echo $data."<hr>";
			$data = get_data($data);
			echo $a = send_sms($number_mdn, $msg, $tx_id);
			write_logfile($number_mdn, "SMSG:MT: $msg");
		}

		write_logfile($number_mdn, "SMSG:Job matching");
		$data = CORE_URL."add_jobsend.php?tx_id=".urlencode($tx_id)."&jobcat_id=$jobcat_id&rel_id=$rel_id&date_add=".urlencode($dtime)."&date_send=$curr_date&subscriber_id=$subscriber_id";
		echo $data."<hr>";
		$data = get_data($data);
		
		$jobsend_id = $data["jobsend_id"];
						
		$data = SMSG_URL."send_sms_by_jobsend_id.php?tx_id=".urlencode($tx_id)."&jobsend_id=$jobsend_id";					
		echo $data."<hr>";
		echo file_get_contents($data)."<hr>";
		
		$data = CORE_URL."add_subscription_smsg.php?tx_id=$tx_id&rel_subscription_id=$rel_id&sms_type=".urlencode("MO:KERJA OK")."&dtime_add=".urlencode($dtime_micro);
		echo $data."<hr>";				
		$data = get_data($data);
		
	}
	
	$status_pulsa = "";
	$check_mdn = "";
	$write_cdr = "";

	$keyword = $arr_message[1];
	
	$msg_err = "Maaf keyword yg Anda masukkan salah.Ketik KERJA INFO sms ke 818 utk pilihan pekerjaan.CS: 02160908000";
	switch ($keyword)
	{
		case "INFO" : 
			$msg = "Pilih pekerjaan: PABRIK,BURUH,KASIR,STAF,SALES,PEMBANTU,PELAYAN,MONTIR,SUPIR,SATPAM,PERAWAT,GURU,KOKI,LAINNYA.Daftar: REG KERJA (pekerjaan) sms ke 818";
			$a = file_get_contents("http://10.99.1.5:8085/sendsms.php?msisdn=$number_mdn&message=".urlencode($msg)."&appsid=GRAMEEN&msgid=".time()); 
			break;
			
		case "TENTANG" : 
			$msg = "Ktk REG KERJA (supir/sales/admin) sms ke 818 utk info lowongan lgsg d hp Anda.ch:REG KERJA SUPIR.Rp3Rb/mg,1SMS/hr.Kerja Lokal lain ktk KERJA INFO.CS:02160908000";
			$a = file_get_contents("http://10.99.1.5:8085/sendsms.php?msisdn=$number_mdn&message=".urlencode($msg)."&appsid=GRAMEEN&msgid=".time()); 
			break;
		
		case "PROFIL" : 
			include_once("kerja_profil.php");
			break;
		
		case "LOKASI" : 
			include_once("kerja_lokasi.php");
			break;
		
		case "AKTIF" : 
			
			$sent = CORE_URL."get_subscriber_by_mdn.php?tx_id=$tx_id&mdn=".urlencode($number_mdn0)."";	//CHECK APAKAH NOMOR MDN INI SUDAH ADA APA BELUM
			//echo str_replace("10.99.1.5", "180.243.231.8", $sent)."<hr>";				
			//die();
			$sent = file_get_contents($sent);
			$sent = json_decode($sent, TRUE);
			$totaldata = $sent["totaldata"];
			
			if($totaldata != 0)	//JIKA NOMOR MDN SUDAH ADA
			{
				//$msg = "Pekerjaan: SUPIR,STAF,GURU ,PELAYAN,BURUH,SATPAM,SALES ,KOKI,MONTIR,KASIR,PERAWAT,PEMBANTU. Kerja Lokal lain ktk KERJA INFO sms ke 818.";
				//$a = file_get_contents("http://10.99.1.5:8085/sendsms.php?msisdn=$number_mdn&message=".urlencode($msg)."&appsid=GRAMEEN&msgid=".time()); 
				$data = CORE_URL."get_rel_subscriber_by_subscriber.php?mdn=".urlencode($number_mdn0)."&status=1";	
				//echo str_replace("10.99.1.5", "180.243.231.8", $data)."<hr>";				
				$data = file_get_contents($data);
				$data = json_decode($data, TRUE);
				//echo "<pre>"; print_r($data); echo "</pre>";
				
				
				//$mdn = $unreg['mdn'];
				
				
				if($data["totaldata"] == 0)
				{
					$msg = "Anda belum terdaftar.Ktk REG KERJA(supir/sales/admin) sms ke 818 utk mndaftar.ch:REG KERJA SUPIR.Rp3Rb/mg,1SMS/hr.Kerja Lokal lain ktk KERJA INFO.CS: 02160908000";
					$a = file_get_contents("http://10.99.1.5:8085/sendsms.php?msisdn=$number_mdn&message=".urlencode($msg)."&appsid=GRAMEEN&msgid=".time()); 
					
				}
				else
				{
					
					$numUpdate = NULL;
					$idAdder = 0;
					$strShow = null;
					$category = null;
					
						
					//echo "hello";
					foreach($data["results"] as $unreg)
					{
						$subscriber_id = $unreg['subscriber_id'];
						$jobcat_key = $unreg['jobcat_key'];
						$strShow .= $jobcat_key."|";						
						++$idAdder;		                	
					}
					
					//++$numUpdate;	
					$strShow = $strShow;
					
					$strShow = explode("|", $strShow);
					for($i = 0; $i < count($strShow); $i++){				
						$category .= $strShow[$i]. ",";		
					}
					
					//$category = $category;	
					$category = rtrim($category);
					$category = substr($category,0,strlen($category)-2);
					$a = file_get_contents("http://10.99.1.5:8085/sendsms.php?msisdn=$number_mdn&message=".urlencode("Pekerjaan: $category. Kerja Lokal lain ktk KERJA INFO sms ke 818")."&appsid=GRAMEEN&msgid=".time());
						
				}
			}
			else
			{
				$msg = "Anda belum terdaftar.Ktk REG KERJA(supir/sales/admin) sms ke 818 utk mndaftar.ch:REG KERJA SUPIR.Rp3Rb/mg,1SMS/hr.Kerja Lokal lain ktk KERJA INFO.CS: 02160908000";
				$a = file_get_contents("http://10.99.1.5:8085/sendsms.php?msisdn=$number_mdn&message=".urlencode($msg)."&appsid=GRAMEEN&msgid=".time());
			}
			
			break;	

		
		default :
			$jobcat_key = $arr_message[1];
			//$dtime = date("Y-m-d H:i:s");
			write_logfile($number_mdn, "SMSG:Check Job category not empty.");
			if ($jobcat_key != "")
			{
				//CEK APAKAH KEYWORD JOB ADA DI DATABASE
				write_logfile($number_mdn, "SMSG:Job category not empty. Check Job category validity.");
				$job = CORE_URL."get_jobcat.php?tx_id=$tx_id&key=jobcat_key&value=$jobcat_key";
				echo $job."<hr>";
				$job = get_data($job);
				if ($job["nrows"] > 0)
				{
					write_logfile($number_mdn, "SMSG:Job category keyword valid.");
					$jobcat_id = $job["results"][0]["jobcat_id"];
					
					//CEK APAKAH MDN SUDAH TERDAFTAR SEBAGAI SUBSCRIBER
					write_logfile($number_mdn, "SMSG:Check MDN Registration.");
					$data = CORE_URL."get_subscriber_by_mdn.php?tx_id=$tx_id&mdn=".urlencode($number_mdn0)."";	//CHECK APAKAH NOMOR MDN INI SUDAH ADA APA BELUM
					echo $data."<hr>";
					$data = get_data($data);
					
					if($data["totaldata"] > 0)	
					{
						write_logfile($number_mdn, "SMSG:MDN existed. Get subscriber_id");
						$subscriber_id = $data["subscriber_id"];
						$keyword2 = $arr_message[2];
						if ($keyword2 == "OK") 			include_once("kerja_ok.php");
						else if ($keyword2 == "YA")		include_once("kerja_ya.php");
						else { echo $a = send_sms($number_mdn, $msg_notif[1], $tx_id); }
					}
					else 
					{ 
						echo $a = send_sms($number_mdn, $msg_notif[1], $tx_id); 
						write_logfile($number_mdn, "SMSG:$sms_keyword[1] request rejected. Reason: MDN doesn't exists.");
						write_logfile($number_mdn, "SMSG:MT: $msg_notif[1]");
					}
				}
				else 
				{ 
					echo $a = send_sms($number_mdn, $msg_notif[6], $tx_id); 
					write_logfile($number_mdn, "SMSG:$sms_keyword[1] request rejected. Reason: Invalid job category keyword");
					write_logfile($number_mdn, "SMSG:MT: $msg_notif[6]");
				}
			}
			else 
			{ 
				echo $a = send_sms($number_mdn, $msg_notif[6], $tx_id); 
				write_logfile($number_mdn, "SMSG:$sms_keyword[1] request rejected. Reason: Job category is empty");
				write_logfile($number_mdn, "SMSG:MT: $msg_notif[6]");
			}
			break;
	}
?>