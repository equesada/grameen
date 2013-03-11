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

$sms_status = "1";

write_logfile(0, "CRON:Job sending start. Get job list");
$json = CORE_URL."get_jobsends.php?tx_id=$tx_id&key=jobsend_status&value=1";
echo $json."<hr>";
$json = get_data($json);
print_r($json); echo "<hr>";

foreach ($json["results"] as $job)
{
	$rel_id = $job["rel_id"];
	$job_id = $job["job_id"];
	$jobcat_id = $job["jobcat_id"];
	$jobsend_id = $job["jobsend_id"];
	$n_send = $job["n_send"] == "0" ? "1" : $job["n_send"];
	$n_jobreceived = $job["n_jobreceived"] == "" ? "0" : $job["n_jobreceived"];
	$subscriber_id = $job["subscriber_id"];
	$mdn = $job["mdn"];
	if ($job_id != "1")
	{
		if ($job["sms"] != "")
		{ $msg = $job["sms"]; }
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

	send_sms(substr($mdn,1), $msg, $tx_id);
	write_logfile(substr($mdn,1), "CRON:MT: $msg");

	/*
	$a = BR14_URL."msisdn=62".substr($mdn,1)."&message=".urlencode($msg)."&msgid=".urlencode(str_replace(" ","_",$msg))."&shortcode=81820&appsid=GRAMEEN";
	echo $a."<hr>";
	$a = file_get_contents($a);
		//echo str_replace("10.99.4.5", "110.138.141.255", $a)."<hr>";
	*/	
	echo $a."<hr>";


	//$writelog = CORE_URL."add_logsms.php?tx_id=".urlencode($tx_id)."&job_id=$job_id&jobsend_id=$jobsend_id&rel_id=$rel_id&subscriber_id=$subscriber_id&title=sms&msg=".urlencode($msg)."&mdn=$mdn&status=1&date_send=".urlencode($dtime);	
	$writelog = CORE_URL."add_logsms.php?tx_id=".urlencode($tx_id)."&job_id=$job_id&jobcat_id=$jobcat_id&rel_id=$rel_id&subscriber_id=$subscriber_id&jobsend_id=$jobsend_id&title=sms&msg=".urlencode($msg)."&mdn=$mdn&status=1&date_send=".urlencode($dtime);	
	echo $writelog."<Br>";
	$writelog = get_data($writelog);
	print_r($writelog); echo "<hr>";

	$is_mentor = CORE_URL."check_mdn.php?tx_id=".urlencode($tx_id)."&mdn=$mdn&type=mentor";
	$is_mentor = get_data($is_mentor);
	if ($is_mentor["status"] == "1")
	{
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
		print_r($writelog); echo "<hr>";
		write_logfile(0, "CRON:DMS: $msg");
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
		
		$n_jobreceived++;
		
		$json2 = CORE_URL."update_rel_subscriber.php?tx_id=$tx_id&key=rel_id&value=$rel_id".
			"&update_key=date_update|update_by_name|update_notes|n_jobreceived".
			"&update_value=".urlencode($dtime)."|SYS_00|".urlencode("CRON:Job send $curr_date")."|$n_jobreceived";
		echo $json2."<hr>";
		$json2 = get_data($json2);
		print_r($json2);
		write_logfile(0, "CRON:Update job statistic.");
	}
	else
	{
		$json2 = CORE_URL."update_jobsend.php?tx_id=".urlencode($tx_id)."&jobsend_id=$jobsend_id&status=1&date_update=".urlencode($dtime)."&date_send=".date("Y-m-d", (time() + 7*86400));
		echo $json2."<hr>";
		$json2 = get_data($json2);
		print_r($json2);
	}
	
}
// date_default_timezone_set("Asia/Jakarta");
// $next_date = date("Y-m-d H:i:s", (strtotime($dtime) + 86400*2));
// $query  = "DELETE FROM rel_subscriber_cat WHERE DATE_ADD < '$next_date' AND STATUS=2";
// $result = mysql_query($query) or die(mysql_error());
 echo "</pre>";
 write_logfile(0, "CRON:Job sending end.");

?>