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
//get contents
include("content_managesubscriptions.php");


//footer
include("content_footer.php");

?>
	


