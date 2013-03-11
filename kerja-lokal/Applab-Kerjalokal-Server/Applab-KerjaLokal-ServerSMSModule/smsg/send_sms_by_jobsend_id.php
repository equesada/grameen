<?php
require_once("conf.php");
require_once("func.php");

//$tx_id = get_uuid("sms");

$tx_id = isSet($_GET["tx_id"]) ? $_GET["tx_id"] : "0";
$jobsend_id = isSet($_GET["jobsend_id"]) ? $_GET["jobsend_id"] : "0";
$mentor_id = isSet($_GET["mentor_id"]) ? $_GET["mentor_id"] : "1";	

$id = isSet($_GET["id"]) ? $_GET["id"] : 0;
$dtime = date("Y-m-d H:i:s", time() + ($id * 86400));
$curr_date = date("Y-m-d", strtotime($dtime));
$next_date = date("Y-m-d", (strtotime($dtime) + 86400));
$next_2date = date("Y-m-d", (strtotime($dtime) + 86400 + 86400));
$date_expired = date("Y-m-d", (strtotime($dtime) + (7 * 86400)));

// echo $dtime."<br>";
// echo $curr_date."<br>";
// echo $next_date."<br>";
// echo $date_expired."<hr>";

$sms_status = "1";

echo "<pre>"; 
$json = CORE_URL."get_jobsends_by_jobsend_id.php?tx_id=".urlencode($tx_id)."&jobsend_id=$jobsend_id";
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
	
	//echo "Send to $mdn : $msg<br>";
	
	if($mentor_id != '0'){ 
		echo $a = send_sms(substr($mdn,1), $msg, $tx_id);
		//$a = "http://10.99.1.5:8085/sendsms.php?msisdn=62".substr($mdn,1)."&message=".urlencode($msg)."&msgid=".time()."&appsid=GRAMEEN";
		//$a = file_get_contents($a);
		//echo str_replace("10.99.1.5", "110.138.141.255", $a)."<hr>";
		//echo $a."<hr>";
		write_logfile(substr($mdn,1), "SMSG:Send instant job to user");
		write_logfile(substr($mdn,1), "SMSG:MT: $msg");
	}	
	//$writelog = CORE_URL."add_logsms.php?tx_id=".urlencode($tx_id)."&job_id=$job_id&jobsend_id=$jobsend_id&rel_id=$rel_id&subscriber_id=$subscriber_id&title=sms&msg=".urlencode($msg)."&mdn=$mdn&status=1&date_send=".urlencode($dtime);	
	$writelog = CORE_URL."add_logsms.php?tx_id=".urlencode($tx_id)."&job_id=$job_id&jobcat_id=$jobcat_id&rel_id=$rel_id&subscriber_id=$subscriber_id&jobsend_id=$jobsend_id&&title=sms&msg=".urlencode($msg)."&mdn=$mdn&status=1&date_send=".urlencode($dtime);	
    echo $writelog."<hr>";
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
		write_logfile(substr($mdn,1), "SMSG:DMS: $msg");
	}
	
	if ($sms_status == "1")
	{
		$json2 = CORE_URL."update_jobsend.php?tx_id=".urlencode($tx_id)."&jobsend_id=$jobsend_id&status=2&date_update=".urlencode($dtime);
		echo $json2."<hr>";
        $json2 = get_data($json2);
		print_r($json2); echo "<hr>";
		$n_send++;
		$json2 = CORE_URL."update_jobpost.php?tx_id=".urlencode($tx_id)."&job_id=$job_id&n_send=".$n_send."&date_update=".urlencode($dtime);
		echo $json2."<hr>";
        $json2 = get_data($json2);
		print_r($json2); echo "<hr>";
		$json2 = CORE_URL."update_rel_subscriber.php?tx_id=".urlencode($tx_id)."&key=rel_id&value=$rel_id&update_key=n_jobreceived|date_update&update_value=".($n_jobreceived+1)."|".urlencode($dtime);
		echo $json2."<hr>";
        $json2 = get_data($json2);
		print_r($json2); echo "<hr>";
	}
	else
	{
		$json2 = CORE_URL."update_jobsend.php?tx_id=".urlencode($tx_id)."&jobsend_id=$jobsend_id&status=1&date_update=".urlencode($dtime)."&date_send=".date("Y-m-d", (time() + 7*86400));
		echo $json2."<hr>";
		$json2 = get_data($json2);
		print_r($json2); echo "<hr>";
	}

}
echo "</pre>";

?>