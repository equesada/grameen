<?php
require_once("conf.php");
require_once("func.php");

$tx_id = get_uuid("cron");
$a = "";

$id = isSet($_GET["id"]) ? $_GET["id"] : 0;
$dtime = isSet($_GET["dtime"]) ? ($_GET["dtime"]." ".date("H:i:s")) : date("Y-m-d H:i:s", time() + ($id * 86400));
$curr_date = date("Y-m-d", strtotime($dtime));
$date_expired = date("Y-m-d", (strtotime($dtime) + (8 * 86400)));
$prev_date = date("Y-m-d", (strtotime($dtime) - 86400));
$next_date = date("Y-m-d", (strtotime($dtime) + 86400));
$next_date_expired = date("Y-m-d", (strtotime($next_date) + (8 * 86400)));

echo "<pre>";

write_logfile(0, "CRON:Job renewal start. Get active subscriber list");
$data = CORE_URL."get_rel_subscriber.php?tx_id=".urlencode($tx_id)."&key=n_jobreceived|status&value=8|1";
echo $data."<hr>";
$data = get_data($data);

if ($data[totaldata] > 0)
	foreach ($data["results"] as $rel)
	{
		$jobcat_key = $rel["jobcat_key"];

		$mdn1 = $rel["mdn"];
		$mdn = substr($mdn1,1);
		$num_msisdn = '62'.$mdn;

		$rel_id = $rel["rel_id"];

		echo $jobcat_key."<hr>";
		print_r($rel); echo "<br>";
		//echo "Rel expired : ".$rel["date_expired"]."<br>";
		//echo "n_jobreceived : ".$rel["n_jobreceived"]."<hr>";
		
		//if ($rel["status"] == 1)
		{
			write_logfile(0, "CRON:Renew subscription. Deduct user balance");

			//$check_mdn = check_mdn($mdn, $tx_id);
			//$status_pulsa = potong_pulsa($mdn, 10, $check_mdn, $tx_id, $jobcat_ID, $jobcat_key, $cid);
			//$status_pulsa = "1";
			
			$tarif = CORE_URL."get_tarif.php?tx_id=".urlencode($tx_id)."&key=id&value=1";
			echo $tarif."<hr>";
			$tarif = get_data($tarif);
			$tarif = $tarif["results"][0]["tarif"];

			$check_mdn = curl_init();
			curl_setopt($check_mdn, CURLOPT_URL,            BR14_URL."check_mdn.php?mdn=".$mdn."&tx_id=$tx_id" );
			curl_setopt($check_mdn, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($check_mdn, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($check_mdn, CURLOPT_HEADER, 		0);
			curl_setopt($check_mdn, CURLOPT_TIMEOUT,        10);
			$check_mdn = curl_exec($check_mdn);
			//echo $check_mdn."<hr>";
			
			//$tarif = ($check_mdn == "pre") ? "1100000000" : "1000000000";
			$tarif = ($check_mdn == "pre") ? ($tarif * 1.1) : ($tarif * 1);

			$status_pulsa = curl_init();
			curl_setopt($status_pulsa, CURLOPT_URL,            BR14_URL."potong_pulsa2.php?mdn=".$mdn."&tarif=$tarif&pospre=$check_mdn&tx_id=$tx_id&jobcat_ID=$rel[jobcat_id]&jobcat_key=$jobcat_key&app_info=RBR_INFOKERJA_112_GRM" );
			curl_setopt($status_pulsa, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($status_pulsa, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($status_pulsa, CURLOPT_HEADER, 		0);
			curl_setopt($status_pulsa, CURLOPT_TIMEOUT,        10);
			$status_pulsa = curl_exec($status_pulsa);
			//echo $status_pulsa."<hr>";
			//$status_pulsa = potong_pulsa(substr($number_mdn,1), 2, $check_mdn, $tx_id);
			//$status_pulsa = potong_pulsa(substr($number_mdn,1), 10, $check_mdn, $tx_id, $jobcat_ID, $jobcat_key, $cid);		
			
			$cdr = explode("|",$status_pulsa);
			$status_pulsa1 = $cdr[0] == "1" ? "1" : $status_pulsa;
			//echo $status_pulsa."<hr>";
			$log_id = $cdr[1];
			//$mdn  = substr($mdn,1); 
			$pospre = ($cdr[3] == 'pos') ? '1':'2';
			//$tarif = ($cdr[3] == 'pos') ? '10':'11';
			$billing_status = $cdr[5];
			$status_code = $cdr[6];
			$status_code_desc = $cdr[7];
			//$jobcat_ID = $cdr[8];
			//$jobcat_key = $cdr[9];
			
			
			//$check_mdn = file_get_contents("http://10.99.4.1/grameen/infokerja/write_cdr.php?mdn=$number_mdn&tx_id=$tx_id");
			$write_cdr = curl_init();
			curl_setopt($write_cdr, CURLOPT_URL,            BR14_URL."write_cdr.php?log_id=$log_id&mdn=".$mdn."&pospre=$pospre&tarif=$tarif&billing_status=$billing_status&status_code=$status_code&service_name=RENEWAL&status_code_desc=".urlencode($status_code_desc)."&jobcat_ID=$rel[jobcat_id]&jobcat_key=".urlencode($jobcat_key));
			curl_setopt($write_cdr, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($write_cdr, CURLOPT_TIMEOUT,        10);
			$write_cdr = curl_exec($write_cdr);
			
			$status_pulsa = $status_pulsa1;

			$status_pulsa = "1";
			$billing_status = "0000";
			if($status_pulsa != "1")
			{
				//echo $mdn." pulsa tidak cukup<br>";
				/*
				// GAGAL RENEW
				update_subscriber_jobcat.php?status=5&date_update=now
				send_sms.php					
				*/
				
				// $json2 = CORE_URL."update_rel_subscriber_jobcat.php?rel_id=".urlencode($id_rel)."&status=5&date_update=".urlencode($dtime);
				// echo $json2."<hr>";
				// $json2 = get_data($json2);
				
				switch ($billing_status)
				{
					case "1111":$update_notes = "BTEL:1111|INVALID USERNAME/PASSWORD"; $update_notes_sms = "system error(1111)"; $break;
					case "0201":$update_notes = "BTEL:0201|SUBSCRIBER NOT FOUND"; $update_notes_sms = "system error(0201)"; break;
					case "0207":$update_notes = "BTEL:0207|NOT ENOUGH BALANCE"; $update_notes_sms = "pulsa tidak cukup"; break;
					case "0208":$update_notes = "BTEL:0208|DN IN EXPIRED STATE"; $update_notes_sms = "nomor sudah expired"; break;
					case "0216":$update_notes = "BTEL:0216|DN IN IDLE STATE"; $update_notes_sms = "nomor tidak dipakai"; break;
					case "3000":$update_notes = "BTEL:3000|FAILED RESPONSE FROM OCS"; $update_notes_sms = "system error(3000)"; break;
				}
				$data = CORE_URL."update_rel_subscriber.php?tx_id=$tx_id&key=rel_id&value=$rel_id".
					"&update_key=status|date_unsub|date_update|update_by_name|update_notes".
					"&update_value=5|".urlencode($dtime)."|".urlencode($dtime)."|SYS_00|BTEL:$update_notes";
				echo $data."<hr>";
				$data = get_data($data);
				print_r($json2); echo "<hr>";	
				
				//$msg = "Langganan info lowongan pekerjaan $jobcat_key tidak berhasil diperpanjang karena pulsa tidak cukup.Utk info pekerjaan yg lain ketik KERJA INFO sms ke 818";
				//$a = file_get_contents(SMS_URL."msisdn=$num_msisdn&message=".urlencode($msg)."&shortcode=81820&msgid=".urlencode(str_replace(" ","_",$msg))."&appsid=GRAMEEN");
				//echo $msg;
				
				$msg = str_replace("%1%", $jobcat_key, $msg_notif[10]);
				$msg = str_replace("%2%", $update_notes_sms, $msg);
				echo send_sms($number_mdn, $msg, $tx_id);
				
				write_logfile($number_mdn, "CRON:FAIL Renew subscription. Reason: $update_notes_sms");
				write_logfile($number_mdn, "CRON:MT: $msg");
			}
			else if($billing_status == "0000")
			{
				//echo $mdn." pulsa cukup<br>";
			
				/*
				// BERHASIL RENEW
				update_subscriber_jobcat.php?status=4&date_update=now
				send_sms.php					
				*/
				
				// $json2 = CORE_URL."update_rel_subscriber_jobcat.php?rel_id=".urlencode($id_rel)."&status=4&date_update=".urlencode($dtime);
				// echo $json2."<hr>";
				// $json2 = get_data($json2);
				
				// $msg = "Selamat, langganan info lowongan pekerjaan $jobcat_key sudah diperpanjang otomatis.Utk info kerja pekerjaan yg lain ketik KERJA INFO sms ke 818";
				// $a = file_get_contents(SMS_URL."msisdn=$num_msisdn&message=".urlencode($msg)."&shortcode=81820&msgid=".urlencode(str_replace(" ","_",$msg))."&appsid=GRAMEEN");
				// echo $msg;
				
				$msg = str_replace("%1%", $jobcat_key, $msg_notif[11]);
				echo send_sms($number_mdn, $msg, $tx_id);
				
				//$json = CORE_URL."add_subscription.php?tx_id=$tx_id&status=1&subscriber_id=".$rel["subscriber_id"]."&jobcat_key=".$rel["jobcat_key"]."&date_add=".urlencode($dtime)."&date_active=".urlencode($next_date)."&date_expired=".urlencode($date_expired);
				$data = CORE_URL."add_subscription.php?tx_id=$tx_id&status=1&subscriber_id=".$rel["subscriber_id"]."&jobcat_id=".$rel["jobcat_id"]."&jobcat_key=".$rel["jobcat_key"].
					"&n_jobreceived=0&date_add=".urlencode($dtime)."&date_active=$next_date&dtime_active=$next_date&date_expired=$date_expired".
					"&update_by_name=SYS_00&update_notes=".urlencode("CRON:Success renewal. prev_rel_id=$rel_id.");
				echo $data."<hr>";
				$data = get_data($data);
				print_r($data); echo "<hr>";
				$new_rel_id = $data[rel_id];
				
				$data = CORE_URL."update_rel_subscriber.php?tx_id=$tx_id&key=rel_id&value=$rel_id&update_key=status|date_update|update_by_name|update_notes".
					"&update_value=4|".urlencode($dtime)."|SYS_00|".urlencode("CRON:Success renewal. new_rel_id=$new_rel_id.");
				echo $data."<hr>";
				$data = get_data($data);
				print_r($data); echo "<hr>";					
				
				// if ($json["status"] == "0")
					// echo "Job subscription has already been extended.<br>";
				// else
				// {
					// $addjob = CORE_URL."add_jobsend.php?tx_id=".urlencode($tx_id)."&jobcat_id=".$rel["jobcat_id"]."&subscriber_id=".$rel["subscriber_id"]."&rel_id=".$json["rel_id"]."&date_add=".urlencode($dtime)."&date_send=".urlencode($next_date);
					// echo $addjob."<hr>";
					// $addjob = get_data($addjob);
					// echo "<pre>"; print_r($addjob); echo "</pre>";
				// }
				write_logfile(0, "CRON:SUCCESS Renew subscription. User balance deducted.");
				write_logfile($number_mdn, "CRON:MT: $msg");
			}
			/*
			else 
			{
				//echo $mdn." gagal charging<br>";
				
				// GAGAL RENEW
				// update_subscriber_jobcat.php?status=5&date_update=now
				// send_sms.php					
				
				
				$json2 = CORE_URL."update_rel_subscriber_jobcat.php?rel_id=".urlencode($id_rel)."&status=5&date_update=$curr_date";
				echo $json2."<hr>";
				$json2 = get_data($json2);
				echo "<pre>"; print_r($json2); echo "</pre>";					
				$msg = "Permintaan Anda saat ini belum dapat diproses. Utk info pekerjaan selngkapnya ketik KERJA INFO sms ke 818.CS: 02160908000";
				$a = file_get_contents(SMS_URL."msisdn=$num_msisdn&message=".urlencode($msg)."&shortcode=81820&msgid=".urlencode(str_replace(" ","_",$msg))."&appsid=GRAMEEN");
			
				echo $msg;
			}
			*/

		}
		else if (($rel["status"] == 2) || ($rel["status"] == 3) || ($rel["status"] == 5))
		{
			$data = CORE_URL."update_rel_subscriber.php?tx_id=$tx_id&key=rel_id&value=$rel_id&".
				"update_key=status|date_unsub|date_update|update_by_name|update_notes".
				"&update_value=5|".urlencode($dtime)."|".urlencode($dtime)."|SYS_00|SUBS:STATUS=$rel[status]";
			echo $data."<hr>";
			$data = get_data($data);
			print_r($data); echo "<hr>";
		}
		
		// else if ($rel["status"] == 2)
		// { echo "Subscriber has stop the subscription"; }
		// else echo "Under subscription";
	}
echo "</pre>";
	
write_logfile(0, "CRON:Job renewal end.");

?>