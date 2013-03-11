<?php
	$data = CORE_URL."add_logsmsg.php?tx_id=$tx_id&mdn=$number_mdn0".
		"&sms_type=".urlencode("MO:KERJA PROFIL")."&message=".strtoupper(urlencode($message))."&dtime_add=".urlencode($dtime_micro);
	echo $data."<hr>";				
	$data = get_data($data);
	
	write_logfile($number_mdn, "SMSG:Check MDN validity.");

	$data = CORE_URL."get_subscriber_by_mdn.php?tx_id=$tx_id&mdn=".urlencode($number_mdn0)."";	//CHECK APAKAH NOMOR MDN INI SUDAH ADA APA BELUM
	echo $data."<hr>";				
	$data = get_data($data);
	print_r($data);
	$name = $data["name"];
	$subscriber_id = $data["subscriber_id"];
	
	if($data["totaldata"] > 0)	//JIKA NOMOR MDN SUDAH ADA
	{
		write_logfile($number_mdn, "SMSG:MDN registered. Check PROFIL value.");

		//echo "test";
		// $sms = trim(substr($_GET["message"],13));
		// echo $sms."<hr>";
		
		// $msg_err = "Maaf keyword yg Anda masukkan salah.Ketik KERJA INFO sms ke 818 utk pilihan pekerjaan.CS: 02160908000";
		
		echo $profil = $arr_message[2];
		echo "<hr>";
		if ($profil != "")	
		{	
			write_logfile($number_mdn, "SMSG:PROFIL submitted. Check date of birth value.");
			// $sent = CORE_URL."get_subscriber_by_mdn.php?tx_id=$tx_id&mdn=".urlencode($number_mdn0)."";	//CHECK APAKAH NOMOR MDN INI SUDAH ADA APA BELUM
			// echo str_replace("10.99.1.5", "180.243.231.8", $sent)."<hr>";				
			
			// if($totaldata != 0)
			
			$sms = explode("#", $profil);
			$nama = $sms[0];
			$birthday = $sms[1];
			$education = (strlen($education) <= 4) ? strtoupper($sms[2]) : ucfirst(strtolower($sms[2]));
			//$date = strtotime($date);
			echo "$nama - $birthday - $education <hr>";
			
			if($birthday != "")
			{
				write_logfile($number_mdn, "SMSG:PROFIL date of birth submitted. Check date of birth validity.");
				//tg/bln/th lahir
				//$date = date("Y-m-d", $date);
				$birthday = str_replace("/","-",$birthday);
				$a = explode("-",$birthday);
				$birthday = $a[2]."-".$a[1]."-".$a[0];
				$checkdate = checkdate($a[1], $a[0], $a[2]);
				if ($checkdate == true)
				{
					write_logfile($number_mdn, "SMSG:PROFIL date of birth valid. Check education value.");
					if($education != "")
					{
						write_logfile($number_mdn, "SMSG:PROFIL education submitted. Check education validity.");
						//$edu_title    = $education;				      
						$data = CORE_URL."get_education.php?tx_id=".urlencode($tx_id)."&id=$education";
						// $totaldata = $data["totaldata"];
						echo $data."<hr>";
						$data = get_data($data);
						print_r($data);
						if($data["totaldata"] > 0)
						{
							
							$education_id = $data["edu_id"];
							//$education_id;
							//echo edu_id."<hr>";
							
							//$sent = CORE_URL."update_subscriber.php?tx_id=".urlencode($tx_id)."&subscriber_id=$subscriber_id&name=".urlencode($nama)."&birthday=".urlencode(date("Y-m-d", $date))."&edu_id=".urlencode($education_id)."";
							$data = CORE_URL."update_subscriber.php?tx_id=$tx_id&subscriber_id=$subscriber_id&name=".urlencode($nama)."&birthday=$birthday&edu_id=$education_id";
							echo $data."<hr>";
							$data = get_data($data);
							print_r($data);
							
							$data = CORE_URL."update_log_sms_reminder.php?subscriber_id=$subscriber_id&sms_type=2&sms_status=2&date_update=".urlencode($dtime);	
							echo $data."<hr>";
							$data = get_data($data);
							print_r($data);
							
							// $msg = "Terima kasih. Data profil Anda telah disimpan";
							// $a = file_get_contents("http://10.99.1.5:8085/sendsms.php?msisdn=$number_mdn&message=".urlencode($msg)."&appsid=GRAMEEN&msgid=".time()); 
							echo $a = send_sms($number_mdn, $msg_notif[18], $tx_id);
							write_logfile($number_mdn, "SMSG:$sms_keyword[1] PROFIL request accepted. Education valid.");
							write_logfile($number_mdn, "SMSG:MT: $msg_notif[18]");
						}
						else
						{
							// $a = file_get_contents("http://10.99.1.5:8085/sendsms.php?msisdn=$number_mdn&message=".urlencode($msg_err)."&appsid=GRAMEEN&msgid=".time());
							echo $a = send_sms($number_mdn, $msg_notif[1], $tx_id);
							write_logfile($number_mdn, "SMSG:$sms_keyword[1] PROFIL request rejected. Reason: PROFIL education doesn't valid");
							write_logfile($number_mdn, "SMSG:MT: $msg_notif[1]");
						}
					}
					else
					{
						echo $a = send_sms($number_mdn, $msg_notif[1], $tx_id);
						write_logfile($number_mdn, "SMSG:$sms_keyword[1] PROFIL request rejected. Reason: PROFIL education is empty");
						write_logfile($number_mdn, "SMSG:MT: $msg_notif[1]");
					}
				}
				else
				{
					echo $a = send_sms($number_mdn, $msg_notif[1], $tx_id);
					write_logfile($number_mdn, "SMSG:$sms_keyword[1] PROFIL request rejected. Reason: PROFIL date of birth doesn't valid");
					write_logfile($number_mdn, "SMSG:MT: $msg_notif[1]");
				}
			}
			else
			{
				// $a = file_get_contents("http://10.99.1.5:8085/sendsms.php?msisdn=$number_mdn&message=".urlencode($msg_err)."&appsid=GRAMEEN&msgid=".time());
				echo $a = send_sms($number_mdn, $msg_notif[1], $tx_id);
				write_logfile($number_mdn, "SMSG:$sms_keyword[1] PROFIL request rejected. Reason: PROFIL date of birth is empty");
				write_logfile($number_mdn, "SMSG:MT: $msg_notif[1]");
			}
			
			//update log_sms_reminder		
			
			/*
			else
			{
				$msg_err = "Anda belum terdaftar.Ktk REG KERJA(staf/sales/supir) sms ke 818 utk mendaftar.ch:REG KERJA SUPIR.Rp3Rb/mg,1SMS/hr.Kerja Lokal lain ktk KERJA INFO.CS: 02160908000";
				$a = file_get_contents("http://10.99.1.5:8085/sendsms.php?msisdn=$number_mdn&message=".urlencode($msg_err)."&appsid=GRAMEEN&msgid=".time());
			}
			*/
		
		}
		else
		{
			// $msg = "Maaf keyword tidak lengkap.Ktk KERJA PROFIL (nama)#(tg/bln/th lahir)#(pnddkn: sd/sltp/slta/diploma/sarjana) ke 818.ch:KERJA PROFIL ADI#13/03/1983#SLTP";
			// $a = file_get_contents("http://10.99.1.5:8085/sendsms.php?msisdn=$number_mdn&message=".urlencode($msg)."&appsid=GRAMEEN&msgid=".time());
			echo $a = send_sms($number_mdn, $msg_notif[19], $tx_id);
			write_logfile($number_mdn, "SMSG:$sms_keyword[1] PROFIL request rejected. Reason: PROFIL is empty.");
			write_logfile($number_mdn, "SMSG:MT: $msg_notif[19]");
		}
	}
	else
	{
		// $msg = "Anda belum terdaftar.Ktk REG KERJA(staf/sales/supir) sms ke 818 utk mendaftar.ch:REG KERJA SUPIR.Rp3Rb/mg,1SMS/hr.Kerja Lokal lain ktk KERJA INFO.CS: 02160908000";
		// $a = file_get_contents("http://10.99.1.5:8085/sendsms.php?msisdn=$number_mdn&message=".urlencode($msg)."&appsid=GRAMEEN&msgid=".time()); 
		echo $a = send_sms($number_mdn, $msg_notif[16], $tx_id);
		write_logfile($number_mdn, "SMSG:$sms_keyword[1] PROFIL request rejected. Reason: MDN doesn't exist.");
		write_logfile($number_mdn, "SMSG:MT: $msg_notif[16]");
	}
?>