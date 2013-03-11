<?php
require_once("conf.php");
require_once("func.php");

$tx_id = get_uuid("cron");
$a = "";

$id = isSet($_GET["id"]) ? $_GET["id"] : 0;
$dtime = isSet($_GET["dtime"]) ? ($_GET["dtime"]." ".date("H:i:s")) : date("Y-m-d H:i:s", time() + ($id * 86400));
$curr_date = date("Y-m-d", strtotime($dtime));
$date_expired = date("Y-m-d", (strtotime($dtime) + (7 * 86400)));
$prev_date = date("Y-m-d", (strtotime($dtime) - 86400));
$next_date = date("Y-m-d", (strtotime($dtime) + 86400));
$next_date_expired = date("Y-m-d", (strtotime($next_date) + (7 * 86400)));

echo "<pre>";
echo "dtime = ".$dtime."<br>";
echo "curr_date = ".$curr_date."<br>";
echo "next_date = ".$next_date."<br>";
echo "date_expired = ".$date_expired."<hr>";

// $min_date_expired = strtotime ( '-1 day' , strtotime ( $date_expired ) ) ;
// $min_date_expired = date ( 'Y-m-j' , $min_date_expired );
// echo $min_date_expired."<hr>";

$sms_status = "1";

/*** SEND SMS REMINDER KERJA LOKASI 1 HARI SETELAH ACTIVE	***/
echo "REMINDER KERJA LOKASI<hr>";
/*
$sql = "SELECT 
			subscribers.mdn AS mdn, subscribers.mentor_id AS mentor_id, jobcat_key, rel_subscriber_cat.date_add AS DATE_ADD, 
			rel_subscriber_cat.date_expired AS date_expired, rel_subscriber_cat.subscriber_id AS subscriber_id	
		FROM rel_subscriber_cat 
		LEFT JOIN subscribers ON rel_subscriber_cat.subscriber_id = subscribers.subscriber_id 	
		WHERE 
			TIMESTAMPDIFF(DAY, \"$curr_date\", rel_subscriber_cat.date_expired) = 5 AND subscribers.mentor_id=2	
		GROUP BY subscribers.mdn ORDER BY subscribers.mdn, DATE_ADD"; 
		
$sql = str_replace("\r\n","",$sql);
$sql = urlencode($sql);
$sql = CORE_URL."sql.php?sql=".$sql;
//echo str_replace("10.99.4.5", "110.138.141.255", $sql)."<hr>";
$sql = get_data($sql);	

print_r($sql);
*/

write_logfile(0, "CRON:Job reminder start. Get subscriber list for $sms_keyword[1] LOKASI.");

$sql = CORE_URL."get_mdn_for_reminder.php?tx_id=$tx_id&key=hari_reminder|mentor_id&value=6|2&groupby=subscriber_id&curr_date=$curr_date";
echo $sql."<hr>";
$sql = get_data($sql);	
//print_r($sql); echo "<hr>";

if ($sql[totaldata] > 0)
	foreach ($sql["results"] as $data)
	{
		$subscriber_id = $data["subscriber_id"];	
		/*
		$sql2 = "SELECT * FROM log_sms_reminder WHERE subscriber_id=$subscriber_id AND sms_type='3'";
		//echo $sql2."<hr>";
		$sql2 = str_replace("\r\n","",$sql2);
		$sql2 = urlencode($sql2);
		$sql2 = CORE_URL."sql.php?sql=".$sql2;		
		$sql2 = get_data($sql2);
		*/
		//if ($sql2["nrows"] == 0)
		
		write_logfile(0, "CRON:Check if subscriber have updated their LOKASI.");
		
		$sql2 = CORE_URL."get_logsmsg.php?tx_id=$tx_id&key=mdn|sms_type&value=$data[mdn]|".urlencode("MO:KERJA LOKASI");
		echo $sql2."<hr>";
		$sql2 = get_data($sql2);
		
		if ($sql2[totaldata] == 0)
		{
			// $msg = "Utk mndpt lowngan d lokasi pilihn Anda ketik KERJA LOKASI (jaksel/jakbar/jakut/jakpus/jaktim/bekasi/tangerang/depok/bogor) sms ke 818.ch:KERJA LOKASI DEPOK";
			// $a = file_get_contents("http://10.99.4.5:8085/sendsms.php?msisdn=".$data["mdn"]."&message=".urlencode($msg)."&shortcode=818&msgid=".urlencode(str_replace(" ","_",$msg))."&appsid=GRAMEEN");
			// echo $a;					
			// echo $data["mdn"].": $msg<br>";		
			
			echo send_sms($data["mdn"], $msg_notif[12], $tx_id);

			/*
			$sql = "INSERT INTO log_sms_reminder(tx_id, subscriber_id, sms_type, sms_status, date_add, msg, mdn) 
						VALUES ('$tx_id', ".$data["subscriber_id"].", '3', '1', '$dtime', '$msg', ".$data["mdn"].")";	
						
			$sql = str_replace("\r\n","",$sql);
			$sql = urlencode($sql);
			$sql = CORE_URL."sql.php?sql=".$sql;
			//echo str_replace("10.99.4.5", "110.138.141.255", $sql)."<hr>";
			$sql = get_data($sql);			
			*/
			
			$sql2 = CORE_URL."add_logsmsreminder.php?tx_id=$tx_id&subscriber_id=$data[subscriber_id]&sms_type=3&sms_status=1&date_add=".urlencode($dtime)."&msg=".urlencode($msg_notif[12])."&mdn=$data[mdn]";
			echo $sql2."<hr>";
			$sql2 = get_data($sql2);
			
			write_logfile($data["mdn"], "CRON:Subscriber never updates their LOKASI. Send MT to subscriber.");
			write_logfile($data["mdn"], "CRON:MT: $msg_notif[12]");
		}
		/*
		else
		{
			foreach ($sql2["results"] as $val)
			{
				// $status = $val["sms_status"];
				// if($status == 1)
				{
					$msg = "Utk mndpt lowngan d lokasi pilihn Anda ketik KERJA LOKASI (jaksel/jakbar/jakut/jakpus/jaktim/bekasi/tangerang/depok/bogor) sms ke 818.ch:KERJA LOKASI DEPOK";
			
					$a = file_get_contents("http://10.99.4.5:8085/sendsms.php?msisdn=".$data["mdn"]."&message=".urlencode($msg)."&shortcode=818&msgid=".urlencode(str_replace(" ","_",$msg))."&appsid=GRAMEEN");
					//echo $a;					
					echo $data["mdn"].": $msg<br>";		

					
					$sql = "UPDATE log_sms_reminder SET sms_status = '1', date_update='$dtime'
							WHERE sms_type='3' AND subscriber_id = ".$data["subscriber_id"]."";	
					//die($sql);	
					$sql = str_replace("\r\n","",$sql);
					$sql = urlencode($sql);
					$sql = CORE_URL."sql.php?sql=".$sql;
					//echo str_replace("10.99.4.5", "110.138.141.255", $sql)."<hr>";
					$sql = get_data($sql);			
				}			
			}
		}
		*/
	}
	
/*** SEND SMS REMINDER KERJA PROFIL 5 HARI SETELAH ACTIVE	***/
echo "REMINDER PROFIL<hr>";
/*
$sql = "SELECT 
			subscribers.mdn AS mdn, subscribers.mentor_id AS mentor_id, jobcat_key, rel_subscriber_cat.date_add AS DATE_ADD, 
			rel_subscriber_cat.date_expired AS date_expired, rel_subscriber_cat.subscriber_id AS subscriber_id	
		FROM rel_subscriber_cat 
		LEFT JOIN subscribers ON rel_subscriber_cat.subscriber_id = subscribers.subscriber_id 	
		WHERE 
			TIMESTAMPDIFF(DAY, \"$curr_date\", rel_subscriber_cat.date_expired) = 3 AND subscribers.mentor_id=2	
		GROUP BY subscribers.mdn ORDER BY subscribers.mdn, DATE_ADD"; 

$sql = str_replace("\r\n","",$sql);
$sql = urlencode($sql);
$sql = CORE_URL."sql.php?sql=".$sql;
//echo str_replace("10.99.4.5", "110.138.141.255", $sql)."<hr>";
$sql = get_data($sql);	

print_r($sql);
*/

write_logfile(0, "CRON:Get subscriber list for $sms_keyword[1] PROFIL.");

$sql = CORE_URL."get_mdn_for_reminder.php?tx_id=$tx_id&key=hari_reminder|mentor_id&value=2|2&groupby=subscriber_id&curr_date=$curr_date";
echo $sql."<hr>";
$sql = get_data($sql);	

if ($sql[totaldata] > 0)
	foreach ($sql["results"] as $data)
	{		
		$subscriber_id = $data["subscriber_id"];	
		/*
		$sql2 = "SELECT * FROM log_sms_reminder WHERE subscriber_id=$subscriber_id";
		//die($sql2);
		$sql2 = str_replace("\r\n","",$sql2);
		$sql2 = urlencode($sql2);
		$sql2 = CORE_URL."sql.php?sql=".$sql2;		
		$sql2 = get_data($sql2);
		*/
		
		write_logfile(0, "CRON:Check if subscriber have updated their PROFIL.");
		if ($data[name] == "SMS")
		{
			// $msg = "Promosikn profil Anda k pmberi krja.ktk KERJA PROFIL (nama)#(tg/bln/th lahir)#(pnddkn: sd/sltp/slta/diploma/sarjana) ke 818.ch:KERJA PROFIL ADI#13/03/1983#SLTP";
			// $a = file_get_contents("http://10.99.4.5:8085/sendsms.php?msisdn=".$data["mdn"]."&message=".urlencode($msg)."&shortcode=818&msgid=".urlencode(str_replace(" ","_",$msg))."&appsid=GRAMEEN");
			// echo $a;					
			// echo $data["mdn"].": $msg<br>";		
			
			echo send_sms($data["mdn"], $msg_notif[13], $tx_id);
			/*
			$sql = "INSERT INTO log_sms_reminder(tx_id, subscriber_id, sms_type, sms_status, date_add, msg, mdn) 
						VALUES ('$tx_id', ".$data["subscriber_id"].", '2', '1', '$dtime', '$msg', ".$data["mdn"].")";	
						
			$sql = str_replace("\r\n","",$sql);
			$sql = urlencode($sql);
			$sql = CORE_URL."sql.php?sql=".$sql;
			//echo str_replace("10.99.4.5", "110.138.141.255", $sql)."<hr>";
			$sql = get_data($sql);			
			*/
			
			$sql2 = CORE_URL."add_logsmsreminder.php?tx_id=$tx_id&subscriber_id=$data[subscriber_id]&sms_type=2&sms_status=1&date_add=".urlencode($dtime)."&msg=".urlencode($msg_notif[13])."&mdn=$data[mdn]";
			echo $sql2."<hr>";
			$sql2 = get_data($sql2);
			
			write_logfile($data["mdn"], "CRON:Subscriber never updates their PROFIL. Send MT to subscriber.");
			write_logfile($data["mdn"], "CRON:MT: $msg_notif[13]");
		}
	/*
		else
		{
			foreach ($sql2["results"] as $val)
			{
				$status = $val["sms_status"];
				if($status == 1)
				{
					$msg = "Promosikn profil Anda k pmberi krja.ktk KERJA PROFIL (nama)#(tg/bln/th lahir)#(pnddkn: sd/sltp/slta/diploma/sarjana) ke 818.ch:KERJA PROFIL ADI#13/03/1983#SLTP";
			
					$a = file_get_contents("http://10.99.4.5:8085/sendsms.php?msisdn=".$data["mdn"]."&message=".urlencode($msg)."&shortcode=818&msgid=".urlencode(str_replace(" ","_",$msg))."&appsid=GRAMEEN");
					//echo $a;					
					echo $data["mdn"].": $msg<br>";		

					
					$sql = "UPDATE log_sms_reminder SET sms_status = '1', date_update='$dtime'
							WHERE sms_type='2' AND subscriber_id = ".$data["subscriber_id"]."";	
					//die($sql);	
					$sql = str_replace("\r\n","",$sql);
					$sql = urlencode($sql);
					$sql = CORE_URL."sql.php?sql=".$sql;
					//echo str_replace("10.99.4.5", "110.138.141.255", $sql)."<hr>";
					$sql = get_data($sql);			
				}			
			}
		}
	*/	
	}

/*** SEND SMS REMINDER 2 HARI SEBELUM EXPIRED	***/
echo "REMINDER EXPIRED H-$hari_reminder[1]<hr>";

//$sql = "SELECT * FROM rel_subscriber_jobcat WHERE (date_expired - '$min_date_expired') = '$curr_date' AND status = 1"; 
// $sql = "SELECT mdn, rel_subscriber_cat.status AS status, rel_subscriber_cat.jobcat_key AS jobcat_key, 
	// rel_subscriber_cat.date_add AS date_add, rel_subscriber_cat.date_expired AS date_expired,
	// subscribers.subscriber_id AS subscriber_id
	// FROM rel_subscriber_cat 
	// INNER JOIN subscribers ON rel_subscriber_cat.subscriber_id = subscribers.subscriber_id WHERE 
	// TIMESTAMPDIFF(DAY, \"$curr_date\", rel_subscriber_cat.date_expired) = 2 ORDER BY mdn, date_add"; 
// $sql = str_replace("\r\n","",$sql);
// $sql = urlencode($sql);
// $sql = CORE_URL."sql.php?sql=".$sql;
//echo str_replace("10.99.4.5", "110.138.141.255", $sql)."<hr>";

write_logfile(0, "CRON:Get subscriber list for reminder H-$hari_reminder[1].");

$sql = CORE_URL."get_mdn_for_reminder.php?tx_id=$tx_id&key=hari_reminder|rel_status&value=$hari_reminder[1]|1&curr_date=$curr_date";
echo $sql."<hr>";
$sql = get_data($sql);	

//echo "send sms reminder -1 day"."<hr>";
if ($sql[totaldata] > 0)
	foreach ($sql["results"] as $data)
	{				
		$jobcat_key = $data[jobcat_key];
		// $msg = "Langganan lowongan $data[jobcat_key] akan sgr berakhir dan diprpnjang otomatis. Rp3rb/mg.1SMS/hr. Pastikan pulsa Anda cukup. Stop:unreg kerja (pekerjaan) sms ke 818";
		// $a = file_get_contents("http://10.99.4.5:8085/sendsms.php?msisdn=".$data["mdn"]."&message=".urlencode($msg)."&shortcode=818&msgid=".urlencode(str_replace(" ","_",$msg))."&appsid=GRAMEEN");

		$date_expired = explode("-",$data["date_expired"]);
		$new_date_expired = $date_expired[2]."-".$date_expired[1]."-".$date_expired[0];
		
		$msg = str_replace("%1%", $jobcat_key, $msg_notif[5]);
		$msg = str_replace("%2%", $new_date_expired, $msg);
		echo send_sms($data["mdn"], $msg, $tx_id);

		$sql2 = CORE_URL."add_logsmsreminder.php?tx_id=$tx_id&subscriber_id=$data[subscriber_id]&sms_type=1&sms_status=1&date_add=".urlencode($dtime)."&msg=".urlencode($msg)."&mdn=$data[mdn]";
		echo $sql2."<hr>";
		$sql2 = get_data($sql2);

		write_logfile($data["mdn"], "CRON:Send MT to subscriber.");
		write_logfile($data["mdn"], "CRON:MT: $msg");
		
	/*
		echo $data["mdn"].": $msg<br>";
		$sql = "INSERT INTO log_sms_reminder(tx_id, subscriber_id, sms_type, sms_status, date_add, msg, mdn) 
				VALUES ('$tx_id', ".$data["subscriber_id"].", '1', '1', '$dtime', '$msg', ".$data["mdn"].")";		
		$sql = str_replace("\r\n","",$sql);
		$sql = urlencode($sql);		
		$sql = CORE_URL."sql.php?sql=".$sql;
		echo str_replace("10.99.4.5", "110.138.141.255", $sql)."<hr>";
		$sql = get_data($sql);	
	*/
	}

echo "REMINDER EXPIRED H-$hari_reminder[2]<hr>";

write_logfile(0, "CRON:Get subscriber list for reminder H-$hari_reminder[2].");

$sql = CORE_URL."get_mdn_for_reminder.php?tx_id=$tx_id&key=hari_reminder&value=$hari_reminder[2]&curr_date=$curr_date";
$sql = get_data($sql);	
print_r($sql);

//echo "send sms reminder -1 day"."<hr>";
if ($sql[totaldata] > 0)
	foreach ($sql["results"] as $data)
	{				
		$jobcat_key = $data[jobcat_key];

		$msg = str_replace("%1%", $jobcat_key, $msg_notif[14]);
		echo send_sms($data["mdn"], $msg, $tx_id);

		$sql2 = CORE_URL."add_logsmsreminder.php?tx_id=$tx_id&subscriber_id=$data[subscriber_id]&sms_type=1&sms_status=1&date_add=".urlencode($dtime)."&msg=".urlencode($msg)."&mdn=$data[mdn]";
		echo $sql2."<hr>";
		$sql2 = get_data($sql2);

		write_logfile($data["mdn"], "CRON:Send MT to subscriber.");
		write_logfile($data["mdn"], "CRON:MT: $msg");
	/*
		echo $data["mdn"].": $msg<br>";
		$sql = "INSERT INTO log_sms_reminder(tx_id, subscriber_id, sms_type, sms_status, date_add, msg, mdn) 
				VALUES ('$tx_id', ".$data["subscriber_id"].", '1', '1', '$dtime', '$msg', ".$data["mdn"].")";		
		$sql = str_replace("\r\n","",$sql);
		$sql = urlencode($sql);		
		$sql = CORE_URL."sql.php?sql=".$sql;
		echo str_replace("10.99.4.5", "110.138.141.255", $sql)."<hr>";
		$sql = get_data($sql);	
	*/
	}

echo "</pre>";

write_logfile(0, "CRON:Job reminder end.");

?>