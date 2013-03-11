<?php
/*
$mysql_host = "localhost";
$mysql_database = "t32907_infokerja";
$mysql_user = "t32907_myuser";
$mysql_password = "t32907_mypwd";
*/

//$host = "10.99.4.5:8085";
$host = "180.243.231.8:8085";
//$host = $_SERVER['HTTP_HOST'].":8085";
$mysql_host = $_SERVER['HTTP_HOST'];
$mysql_host = "localhost";
$mysql_database = "infokerja";
$mysql_user = "root";
$mysql_password = "pendekarganteng";

define("BASE_URL","http://".$host."/infokerja/");
define("CORE_URL","http://".$host."/infokerja_core/");
define("APP_URL","http://10.99.4.1/grameen/infokerja/");

define("SMS_URL","http://".$host."/smsg/"); //NEW YUDHA

$conn = mysql_connect($mysql_host,$mysql_user,$mysql_password);
mysql_select_db($mysql_database, $conn);

$t_admin = "admin";
$t_contents = "contents";
$t_companies = "companies";
$t_broadcast_sms = "broadcast_sms";
$t_broadcast_email = "broadcast_email";
$t_country = "country";
$t_country_setting = "country_setting";
$t_industry = "industry";
$t_education = "education";
$t_job_posters = "job_posters";
$t_job_match = "job_match";
$t_job_weights = "job_weights";
$t_jobs = "jobs";
$t_jobs_apply = "jobs_apply";
$t_jobs_category = "jobs_category";
$t_jobs_send = "jobs_send";
$t_location = "location";
$t_log_web = "log_web";
$t_log_web2 = "log_web2";
$t_log_dms = "log_dms";
$t_log_sms = "log_sms";
$t_log_smsg = "log_smsg";
$t_log_charging = "log_charging";
$t_log_sms_reminder = "log_sms_reminder";
$t_mentors = "mentors";
$t_rel_subscription_smsg = "rel_subscription_smsg";
$t_rel_subscriber_cat = "rel_subscriber_cat";
$t_rel_subscriber_company = "rel_subscriber_company";
$t_status = "status";
$t_subscribers = "subscribers";
$t_subscriber_pulsa = "subscriber_pulsa";
$t_tarif = "tarif";
$t_themes = "themes";
$t_tb_prefix = "tb_prefix";
$t_manual = "manual";
$t_tarif = "tarif";

$t_log_broadcast_sms = "log_broadcast_sms";
$t_log_broadcast_email = "log_broadcast_email";

$t_help = "help";
$t_tips = "tips";
$t_log_sms_reminder = "log_sms_reminder";

$tarif = 1000;

//header('Content-type: application/json');
?>