<?php
	//write_logfile($number_mdn, "SMSG:Check UNREG $sms_keyword[1] keyword.");
	if ($arr_message[1] == $sms_keyword[1])
	{
		//write_logfile($number_mdn, "SMSG:UNREG $sms_keyword[1] keyword valid. Check MDN Registration.");
		$data = CORE_URL."get_subscriber_by_mdn.php?tx_id=$tx_id&mdn=$number_mdn0";	//CHECK APAKAH NOMOR MDN INI SUDAH ADA APA BELUM
		echo $data."<hr>";
		$data = get_data($data);
		
		if($data["totaldata"] > 0)	
		{
			//write_logfile($number_mdn, "SMSG:MDN existed. Get subscriber_id");
			$subscriber_id = $data["subscriber_id"];
			$jobcat_key = $arr_message[2];
			if ($jobcat_key == "")
			{
				//write_logfile($number_mdn, "SMSG:Get active auto-renew subscription");
				$data = CORE_URL."get_rel_subscriber_active.php?tx_id=$tx_id&key=subscriber_id|status&value=$subscriber_id"."|1&order=date_expired";	
				echo $data."<hr>";
				$data = get_data($data);
				$jobcat_key = "";
				
				if($data["totaldata"] > 1)
				{
					if ($shortcode == "818")
					{
						foreach($data["results"] as $unreg)
						{ $jobcat_key .= $unreg['jobcat_key']." "; }
						$jobcat_key = trim($jobcat_key);
						
						//write_logfile($number_mdn, "SMSG:UNREG $sms_keyword[1] request accepted.");
						$msg = "Pkrjaan: $jobcat_key. Stop:unreg kerja (pkerjaan) sms k 818 CS: 02160908000";
						
						echo $a = send_sms($number_mdn, $msg, $tx_id);
					}
					else if ($shortcode == "8888")	
					{
						foreach($data["results"] as $unreg)
						{
							$rel_id = $unreg["rel_id"];
							$date_expired = $unreg["date_expired"];

							$data = CORE_URL."update_rel_subscriber.php?tx_id=$tx_id&key=rel_id&value=$rel_id&update_key=status|date_update|date_unsub|update_by|update_by_name|update_notes".
								"&update_value=2|".urlencode($dtime)."|".urlencode($curr_date)."|".$subscriber_id."|SUBS_".$subscriber_id."|".urlencode("MO:8888 UNREG $sms_keyword[1]");
							echo $data."<hr>";
							$data = get_data($data);
						}
						$msg = "Anda telah berhenti berlangganan layanan Kerja Lokal. Anda masih bisa mengakses layanan ini sampai dengan $date_expired dari Hape Esia Anda. Terima kasih";
						echo $a = send_sms($number_mdn, $msg, $tx_id, $shortcode);
					}
				}
				else if($data["totaldata"] == 1)
				{
					$unreg = $data["results"][0];
					$rel_id = $unreg['rel_id'];
					
					// $subs_update = CORE_URL."update_rel_subscriber_jobcat.php?rel_id=".$unreg["rel_id"]."&update_by=0&status=2&date_unsub=".urlencode(date('Y-m-d'))."&date_update=".urlencode(date('Y-m-d H:i:s'));	
					$subs_update = CORE_URL."update_rel_subscriber.php?tx_id=$tx_id&key=rel_id&value=$rel_id&update_key=status|date_update|date_unsub|update_by|update_by_name|update_notes".
						"&update_value=2|".urlencode($dtime)."|".urlencode($curr_date)."|".$subscriber_id."|SUBS_".$subscriber_id."|".urlencode("MO:$shortcode UNREG $sms_keyword[1]");
					echo $subs_update."<hr>";
					$subs_update = get_data($subs_update);
					
					if ($shortcode == "818") $sender = "81820";
					else if ($shortcode == "8888") $sender = "8888";
					
					$msg = "Anda telah menghentikan langganan info lowongan pekerjaan $jobcat_key.Info lowongan tetap dikirim hingga periode brakhir pd tgl $date_expired.CS:02160908000";
					echo $a = send_sms($number_mdn, $msg, $tx_id, $sender);
				}
				else if($data["totaldata"] == 0)
				{
					$msg_err = "Maaf keyword yg Anda masukkan salah.Ketik KERJA INFO sms ke 818 utk pilihan pekerjaan.CS: 02160908000";
					echo $a = send_sms($number_mdn, $msg_err, $tx_id);
				}
			}
			else
			{
				$job = CORE_URL."get_jobcat.php?tx_id=$tx_id&key=jobcat_key&value=$jobcat_key";
				echo $job."<hr>";
				$job = get_data($job);
				if ($job["nrows"] > 0)
				{
					$rel = CORE_URL."get_rel_subscriber_jobcat_by_mdn.php?mdn=$number_mdn0&jobcat_key=$jobcat_key&status=1";	
					echo $rel."<hr>";
					$rel = get_data($rel);
					
					if($rel["totaldata"] > 0)
					{
						$rel_id     = $rel['rel_id'];
						$date_expired = $rel['date_expired'];
						$date_expired = explode("-",$date_expired);
						$date_expired = $date_expired[2]."-".$date_expired[1]."-".$date_expired[0];
						
						// $json = CORE_URL."update_rel_subscriber_jobcat.php?rel_id=$rel_id&update_by=0&status=2&date_unsub=".urlencode($curr_date)."&date_update=".urlencode($dtime);	
						$json = CORE_URL."update_rel_subscriber.php?tx_id=$tx_id&key=rel_id&value=$rel_id&update_key=status|date_update|date_unsub|update_by|update_by_name|update_notes".
							"&update_value=2|".urlencode($dtime)."|".urlencode($curr_date)."|".$subscriber_id."|SUBS_".$subscriber_id."|".urlencode("MO:UNREG $sms_keyword[1]");
						echo $json."<hr>";
						$json = get_data($json);
						
						if ($shortcode == "818") $sender = "81820";
						else if ($shortcode == "8888") $sender = "8888";
						$msg = "Anda telah menghentikan langganan info lowongan pekerjaan $jobcat_key.Info lowongan tetap dikirim hingga periode brakhir pd tgl $date_expired.CS:02160908000";
						echo $a = send_sms($number_mdn, $msg, $tx_id, $sender);
					}
					else
					{					
						$msg_err = "Anda sedang tidak terdaftar pada langganan info lowongan pekerjaan $jobcat_key. Daftar:REG KERJA (pekerjaan) sms ke 818.CS: 02160908000";
						echo $a = send_sms($number_mdn, $msg_err, $tx_id);
					}
				}
				else
				{
					$msg_err = "Maaf keyword yg Anda masukkan salah.Ketik KERJA INFO sms ke 818 utk pilihan pekerjaan.CS: 02160908000";
					echo $a = send_sms($number_mdn, $msg_err, $tx_id);
				}
			}
		}
		else
		{
			$msg_err = "Maaf keyword yg Anda masukkan salah.Ketik KERJA INFO sms ke 818 utk pilihan pekerjaan.CS: 02160908000";
			echo $a = send_sms($number_mdn, $msg_err, $tx_id);
		}
	}
	else
	{
		$msg_err = "Maaf keyword yg Anda masukkan salah.Ketik KERJA INFO sms ke 818 utk pilihan pekerjaan.CS: 02160908000";
		echo $a = send_sms($number_mdn, $msg_err, $tx_id);
	}
?>