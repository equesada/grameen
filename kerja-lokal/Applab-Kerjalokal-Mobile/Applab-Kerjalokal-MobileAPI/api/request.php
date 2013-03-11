<?
/*
This code is owned by Grameen Applab Indonesia

Copying and re-using the code is prohibited without permission from  Grameen Applab Indonesia

-April 2012-
-Ramot Lubis-

*/

error_reporting(E_ERROR | E_WARNING | E_PARSE);
$CORE_URL = "http://180.243.231.8:8085/infokerja_core/";

session_start();

$act = $_REQUEST['act'];
//$act = empty($_REQUEST['act'])?$_SESSION['ses_act']:$_REQUEST['act'];


if (isset($_REQUEST['language'])) {
	$_SESSION['language'] = $_REQUEST['language'];
	header("Location: request.php?act=".$act);
	exit;
	
}



/* 
//
//I was thinking to put $act in session, so JQUERY url fragmet ("#") is working fine.
//It's not working yet..

if (!empty($_REQUEST['act'])) {

	if ($_REQUEST['act']=="myprofile" || $_REQUEST['act']=="jobmanagesub" || $_REQUEST['act']=="jobsubscribe") {
		$_SESSION['ses_act'] = $_REQUEST['act'];
		header("Location: request.php");
		exit;
	}	
}else  {
	$act = $_SESSION['ses_act'];
	$_SESSION['ses_act'] = "";

}
*/

if (!isset($_SESSION['loginTime']) || !isset($_SESSION['subscriber_id']))	{
	if ($act != "appregister")
		$_SESSION['res_act'] = "applogin";
}else {
	if ($_REQUEST['act'] == 'applogin' )
		$_SESSION['res_act'] = "";

}		




// load application language 	
// ramot lubis, april 2012
if (!isset($_SESSION['language'])) {
	$_SESSION['language'] = "english";
	include("language/english.php");
}else
	include("language/".$_SESSION['language'].".php");
	

?>

<!DOCTYPE html>
<html>
    <head>
		<meta http-equiv='cache-control' content='no-cache'>
		<meta http-equiv='expires' content='0'>
		<meta http-equiv='pragma' content='no-cache'>
	
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title><?=$LANG['app_title']?>
        </title>
	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.css" />
	<script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.js"></script>

    </head>
    <body>
<?





//get content page
switch ($act) {
	case "jobcat": 
				$url = $CORE_URL."get_jobcat_group_by_subscriber_id.php?status=1&id=".trim($_SESSION['subscriber_id']);
							echo $url;

				include("content_jobcategory.php");
				break;
	case "joblist": 
//				$url = $CORE_URL."get_logsms_by_limit.php?tx_id=201203d45b10571427472f8fff075905f26c37&id=".$_SESSION['subscriber_id']."&jobcat_id=".$_REQUEST['job_cat_id']."&order=date_send&status=2";
				
				$url = $CORE_URL."get_last_10_jobs.php?subscriber_id=".trim($_SESSION['subscriber_id'])."&jobcat_id=".trim($_REQUEST['job_cat_id']);
				
				include("content_joblist.php");
				break;

	case "jobdetails": 
//				$url = $CORE_URL."get_logsms_by_log_id.php?tx_id=2012033c1037377ae248b599a6bc3eb685be12&log_id=".$_REQUEST['log_id'];
				$url = $CORE_URL."get_jobpost_by_job_id.php?id=".$_REQUEST['job_id'];
				
				
				include("content_jobdetails.php");
				break;

	case "jobmanagesub": 
				include("content_managesubscriptions.php");
				//header("Location: subscriptions.php"); 
				//exit;
				break;
	case "jobsubscribe": 
				include("content_subscribejobcategory.php");
				break;
	case "tipstrick": 
				include("content_tipstrick.php");
				break;
				
	case "myprofile": 
				include("content_myprofile.php");
				break;
	case "applogin": 
				include("content_login.php");
				break;
	case "appregister": 
				include("content_register.php");
				break;
	case "updateprofile": 
				include("content_updatelocationprofile.php");
				break;
	case "logout": 
				session_destroy();
				include("content_login.php");
				break;

	default: include("content_home.php");

}


//footer
include("content_footer.php");

?>