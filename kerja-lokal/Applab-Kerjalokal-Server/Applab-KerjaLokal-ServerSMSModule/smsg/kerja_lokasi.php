<?php
	$data = CORE_URL."add_logsmsg.php?tx_id=$tx_id&mdn=$number_mdn0".
		"&sms_type=".urlencode("MO:KERJA LOKASI")."&message=".strtoupper(urlencode($message))."&dtime_add=".urlencode($dtime_micro);
	echo $data."<hr>";				
	$data = get_data($data);
	
	write_logfile($number_mdn, "SMSG:Check MDN validity.");

	$data = CORE_URL."get_subscriber_by_mdn.php?tx_id=$tx_id&mdn=".urlencode($number_mdn0)."";	//CHECK APAKAH NOMOR MDN INI SUDAH ADA APA BELUM
	echo $data."<hr>";				
	$data = get_data($data);
	print_r($data);
	$subscriber_id = $data["subscriber_id"];
	
	if($data["totaldata"] > 0)	//JIKA NOMOR MDN SUDAH ADA
	{
		write_logfile($number_mdn, "SMSG:MDN registered. Check LOKASI value.");
		//$sms = trim(substr($_GET["message"],13));
		//echo $sms;
		//$msg_err = "Maaf keyword yg Anda masukkan salah.Ketik KERJA INFO sms ke 818 utk pilihan pekerjaan.CS: 02160908000";
		$lokasi = strtoupper($arr_message[2]);
		if ($lokasi != "")
		{
			write_logfile($number_mdn, "SMSG:LOKASI submitted. Check LOKASI validity.");
			// $sent = CORE_URL."get_subscriber_by_mdn.php?tx_id=$tx_id&mdn=".urlencode($number_mdn0)."";	//CHECK APAKAH NOMOR MDN INI SUDAH ADA APA BELUM
			// $sent = file_get_contents($sent);
			// $sent = json_decode($sent, TRUE);
			$loc_id = "";
			// if($sent["totaldata"] != 0)	//JIKA NOMOR MDN SUDAH ADA
			
			//$lokasi = strtoupper($sms);
			switch($lokasi)
			{
				case "JAKSEL" : $loc_id = "1255010716"; break;
				case "JAKBAR" : $loc_id = "1148010445"; break;
				case "JAKUT" : $loc_id = "1443010324"; break;
				case "JAKPUS" : $loc_id = "103109731"; break;
				case "JAKTIM" : $loc_id = "133207531"; break;
				case "BEKASI" : $loc_id = "173406428"; break;
				case "TANGERANG" : $loc_id = "158106716"; break;
				case "DEPOK" : $loc_id = "164225811"; break;
				case "BOGOR" : $loc_id = "161296042"; break;
				case "TANGSEL" : $loc_id = "1531012127"; break;
			}
			
			if ($loc_id != "")
			{
				$data = CORE_URL."update_subscriber.php?subscriber_id=$subscriber_id&loc_id=$loc_id";
				echo $data."<hr>";				
				$data = get_data($data);
				print_r($data);
				
				$data = CORE_URL."update_log_sms_reminder.php?subscriber_id=$subscriber_id&sms_type=3&sms_status=2&date_update=".urlencode(date('Y-m-d H:i:s'));	
				echo $data."<hr>";				
				$data = get_data($data);
				print_r($data);
				
				// $msg = "Terima kasih. Data lokasi Anda telah disimpan";
				// $a = file_get_contents("http://10.99.1.5:8085/sendsms.php?msisdn=$number_mdn&message=".urlencode($msg)."&appsid=GRAMEEN&msgid=".time()); 
				
				echo $a = send_sms($number_mdn, $msg_notif[15], $tx_id);
				write_logfile($number_mdn, "SMSG:$sms_keyword[1] LOKASI request accepted. Subscriber loc_id updated.");
				write_logfile($number_mdn, "SMSG:MT: $msg_notif[15]");
			}
			else
			{
				//$a = file_get_contents("http://10.99.1.5:8085/sendsms.php?msisdn=$number_mdn&message=".urlencode($msg_err)."&appsid=GRAMEEN&msgid=".time());
				//echo $a = send_sms($number_mdn, $msg_notif[1], $tx_id);
				echo $a = send_sms($number_mdn, $msg_notif[17], $tx_id);
				write_logfile($number_mdn, "SMSG:$sms_keyword[1] LOKASI request rejected. Reason: LOKASI is empty");
				write_logfile($number_mdn, "SMSG:MT: $msg_notif[17]");
			}

			/*
			else
			{
				// $msg = "Anda belum terdaftar.Ktk REG KERJA(supir/sales/admin) sms ke 818 utk mendaftar.ch:REG KERJA SUPIR.Rp3Rb/mg,1SMS/hr.Kerja Lokal lain ktk KERJA INFO.CS: 02160908000";
				// $a = file_get_contents("http://10.99.1.5:8085/sendsms.php?msisdn=$number_mdn&message=".urlencode($msg)."&appsid=GRAMEEN&msgid=".time()); 
			}
			*/
		}
		else
		{
			// $msg = "Maaf keyword yg Anda masukkan tdk lengkap.Ketik KERJA LOKASI (jaksel/jakbar/jakut/jakpus/jaktim/bekasi/tangerang/depok/bogor) sms ke 818.ch:KERJA LOKASI DEPOK";
			// $a = file_get_contents("http://10.99.1.5:8085/sendsms.php?msisdn=$number_mdn&message=".urlencode($msg)."&appsid=GRAMEEN&msgid=".time()); 
			echo $a = send_sms($number_mdn, $msg_notif[17], $tx_id);
			write_logfile($number_mdn, "SMSG:$sms_keyword[1] LOKASI request rejected. Reason: LOKASI is empty");
			write_logfile($number_mdn, "SMSG:MT: $msg_notif[17]");
		}
		
	}
	else
	{
		// $msg = "Anda belum terdaftar.Ktk REG KERJA(supir/sales/staf) sms ke 818 utk mendaftar.ch:REG KERJA SUPIR.Rp3Rb/mg,1SMS/hr.Kerja Lokal lain ktk KERJA INFO.CS: 02160908000";
		// $a = file_get_contents("http://10.99.1.5:8085/sendsms.php?msisdn=$number_mdn&message=".urlencode($msg)."&appsid=GRAMEEN&msgid=".time()); 
		echo $a = send_sms($number_mdn, $msg_notif[16], $tx_id);
		write_logfile($number_mdn, "SMSG:$sms_keyword[1] LOKASI request rejected. Reason: MDN doesn't exist.");
		write_logfile($number_mdn, "SMSG:MT: $msg_notif[16]");
	}	
?>