<?php
	$data = CORE_URL."add_logsmsg.php?tx_id=$tx_id&mdn=$number_mdn0&sms_type=".urlencode("MO:KERJA OK")."&message=".strtoupper(urlencode($message))."&dtime_add=".urlencode($dtime_micro);
	echo $data."<hr>";				
	$data = get_data($data);
	
	write_logfile($number_mdn, "SMSG:Check subscription REG-ed by user.");
	
	$data = CORE_URL."get_rel_subscriber.php?tx_id=$tx_id&key=subscriber_id|jobcat_id|status&value=$subscriber_id"."|".$jobcat_id."|6";	
	echo $data."<hr>";
	$data = get_data($data);
	if($data["totaldata"] > 0)	
	{
		$rel_id = $data["results"][0]["rel_id"];
		$date_add = $data["results"][0]["date_add"];
		$diff_currtime_dateadd = strtotime($dtime) - strtotime($date_add);
		echo $diff_currtime_dateadd."<hr>";

		write_logfile($number_mdn, "SMSG:Subscription REG-ed by user existed. Check active subscription.");
		$total_rel = 0;
		
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
		
		//JIKA MASIH TERDAFTAR DENGAN STATUS AUTO-RENEW OR UNREG BY ADMIN OR UNREG BY USER OR PERNAH REG
		if($total_rel == 0)				
		{
			// write_logfile($number_mdn, "SMSG:No active subscription founded. Check not-activated-yet subscription.");
			
			//$sent = CORE_URL."get_rel_subscriber_jobcat_by_mdn.php?tx_id=$tx_id&mdn=$number_mdn0&jobcat_key=".urlencode($jobcat_key)."&status=1";	
			// $data = CORE_URL."get_rel_subscriber.php?tx_id=$tx_id&key=subscriber_id|jobcat_id|status&value=$subscriber_id"."|".$jobcat_id."|6";	
			// echo $data."<hr>";
			// $data = get_data($data);
			
			// $rel_id = $data[results][0][rel_id];
			
			// echo "rel_id = $rel_id<hr>";
			// if ($data["totaldata"] > 0)
			// {
				write_logfile($number_mdn, "SMSG:Check not-activated-yet subscription validity");
				if ($diff_currtime_dateadd <= ($expiry_sms * 3600))
				{
					write_logfile($number_mdn, "SMSG:Not-activated-yet subscription valid. Deduct user balance");
					$status_pulsa1 = potong_pulsa($tx_id, $number_mdn, $jobcat_id, $jobcat_key);
					//$status_pulsa1 = "1";
					if($status_pulsa1 != "1")
					{
						$msg = str_replace("%1%", $jobcat_key, $msg_notif[9]);
						echo $a = send_sms($number_mdn, $msg, $tx_id);
						write_logfile($number_mdn, "SMSG:$sms_keyword[1] OK request rejected. Reason: Charging failed.");
						write_logfile($number_mdn, "SMSG:MT: $msg");
					}
					else
					{ 
						write_logfile($number_mdn, "SMSG:$sms_keyword[1] OK request accepted. User balance deducted.");
						$date_expired = date("d-m-Y", (strtotime($dtime) + (7 * 86400)));
						if (DOUBLECONF == "1")
						{
							$msg = $msg_notif[3];
							$msg = str_replace("%1%", $jobcat_key, $msg);
							$msg = str_replace("%2%", $date_expired, $msg);
						}
						else
						{
							$msg = $msg_notif[5];
							$msg = str_replace("%1%", $jobcat_key, $msg);
							$msg = str_replace("%2%", $date_expired, $msg);
						}
						renew($tx_id, $rel_id, $dtime, $subscriber_id, $jobcat_id, $msg, $number_mdn); 
						write_logfile($number_mdn, "SMSG:MT: $msg");
					}
					
				}
				else
				{
					echo $a = send_sms($number_mdn, $msg_notif[1], $tx_id);
					//echo $a = send_sms($number_mdn, $message." EXPIRED");
					$data = CORE_URL."del_rel_subscriber_jobcat.php?tx_id=$tx_id&rel_id=".$rel_id;
					echo $data."<hr>";
					$data = get_data($data);
					write_logfile($number_mdn, "SMSG:$sms_keyword[1] OK request rejected. Reason: REG expired.");
					write_logfile($number_mdn, "SMSG:MT: $msg_notif[1]");
				}
			// }
			// else 
			// { 
				// echo $a = send_sms($number_mdn, $msg_notif[1], $tx_id); 
				// write_logfile($number_mdn, "SMSG:$sms_keyword[1] OK request rejected. Reason: Not-activated-yet subscription doesn't exist.");
				// write_logfile($number_mdn, "SMSG:MT: $msg_notif[1]");
			// }
		}
		else
		{
			$msg = str_replace("%1%", $jobcat_key, $msg_notif[7]);
			echo send_sms($number_mdn, $msg, $tx_id);
			//$msg = "Saat ini Anda masih terdaftar pada lowongan pekerjaan $jobcat_key.Utk info pekerjaan selengkapnya ketik KERJA INFO sms ke 818";
			write_logfile($number_mdn, "SMSG:$sms_keyword[1] request rejected. Reason: Active subscription existed.");
			write_logfile($number_mdn, "SMSG:MT: $msg");
		}
	}
	else 
	{ 
		echo $a = send_sms($number_mdn, $msg_notif[1], $tx_id); 
		write_logfile($number_mdn, "SMSG:$sms_keyword[1] request rejected. Reason: Subscription REG-ed by user doesn't exist.");
		write_logfile($number_mdn, "SMSG:MT: $msg_notif[1]");
	}
?>