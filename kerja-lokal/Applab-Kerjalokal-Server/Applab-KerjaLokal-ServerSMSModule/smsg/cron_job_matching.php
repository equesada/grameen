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

write_logfile(0, "CRON:Job matching start. Get subscription not expired.");
$json = CORE_URL."get_rel_subscriber_cat_not_expired.php?tx_id=$tx_id";
echo $json."<hr>";
$json = get_data($json);
//echo "<pre>"; print_r($json); echo "</pre>";

foreach ($json["results"] as $rel)
{
	write_logfile(0, "CRON:Check last sent job");
	$json2 = CORE_URL."get_logsms2.php?tx_id=$tx_id&key=rel_id&value=$rel[rel_id]&order=date_send&ascdesc=desc&ndata=1&page=1";
	echo $json2."<hr>";
	$json2 = get_data($json2);
	$logsms = $json2["results"][0];
	
	$date_lastsend = substr($logsms['date_send'],0,strpos($logsms['date_send']," "));
	$n_day = (strtotime($curr_date)-strtotime($date_lastsend))/86400;
	echo "$curr_date - $date_lastsend = $n_day<hr>";
	//$diff_day_jobreceived = $n_day - $rel["n_jobreceived"];
	if ($n_day > 1)
	{
		write_logfile(0, "CRON:Update subscription expiry date. Reason: CRON off for ".($n_day-1)." day(s)");
		$new_date_expired = date("Y-m-d", (strtotime($rel["date_expired"]) + (($n_day-1) * 86400)));
		$update_rel = CORE_URL."update_rel_subscriber.php?tx_id=".urlencode($tx_id)."&key=rel_id&value=".$rel["rel_id"].
			"&update_key=date_expired|date_update|update_by_name|update_notes&update_value=".urlencode($new_date_expired)."|".$curr_date."|SYS_00|".urlencode("CRON off for ".($n_day-1)." day(s)");
		echo $update_rel."<hr>";
		$update_rel = get_data($update_rel);
	}
	
	//echo $date_add." - ".$curr_date." - ".$n_day." - ".$n_job."<hr>";
	write_logfile(0, "CRON:Add job to be sent");
	$job_n = $rel["n_jobreceived"]+1;
	$addjob = CORE_URL."add_jobsend.php?tx_id=".urlencode($tx_id)."&jobcat_id=".$rel["jobcat_id"]."&subscriber_id=".$rel["subscriber_id"]."&rel_id=".$rel["rel_id"]."&job_n=$job_n&date_add=".urlencode($dtime)."&date_send=".urlencode($dtime);
	echo $addjob."<hr>";
	$addjob = get_data($addjob);
	
	print_r($addjob); echo "<hr>";
	//echo "Next job : ".$addjob["job_id"]." - ".$addjob["n_job"]." - ".$addjob["n_qualified_job"]." - <br>";

}
write_logfile(0, "CRON:Job matching end.");

?>