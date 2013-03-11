<?php
	//$msg_err = "Cek lagi keywrd pekrjaan: PABRIK,BURUH,KASIR,STAF,SALES,PEMBANTU,PELAYAN,MONTIR,SUPIR,SATPAM,PERAWAT,GURU,KOKI,LAINNYA.Daftar: REG KERJA (pekerjaan) sms ke 818";
	
	write_logfile($number_mdn, "SMSG:Check $sms_keyword[1] keyword.");
	if ($arr_message[1] == $sms_keyword[1])
	{
		$jobcat_key = $arr_message[2];
		if ($jobcat_key != "")
		{
			//CEK APAKAH KEYWORD JOB ADA DI DATABASE
			write_logfile($number_mdn, "SMSG:$sms_keyword[1] keyword valid. Check Job category keyword.");
			$job = CORE_URL."get_jobcat.php?tx_id=$tx_id&key=jobcat_key&value=$jobcat_key";
			echo $job."<hr>";
			$job = get_data($job);
			if ($job["nrows"] > 0)
			{
				write_logfile($number_mdn, "SMSG:Job category keyword valid.");
				$data = CORE_URL."add_logsmsg.php?tx_id=$tx_id&mdn=$number_mdn0".
					"&sms_type=MO:REG&message=".strtoupper(urlencode($message))."&dtime_add=".urlencode($dtime_micro);
				echo $data."<hr>";				
				$data = get_data($data);
				
				$jobcat_id = $job["results"][0]["jobcat_id"];
				
				//CEK APAKAH MDN SUDAH TERDAFTAR SEBAGAI SUBSCRIBER
				write_logfile($number_mdn, "SMSG:Check MDN Registration.");
				$data = CORE_URL."get_subscriber_by_mdn.php?tx_id=$tx_id&mdn=".urlencode($number_mdn0)."";	//CHECK APAKAH NOMOR MDN INI SUDAH ADA APA BELUM
				echo $data."<hr>";
				$data = get_data($data);
				
				if($data["totaldata"] == 0)	
				{
					write_logfile($number_mdn, "SMSG:MDN doesn't exists. Register MDN.");
					$data  = CORE_URL."add_subscriber.php?tx_id=".urlencode($tx_id)."&mentor_id=2&name=".urlencode('SMS')."&loc_id=".urlencode('164586577')."&mdn=$number_mdn0&date_add=".urlencode(date("Y-m-d H:i:s"))."&date_update=".urlencode(date("Y-m-d H:i:s"))."&status=1";            
					echo $data."<hr>";
					$data = get_data($data);
					$subscriber_id = $data["subscriber_id"];
				}
				else 
				{ 
					write_logfile($number_mdn, "SMSG:MDN existed. Get subscriber_id");
					$subscriber_id = $data["subscriber_id"]; 
				}
					
				//CEK APAKAH SUBSCRIBER MASIH BERLANGGANAN JOB CATEGORY YANG DIREQUEST				
				//$sent = CORE_URL."get_rel_subscriber_mdn.php?tx_id=$tx_id&mdn=".urlencode($number_mdn0)."&jobcat_key=".urlencode($reply)."&tstamp=".urlencode($tstamp)."";	
				$total_rel = 0;
				write_logfile($number_mdn, "SMSG:Check user subscription on registered job category.");
				
				$data = CORE_URL."get_rel_subscriber_active.php?tx_id=$tx_id&key=subscriber_id|jobcat_id|status&value=$subscriber_id"."|".$jobcat_id."|1";	
				echo $data."<hr>";
				$data = get_data($data);
				$total_rel = $data["totaldata"];	
				echo $total_rel."<hr>";
				
				$data = CORE_URL."get_rel_subscriber_active.php?tx_id=$tx_id&key=subscriber_id|jobcat_id|status&value=$subscriber_id"."|".$jobcat_id."|2";	
				echo $data."<hr>";
				$data = get_data($data);
				$total_rel += $data["totaldata"];	
				echo $total_rel."<hr>";
				
				$data = CORE_URL."get_rel_subscriber_active.php?tx_id=$tx_id&key=subscriber_id|jobcat_id|status&value=$subscriber_id"."|".$jobcat_id."|3";	
				echo $data."<hr>";
				$data = get_data($data);
				$total_rel += $data["totaldata"];	
				echo $total_rel."<hr>";
				
				$data = CORE_URL."get_rel_subscriber_active.php?tx_id=$tx_id&key=subscriber_id|jobcat_id|status&value=$subscriber_id"."|".$jobcat_id."|6";	
				echo $data."<hr>";				
				$data = get_data($data);
				$total_rel += $data["totaldata"];	
				echo $total_rel."<hr>";
				
				//JIKA MASIH TERDAFTAR DENGAN STATUS AUTO-RENEW OR UNREG BY ADMIN OR UNREG BY USER OR PERNAH REG
				if($total_rel == 0)				
				{
					$data = CORE_URL."add_subscriber_jobcat.php?tx_id=$tx_id&subscriber_id=$subscriber_id&jobcat_id=$jobcat_id".
						"&jobcat_key=$jobcat_key&date_add=".urlencode($dtime)."&date_active=0000-00-00&status=6&date_expired=0000-00-00";
					echo $data."<hr>";				
					$data = get_data($data);
					print_r($data); echo "<hr>";
					$rel_id = $data[rel_id];
					
					$data = CORE_URL."add_subscription_smsg.php?tx_id=$tx_id&rel_subscription_id=$rel_id&sms_type=MO:REG&dtime_add=".urlencode($dtime_micro);
					echo $data."<hr>";				
					$data = get_data($data);

					write_logfile($number_mdn, "SMSG:REG request accepted.");
					//"Anda akan mendaftar layanan info pkrjaan %1% dr PT RUMA.Rp3rb/mg.1sms/hr.Jk setuju balas dg ktik ".$sms_keyword[1]." %1% OK.CS:02160908000";
					$msg = str_replace("%1%", $jobcat_key, $msg_notif[2]);
					echo $a = send_sms($number_mdn, $msg, $tx_id);
					
				}
				else
				{
					write_logfile($number_mdn, "SMSG:REG request rejected. Reason: Active subscription found.");
					//"Saat ini Anda masih terdaftar pada lowongan pekerjaan %1%.Utk info pekerjaan selengkapnya ketik KERJA INFO sms ke 818";
					$msg = str_replace("%1%", $jobcat_key, $msg_notif[7]);
					echo $a = send_sms($number_mdn, $msg, $tx_id);
					
				}
			}
			else
			{ 
				write_logfile($number_mdn, "SMSG:REG request rejected. Reason: Invalid job category keyword.");
				echo $a = send_sms($number_mdn, $msg_notif[6], $tx_id); 
			}
		}
		else
		{ 
			write_logfile($number_mdn, "SMSG:REG request rejected. Reason: Job category is empty.");
			echo $a = send_sms($number_mdn, $msg_notif[6], $tx_id); 
		}
	}
	else
	{ 
		write_logfile($number_mdn, "SMSG:REG request rejected. Reason: Use 'REG $sms_keyword[1]' instead of 'REG $arr_message[1]'.");
		echo $a = send_sms($number_mdn, $msg_notif[6], $tx_id); 
	}
	
	echo "</pre>";
?>