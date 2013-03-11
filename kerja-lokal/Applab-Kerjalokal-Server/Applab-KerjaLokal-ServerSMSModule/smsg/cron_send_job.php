<?php
require_once("conf.php");
require_once("func.php");

$tx_id = get_uuid("sms");
$a = "";
//$id = isSet($_GET["id"]) ? $_GET["id"] : 0;
$dtime = isSet($_GET["dtime"]) ? ($_GET["dtime"]." ".date("H:i:s")) : date("Y-m-d H:i:s", time() + ($id * 86400));
$curr_date = date("Y-m-d", strtotime($dtime));
$date_expired = date("Y-m-d", (strtotime($dtime) + (7 * 86400)));
$next_date = date("Y-m-d", (strtotime($dtime) + 86400));
$next_date_expired = date("Y-m-d", (strtotime($next_date) + (7 * 86400)));

echo $dtime."<br>";
echo $curr_date."<br>";
echo $next_date."<br>";
echo $date_expired."<hr>";

//$date = "1998-08-14";
$min_date_expired = strtotime ( '-1 day' , strtotime ( $date_expired ) ) ;
$min_date_expired = date ( 'Y-m-j' , $min_date_expired );
echo $min_date_expired."<hr>";

$sms_status = "1";

$json = CORE_URL."get_jobsends_by_date.php?tx_id=$tx_id&date_send=$curr_date";
echo $json."<hr>";
echo str_replace("10.99.4.5", "110.138.141.255", $json)."<hr>";
$json = get_data($json);
echo "<pre>"; print_r($json); echo "</pre>";

foreach ($json["results"] as $job)
{
	if ($job["status"] == "1")
	{
		$rel_id = $job["rel_id"];
		$job_id = $job["job_id"];
		$jobcat_id = $job["jobcat_id"];
		$jobsend_id = $job["jobsend_id"];
		$n_send = $job["n_send"] == "0" ? "1" : $job["n_send"];
		$subscriber_id = $job["subscriber_id"];
		$mdn = $job["mdn"];
		if ($job_id != "1")
		{
			if ($job["sms"] != "")
			{
				$msg = $job["sms"];
			}
			else
			{
				$msg = $job["comp_name"];
				$msg .= ($job["title"] != "") ? ",".$job["title"] : "";
				$msg .= ($job["description"] != "") ? ",".$job["description"] : "";
				$msg .= ($job["comp_cp"] != "") ? ", hub.".$job["comp_cp"] : "";
				$msg .= ($job["comp_tel"] != "") ? " ".$job["comp_tel"] : "";
			}
		}
		else
		{	$msg = $job["sms"]; }
		
		
		
		

		$a = "http://10.99.4.5:8085/sendsms.php?msisdn=62".substr($mdn,1)."&message=".urlencode($msg)."&msgid=".urlencode(str_replace(" ","_",$msg))."&shortcode=81820&appsid=GRAMEEN";
		echo $a."<hr>";
		$a = file_get_contents($a);
			//echo str_replace("10.99.4.5", "110.138.141.255", $a)."<hr>";
			
		echo $a."<hr>";

	
		//$writelog = CORE_URL."add_logsms.php?tx_id=".urlencode($tx_id)."&job_id=$job_id&jobsend_id=$jobsend_id&rel_id=$rel_id&subscriber_id=$subscriber_id&title=sms&msg=".urlencode($msg)."&mdn=$mdn&status=1&date_send=".urlencode($dtime);	
		$writelog = CORE_URL."add_logsms.php?tx_id=".urlencode($tx_id)."&job_id=$job_id&jobcat_id=$jobcat_id&rel_id=$rel_id&subscriber_id=$subscriber_id&jobsend_id=$jobsend_id&title=sms&msg=".urlencode($msg)."&mdn=$mdn&status=1&date_send=".urlencode($dtime);	
		echo $writelog."<Br>";
		$writelog = get_data($writelog);
		echo "<pre>"; print_r($writelog); echo "</pre>";
	
		$is_mentor = CORE_URL."check_mdn.php?tx_id=".urlencode($tx_id)."&mdn=$mdn&type=mentor";
		$is_mentor = get_data($is_mentor);
		if ($is_mentor["status"] == "1")
		{
			//if ($job["sms"] != "")
			//	$msg = $job["sms"];
			//else
			
			if ($job_id != "1")
			{
				$msg = $curr_date;
				$msg .= ($job["title"] != "") ? ", ".$job["title"] : "";
				$msg .= ($job["comp_name"] != "") ? ", ".$job["comp_name"] : "";
				$msg .= ($job["salary"] != "") ? ",".$job["salary"] : "";
				$msg .= ($job["description"] != "") ? ",".$job["description"] : "";
				$msg .= ($job["comp_cp"] != "") ? ", hub.".$job["comp_cp"] : "";
				$msg .= ($job["comp_tel"] != "") ? " ".$job["comp_tel"] : "";
				$title = $curr_date;
				$title .= ($job["title"] != "") ? ", ".$job["title"] : "";
				$title .= ($job["salary"] != "") ? ", ".$job["salary"] : "";
			}
			else
			{	
				$msg = $job["sms"]; 
				$title = $curr_date.", Belum tersedia";
			}
			
			//$writelog = CORE_URL."add_logsms.php?tx_id=".urlencode($tx_id)."&job_id=$job_id&jobsend_id=$jobsend_id&rel_id=$rel_id&subscriber_id=$subscriber_id&title=".urlencode($title)."&msg=".urlencode($msg)."&mdn=$mdn&status=2&date_send=".urlencode($dtime);
			$writelog = CORE_URL."add_logsms.php?tx_id=".urlencode($tx_id)."&job_id=$job_id&jobcat_id=$jobcat_id&rel_id=$rel_id&subscriber_id=$subscriber_id&jobsend_id=$jobsend_id&title=".urlencode($title)."&msg=".urlencode($msg)."&mdn=$mdn&status=2&date_send=".urlencode($dtime);
			echo $writelog."<hr>";
			$writelog = get_data($writelog);
			echo "<pre>"; print_r($writelog); echo "</pre>";
		}
	
		if ($sms_status == "1")
		{
			$json2 = CORE_URL."update_jobsend.php?tx_id=".urlencode($tx_id)."&jobsend_id=$jobsend_id&status=2&date_update=".urlencode($dtime);
			echo $json2."<hr>";
			$json2 = get_data($json2);
			echo "<pre>"; print_r($json2); echo "</pre>";
			$n_send++;
			$json2 = CORE_URL."update_jobpost.php?tx_id=".urlencode($tx_id)."&job_id=$job_id&n_send=".$n_send."&date_update=".urlencode($dtime);
			echo $json2."<hr>";
			$json2 = get_data($json2);
			echo "<pre>"; print_r($json2); echo "</pre>";
		}
		else
		{
			$json2 = CORE_URL."update_jobsend.php?tx_id=".urlencode($tx_id)."&jobsend_id=$jobsend_id&status=1&date_update=".urlencode($dtime)."&date_send=".date("Y-m-d", (time() + 7*86400));
			echo $json2."<hr>";
			$json2 = get_data($json2);
			echo "<pre>"; print_r($json2); echo "</pre>";
		}


		$rel = CORE_URL."get_rel_subscriber_jobcat_by_rel_id.php?tx_id=".urlencode($tx_id)."&id=$rel_id";
		echo $rel."<br>";
		$rel = get_data($rel);
		$jobcat_key = $rel["jobcat_key"];
		
		$mdn1 = $rel["mdn"];
		$mdn = substr($mdn1,1);
		$num_msisdn = '62'.$mdn;
		
		$id_rel = $rel["rel_id"];
		
		echo $jobcat_key."<hr>";
		echo "<pre>"; print_r($rel); echo "</pre>";
		echo "Rel expired : ".$rel["date_expired"]."<br>";
		//die();
		if ($curr_date < $rel["date_expired"])
		{
			//if ($rel["status"] == 1)
			/*
			$sql = "SELECT * FROM jobs_send WHERE date_send = '$curr_date' AND rel_id='$rel_id'";
			$sql = str_replace("\r\n","",$sql);
			$sql = urlencode($sql);
			$sql = CORE_URL."sql.php?sql=".$sql;
			*/
			$sql = CORE_URL."get_jobs_send.php?tx_id=$tx_id&key=date_send|rel_id&value=$curr_date|$rel_id";
			$sql = get_data($sql);
			if ($sql["nrows"] != "0")
			{
				//$json2  = CORE_URL."add_jobsend.php?tx_id=".urlencode($tx_id)."&rel_id=".$rel_id."&date_add=".urlencode($date_add)."&date_send=".urlencode($date_send)."&subscriber_id=$jobseeker_subscriber_id";
				$addjob = CORE_URL."add_jobsend.php?tx_id=".urlencode($tx_id)."&rel_id=$rel_id&date_add=".urlencode($dtime)."&jobcat_id=".$rel["jobcat_id"]."&date_send=".urlencode($next_date)."&subscriber_id=$subscriber_id";
				//$addjob = CORE_URL."add_jobsend.php?tx_id=".urlencode($tx_id)."&subscriber_id=$subscriber_id&rel_id=$rel_id&jobcat_id=".$rel["jobcat_id"]."&date_add=".urlencode($dtime)."&date_send=".urlencode($next_date);
				echo $addjob."<hr>";
				$addjob = get_data($addjob);
				echo "<pre>"; print_r($addjob); echo "</pre>";
				//echo "Next job : ".$addjob["job_id"]." - ".$addjob["n_job"]." - ".$addjob["n_qualified_job"]." - <br>";
			}
			//else echo "Subscription has been de-activated";
			
			//} else if {
			
		}
		else
		{
			if ($rel["status"] == 1)
			{
				//$check_mdn = check_mdn($mdn, $tx_id);
				//$status_pulsa = potong_pulsa($mdn, 10, $check_mdn, $tx_id, $jobcat_ID, $jobcat_key, $cid);
				//$status_pulsa = "1";
				
				
				$check_mdn = curl_init();
				curl_setopt($check_mdn, CURLOPT_URL,            "http://10.99.4.1/grameen/infokerja/check_mdn.php?mdn=".$mdn."&tx_id=$tx_id" );
				curl_setopt($check_mdn, CURLOPT_CONNECTTIMEOUT, 10);
				curl_setopt($check_mdn, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($check_mdn, CURLOPT_HEADER, 		0);
				curl_setopt($check_mdn, CURLOPT_TIMEOUT,        10);
				$check_mdn = curl_exec($check_mdn);
				//echo $check_mdn."<hr>";
				
				//$tarif = ($check_mdn == "pre") ? "1100000000" : "1000000000";
				$tarif = ($check_mdn == "pre") ? "11" : "10";

				//$status_pulsa = file_get_contents("http://10.99.4.1/grameen/infokerja/potong_pulsa.php?mdn=$number_mdn&tarif=2&pospre=$check_mdn&tx_id=$tx_id");
				$status_pulsa = curl_init();
				curl_setopt($status_pulsa, CURLOPT_URL,            "http://10.99.4.1/grameen/infokerja/potong_pulsa2.php?mdn=".$mdn."&tarif=$tarif&pospre=$check_mdn&tx_id=$tx_id&jobcat_ID=$rel[jobcat_id]&jobcat_key=$jobcat_key&app_info=RBR_INFOKERJA_112_GRM" );
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
				curl_setopt($write_cdr, CURLOPT_URL,            "http://10.99.4.1/grameen/infokerja/write_cdr.php?log_id=$log_id&mdn=".$mdn."&pospre=$pospre&tarif=$tarif&billing_status=$billing_status&status_code=$status_code&service_name=RENEWAL&status_code_desc=".urlencode($status_code_desc)."&jobcat_ID=$rel[jobcat_id]&jobcat_key=".urlencode($jobcat_key));
				curl_setopt($write_cdr, CURLOPT_CONNECTTIMEOUT, 10);
				curl_setopt($write_cdr, CURLOPT_TIMEOUT,        10);
				$write_cdr = curl_exec($write_cdr);
				
				$status_pulsa = $status_pulsa1;

				
				if($status_pulsa != "1")
				{
					//echo $mdn." pulsa tidak cukup<br>";
					/*
					// GAGAL RENEW
					update_subscriber_jobcat.php?status=5&date_update=now
					send_sms.php					
					*/
					
					$json2 = CORE_URL."update_rel_subscriber_jobcat.php?rel_id=".urlencode($id_rel)."&status=5&date_update=".urlencode($dtime);
					echo $json2."<hr>";
					$json2 = get_data($json2);
					echo "<pre>"; print_r($json2); echo "</pre>";					
					$msg = "Langganan info lowongan pekerjaan $jobcat_key tidak berhasil diperpanjang karena pulsa tidak cukup.Utk info pekerjaan yg lain ketik KERJA INFO sms ke 818";
					$a = file_get_contents("http://10.99.4.5:8085/sendsms.php?msisdn=$num_msisdn&message=".urlencode($msg)."&shortcode=81820&msgid=".urlencode(str_replace(" ","_",$msg))."&appsid=GRAMEEN");
				
					echo $msg;
					
				}
				else if($billing_status == "0000")
				{
				
					//echo $mdn." pulsa cukup<br>";
				
					/*
					// BERHASIL RENEW
					update_subscriber_jobcat.php?status=4&date_update=now
					send_sms.php					
					*/
					
					$json2 = CORE_URL."update_rel_subscriber_jobcat.php?rel_id=".urlencode($id_rel)."&status=4&date_update=".urlencode($dtime);
					echo $json2."<hr>";
					$json2 = get_data($json2);
					echo "<pre>"; print_r($json2); echo "</pre>";
					
					$msg = "Selamat, langganan info lowongan pekerjaan $jobcat_key sudah diperpanjang otomatis.Utk info kerja pekerjaan yg lain ketik KERJA INFO sms ke 818";
					$a = file_get_contents("http://10.99.4.5:8085/sendsms.php?msisdn=$num_msisdn&message=".urlencode($msg)."&shortcode=81820&msgid=".urlencode(str_replace(" ","_",$msg))."&appsid=GRAMEEN");
				
					echo $msg;
					
					
					
					$json = CORE_URL."add_subscriber_jobcat_key.php?tx_id=".urlencode($tx_id)."&status=1&subscriber_id=".$rel["subscriber_id"]."&jobcat_key=".$rel["jobcat_key"]."&date_add=".urlencode($dtime)."&date_active=".urlencode($next_date)."&date_expired=".urlencode($date_expired);
					echo $json."<hr>";
					$json = get_data($json);
					echo "<pre>"; print_r($json); echo "</pre>";
					if ($json["status"] == "0")
						echo "Job subscription has already been extended.<br>";
					else
					{
						$addjob = CORE_URL."add_jobsend.php?tx_id=".urlencode($tx_id)."&jobcat_id=".$rel["jobcat_id"]."&subscriber_id=".$rel["subscriber_id"]."&rel_id=".$json["rel_id"]."&date_add=".urlencode($dtime)."&date_send=".urlencode($next_date);
						echo $addjob."<hr>";
						$addjob = get_data($addjob);
						echo "<pre>"; print_r($addjob); echo "</pre>";
						//echo "Next job : ".$addjob["job_id"]." - ".$addjob["n_job"]." - ".$addjob["n_qualified_job"]." - <br>";
					}
				}
				else 
				{
					//echo $mdn." gagal charging<br>";
					/*
					// GAGAL RENEW
					update_subscriber_jobcat.php?status=5&date_update=now
					send_sms.php					
					*/
					
					$json2 = CORE_URL."update_rel_subscriber_jobcat.php?rel_id=".urlencode($id_rel)."&status=5&date_update=$curr_date";
					echo $json2."<hr>";
					$json2 = get_data($json2);
					echo "<pre>"; print_r($json2); echo "</pre>";					
					$msg = "Permintaan Anda saat ini belum dapat diproses. Utk info pekerjaan selngkapnya ketik KERJA INFO sms ke 818.CS: 02160908000";
					$a = file_get_contents("http://10.99.4.5:8085/sendsms.php?msisdn=$num_msisdn&message=".urlencode($msg)."&shortcode=81820&msgid=".urlencode(str_replace(" ","_",$msg))."&appsid=GRAMEEN");
				
					echo $msg;
					
				}

			}
			else if ($rel["status"] == 2)
			{ echo "Subscriber has stop the subscription"; }
			else echo "Under subscription";
			
		}
	}
}
date_default_timezone_set("Asia/Jakarta");
$next_date = date("Y-m-d H:i:s", (strtotime($dtime) + 86400*2));
$query  = "DELETE FROM rel_subscriber_cat WHERE DATE_ADD < '$next_date' AND STATUS=2";
$result = mysql_query($query) or die(mysql_error());

?>