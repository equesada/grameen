<?php
	$data = CORE_URL."add_logsmsg.php?tx_id=$tx_id&mdn=$number_mdn0".
		"&sms_type=".urlencode("MO:$sms_keyword[1] YA")."&message=".strtoupper(urlencode($message))."&dtime_add=".urlencode($dtime_micro);
	echo $data."<hr>";				
	$data = get_data($data);
	
	/*
	write_logfile($number_mdn, "SMSG:Check subscription UNREG-ed by CRON.");
	$data = CORE_URL."get_rel_subscriber.php?tx_id=$tx_id&key=subscriber_id|jobcat_id|status&value=$subscriber_id"."|".$jobcat_id."|5";	
	echo $data."<hr>";
	$data = get_data($data);
	
	if ($data[totaldata] > 0)
	{
		$rel_id = $data["results"][0]["rel_id"];
		write_logfile($number_mdn, "SMSG:Subscription UNREG-ed by CRON existed. Check subscription validity.");
		$date_expired = $data["results"][0]["date_expired"];
		$diff_currtime_dateexpired = strtotime($dtime) - strtotime($date_expired);
		echo $dtime." - ".$date_expired." = ".$diff_currtime_dateexpired."<hr>";
		if ($diff_currtime_dateexpired <= ($expiry_sms * 3600))
		{
			write_logfile($number_mdn, "SMSG:Deduct user balance");
			$status_pulsa1 = "1";
			if($status_pulsa1 != "1")
			{
				$msg = str_replace("%1%", $jobcat_key, $msg_notif[9]);
				echo $a = send_sms($number_mdn, $msg, $tx_id);
					
				$data = CORE_URL."add_subscription_smsg.php?tx_id=$tx_id&rel_subscription_id=$rel_id&sms_type=".urlencode("MO:KERJA YA")."&dtime_add=".urlencode($dtime_micro);
				echo $data."<hr>";
				$data = get_data($data);
				
				write_logfile($number_mdn, "SMSG:$sms_keyword[1] YA request rejected. Reason: Charging failed.");
				write_logfile($number_mdn, "SMSG:MT: $msg");
			}
			else
			{ 
				$date_expired = date("Y-m-d", (strtotime($dtime) + (8 * 86400)));
				$next_date = date("Y-m-d", (strtotime($dtime) + 86400));
				
				$msg = $msg_notif[4];
				$msg = str_replace("%1%", $jobcat_key, $msg);
				echo $a = send_sms($number_mdn, $msg, $tx_id);
				
				if (DOUBLECONF == "1") $status = "2";
				else $status = "1";
				
				$data = CORE_URL."add_subscription.php?tx_id=$tx_id&status=$status&subscriber_id=$subscriber_id&jobcat_id=$jobcat_id&jobcat_key=$jobcat_key".
					"&n_jobreceived=0&date_add=".urlencode($dtime)."&date_active=$next_date&dtime_active=$next_date&date_expired=$date_expired".
					"&update_by_name=SUBS_".$subscriber_id."&update_notes=".urlencode("MT:$sms_keyword[1] YA");
				echo $data."<hr>";				
				$data = get_data($data);
				print_r($data); echo "<hr>";
				$rel_id = $data[rel_id];
					
				
				$data = CORE_URL."add_subscription_smsg.php?tx_id=$tx_id&rel_subscription_id=$rel_id&sms_type=".urlencode("MO:KERJA YA")."&dtime_add=".urlencode($dtime_micro);
				echo $data."<hr>";				
				$data = get_data($data);
				
				write_logfile($number_mdn, "SMSG:$sms_keyword[1] YA request accepted. User balance deducted.");
				write_logfile($number_mdn, "SMSG:MT: $msg");
			}
		}
		else
		{
			echo $a = send_sms($number_mdn, $msg_notif[1], $tx_id);
			//echo $a = send_sms($number_mdn, $message." EXPIRED", $tx_id);
			write_logfile($number_mdn, "SMSG:$sms_keyword[1] YA request rejected. Reason: UNREG by CRON expired.");
			write_logfile($number_mdn, "SMSG:MT: $msg_notif[1]");
		}
	}
	else 
	*/
	if (DOUBLECONF == "1")
	{
		write_logfile($number_mdn, "SMSG:Double Confirmation enabled. Check subscription REG-ed by user.");
		$total_rel = 0;
/*		
		$data = CORE_URL."get_rel_subscriber.php?tx_id=$tx_id&key=subscriber_id|jobcat_id|status&value=$subscriber_id"."|".$jobcat_id."|2";	
		echo $data."<hr>";
		$data = get_data($data);
		$total_rel = $data["totaldata"];	
		echo $total_rel."<hr>";
		
		
		$data = CORE_URL."get_rel_subscriber.php?tx_id=$tx_id&key=subscriber_id|jobcat_id|status&value=$subscriber_id"."|".$jobcat_id."|6";	
		echo $data."<hr>";
		$data = get_data($data);
		$total_rel += $data["totaldata"];	
		echo $total_rel."<hr>";
*/

		$data = CORE_URL."get_logsmsg.php?tx_id=$tx_id&order=log_id&ascdesc=desc&key=mdn|message&value=$number_mdn0|".strtoupper(urlencode("reg $sms_keyword[1] $jobcat_key"));
		echo $data."<hr>";
		$data = get_data($data);
		$total_rel = $data["totaldata"];
		
		if($total_rel > 0)				
		{
			write_logfile($number_mdn, "SMSG:Subscription REG-ed by user existed. Check subscription OK-ed by user.");
			$data = CORE_URL."get_logsmsg.php?tx_id=$tx_id&order=log_id&ascdesc=desc&key=mdn|message&value=$number_mdn0|".strtoupper(urlencode("$sms_keyword[1] $jobcat_key ok"));
			echo $data."<hr>";
			$data = get_data($data);
			
			if ($data["totaldata"] > 0)
			{
				write_logfile($number_mdn, "SMSG:Subscription OK-ed by user existed. Check subscription OK-ed validity.");
				$dtime_ok = $data["results"][0]["dtime_add"];

				$data = CORE_URL."get_rel_subscriber.php?tx_id=$tx_id&key=subscriber_id|jobcat_id|status&value=$subscriber_id"."|".$jobcat_id."|2";	
				echo $data."<hr>";
				$data = get_data($data);
				$total_rel = $data["totaldata"];	
				echo $total_rel."<hr>";
				if($total_rel > 0)				
					$rel_id = $data["results"][0]["rel_id"]; 
				else
					$rel_id = ""; 
				

				// $dtime_add = $data["results"][0]["dtime_add"];
				// $diff_currtime_dateadd = strtotime($dtime) - strtotime(substr($dtime_add,0,strpos($dtime_add,".")));
				// echo $dtime." - ".$dtime_add." = ".$diff_currtime_dateadd."<hr>";
				// echo strtotime($dtime)." - ".strtotime(substr($dtime_add,0,strpos($dtime_add,".")))." = ".$diff_currtime_dateadd."<hr>";
				
				$diff_currtime_dateadd = strtotime($dtime) - strtotime(substr($dtime_ok,0,strpos($dtime_ok,".")));
				
				echo $dtime." - ".$dtime_ok." = ".$diff_currtime_dateadd."<hr>";
				echo strtotime($dtime)." - ".strtotime(substr($dtime_ok,0,strpos($dtime_ok,".")))." = ".$diff_currtime_dateadd."<hr>";
				if (($diff_currtime_dateadd >= 0) && ($diff_currtime_dateadd <= ($expiry_sms * 3600)))
				{
					$data = CORE_URL."update_rel_subscriber.php?tx_id=$tx_id&key=rel_id&value=$rel_id&update_key=status|date_update|update_by|update_by_name|update_notes".
						"&update_value=1|".urlencode($dtime)."|".$subscriber_id."|SUBS_".$subscriber_id."|".urlencode("MO:$sms_keyword[1] YA");
					echo $data."<hr>";
					$data = get_data($data);
					write_logfile($number_mdn, "SMSG:Subscription OK valid. Update subscription status to auto-renew.");

					$msg = str_replace("%1%", $jobcat_key, $msg_notif[4]);
					echo $a = send_sms($number_mdn, $msg, $tx_id);

					$data = CORE_URL."add_subscription_smsg.php?tx_id=$tx_id&rel_subscription_id=$rel_id&sms_type=".urlencode("MO:$sms_keyword[1] YA")."&dtime_add=".urlencode($dtime_micro);
					echo $data."<hr>";
					$data = get_data($data);
					
					write_logfile($number_mdn, "SMSG:$sms_keyword[1] YA request accepted.");
					write_logfile($number_mdn, "SMSG:MT: $msg");
				}
				else
				{
					echo $a = send_sms($number_mdn, $msg_notif[1], $tx_id);
					//echo $a = send_sms($number_mdn, $message." EXPIRED", $tx_id);
					
					// $data = CORE_URL."del_rel_subscriber_jobcat.php?tx_id=$tx_id&rel_id=".$rel_id;
					// echo $data."<hr>";
					// $data = get_data($data);
					
					$data = CORE_URL."add_subscription_smsg.php?tx_id=$tx_id&rel_subscription_id=$rel_id&sms_type=".urlencode("MO:$sms_keyword[1] YA")."&dtime_add=".urlencode($dtime_micro);
					echo $data."<hr>";
					$data = get_data($data);
					
					write_logfile($number_mdn, "SMSG:$sms_keyword[1] YA request rejected. Reason: subscription OK-ed by user expired.");
					write_logfile($number_mdn, "SMSG:MT: $msg_notif[1]");
				}
			}
			else 
			{ 
				echo $a = send_sms($number_mdn, $msg_notif[1], $tx_id); 
				write_logfile($number_mdn, "SMSG:$sms_keyword[1] YA request rejected. Reason: subscription OK-ed by user doesn't exist.");
				write_logfile($number_mdn, "SMSG:MT: $msg_notif[1]");
			}
		}
		else 
		{ 
			echo $a = send_sms($number_mdn, $msg_notif[1], $tx_id); 
			write_logfile($number_mdn, "SMSG:$sms_keyword[1] YA request rejected. Reason: subscription REG-ed by user doesn't exist.");
			write_logfile($number_mdn, "SMSG:MT: $msg_notif[1]");
		}
	}
	else 
	{ 
		echo $a = send_sms($number_mdn, $msg_notif[1], $tx_id); 
		write_logfile($number_mdn, "SMSG:$sms_keyword[1] YA request rejected. Reason: Double Confirmation disabled.");
		write_logfile($number_mdn, "SMSG:MT: $msg_notif[1]");
	}
?>