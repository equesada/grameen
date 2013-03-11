
<?
/*
This code is owned by Grameen Applab Indonesia

Copying and re-using the code is prohibited without permission from  Grameen Applab Indonesia

-April 2012-
-Ramot Lubis-

*/




$idTips = $_REQUEST['idTips'];


if (isset($_REQUEST['idTips']))
{

$appUrl = "http://ec2-107-20-14-148.compute-1.amazonaws.com/infokerja_core/";
$urlTips = $appUrl."get_tips_by_parent_id.php?id=".$idTips."";

$ch = curl_init($urlTips);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, CURL_TIMEOUT);
$jsonData = curl_exec($ch);
$data = json_decode($jsonData, TRUE);



echo '
<div data-role="page" id="page1">
            <div data-theme="a" data-role="header">
<div>
    <a href="request.php" data-transition="slide"><img border="0" src="images/kerjalokal.gif" alt="Kerja Lokal" style="float:left; display:inline; margin-left:14px; margin-top:11px;" /></a>
</div>       
				<h3 style="margin-left:14px">
                    Tips and Tricks - '.$data[0]['tips_title'].' 
                </h3>
            </div>
            <div data-role="content">


';

echo str_replace("\n", "<br>", $data[0]['description']);	

}
else {


?> 
		<div data-role="page" id="page1">
            <div data-theme="a" data-role="header" align="left">
<div>
    <a href="request.php" data-transition="slide"><img border="0" src="images/kerjalokal.gif" alt="Kerja Lokal" style="float:left; display:inline; margin-left:14px; margin-top:11px;" /></a>
</div>       				<h3 style="margin-left:14px">
                    <?=$LANG['tipstrik_title']?>
                </h3>
            </div>
            <div data-role="content">

 
                <ul data-role="listview" data-divider-theme="b" data-inset="false">
                    <li data-theme="c">
                        <a href="request.php?act=tipstrick&idTips=1" data-transition="slide">
                            <?=$LANG['tipstrik_creatingcv']?>
                        </a>
                    </li>
                    <li data-theme="c">
                        <a href="request.php?act=tipstrick&idTips=2" data-transition="slide">
                            <?=$LANG['tipstrik_writingyourjob']?>
                        </a>
                    </li>
                    <li data-theme="c">
                        <a href="request.php?act=tipstrick&idTips=3" data-transition="slide">
                            <?=$LANG['tipstrik_interview']?>
                        </a>
                    </li>
                    <li data-theme="c">
                        <a href="request.php?act=tipstrick&idTips=4" data-transition="slide">
                            <?=$LANG['tipstrik_salary']?>
                        </a>
                    </li>
                    <li data-theme="c">
                        <a href="request.php?act=tipstrick&idTips=5" data-transition="slide">
                            <?=$LANG['tipstrik_contract']?>
                        </a>
                    </li>
                </ul>
 

<?	


}	
?>
           </div>
        </div>		