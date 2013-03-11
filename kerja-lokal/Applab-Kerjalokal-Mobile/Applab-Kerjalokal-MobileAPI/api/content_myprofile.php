<?
/*
This code is owned by Grameen Applab Indonesia

Copying and re-using the code is prohibited without permission from  Grameen Applab Indonesia

-April 2012-
-Ramot Lubis-

*/

session_start();

$subscriber_id = $_SESSION['subscriber_id'];
$loginTime = $_SESSION['loginTime'];


if (isset($subscriber_id) && isset($loginTime)) {


//$appUrl = "http://ec2-107-20-14-148.compute-1.amazonaws.com/infokerja_core/";
$urlMyProfile= $CORE_URL."get_subscriber_by_subscriber_id.php?subscriber_id=".$subscriber_id;



//get all subscribed job categories
$ch = curl_init($urlMyProfile);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, CURL_TIMEOUT);
$jsonData = curl_exec($ch);
$data = json_decode($jsonData, TRUE);
$act2 = $_REQUEST['act2'];
//print_r ($data);


curl_close($ch);



if ($act2 != "update" || $_REQUEST['submit'] != $LANG['myprofile_savebutton'])
{

?>

        <div data-role="page" id="page15">
            <div data-theme="a" data-role="header" align="left">
<div>
    <a href="request.php" data-transition="slide"><img border="0" src="images/kerjalokal.gif" alt="Kerja Lokal" style="float:left; display:inline; margin-left:14px; margin-top:11px;" /></a>
</div>                

				<h3 style="margin-left:14px">
                    <?=$LANG['myprofile_title']?>
                </h3>
            </div>
            <div data-role="content">

<!--			
    <script type="text/javascript" charset="utf-8" src="cordova-1.5.0.js"></script>
    <script type="text/javascript" charset="utf-8">
    function getPicture() {
        navigator.camera.getPicture(onCamSuccess, onCamFail, { quality: 50 }); 
	}
	function onCamSuccess(imageData) {
		var image = document.getElementById('myProfilePic');
		image.src = "data:image/jpeg;base64," + imageData;
	}

	function onCamFail(message) {
		alert('Failed because: ' + message);
	}
	</script>
-->					

			<form name="formmyprofile" action="request.php?act=myprofile&act2=update" method="post">
                        <label for="fullname">
                             <?=$LANG['myprofile_fullname']?>
                        </label>
                        <input name="fullname" id="fullname" placeholder="" value="<?=$data['name']?>" type="text" />
						<br>
                        <label for="mobilenumber">
                             <?=$LANG['myprofile_mobilenumber']?>
                        </label>
                        <input name="mobilenumber" id="mobilenumber" placeholder="" value="<?=$data['mdn']?>" type="tel" readonly />
						<br>
                        <label for="jobmatchlocation" readonly>
                             <?=$LANG['myprofile_jobmatchlocation']?>
                        </label>
                        <textarea name="jobmatchlocation" id="jobmatchlocation" placeholder="">
GPS: <?=$data['kelurahan_lat']?>:<?=$data['kelurahan_lng']?>
                        </textarea>
						<br>
                <a data-role="button" data-transition="fade" href="request.php?act=updateprofile&step=register2" data-icon="arrow-r" data-iconpos="right">
                     <?=$LANG['myprofile_changejobmatchloc']?>
                </a>
                <br>
                        <label for="pob">
                             <?=$LANG['myprofile_pob']?>
                        </label>
                        <input name="pob" id="pob" placeholder="" value="<?=$data['place_birth']?>" type="text" />
                        <br>
						<label for="dob">
                             <?=$LANG['myprofile_dob']?>
                        </label>
                        <input name="dob" id="dob" placeholder="" value="<?=$data['birthday']?>" type="date" />
						<br>
                        <label for="idcard">
                             <?=$LANG['myprofile_idnum']?>
                        </label>
                        <input name="idcard" id="idcard" placeholder="" value="<?=$data['idcard']?>" type="text" />
                    </fieldset>
					<br>
                    <label for="gender">
                         <?=$LANG['myprofile_gender']?>
                    </label>
					<?
					$maleSelected = ($data['gender'] == "M")?"selected":"";
					$femaleSelected = ($data['gender'] != "M")?"selected":"";
					
					?>
                    <select name="gender" id="gender">
                        <option <?=$maleSelected?> value="M">
                             <?=$LANG['myprofile_gendermale']?>
                        </option>
                        <option <?=$femaleSelected?> value="F">
                             <?=$LANG['myprofile_genderfemale']?>
                        </option>
                    </select>
                <div align=center>
                    <img id="myProfilePic" src="http://2.bp.blogspot.com/_LbPgRRxXBc4/SqEySV2F1sI/AAAAAAAAAKs/aGlEwt96mBw/s320/Wim+Staessens+HogeHerenwebsite.jpg" />
                </div>
                <a data-role="button" data-transition="fade" href="#" onClick="getPicture()">
                     <?=$LANG['myprofile_updatepicture']?>
                </a>
						<br>
                        <label for="homeaddress">
                             <?=$LANG['myprofile_homeaddress']?>
                        </label>
                        <textarea name="homeaddress" id="homeaddress" placeholder="">
<?=$data['address1']?>
<?=$data['kelurahan_name']?> 
<?=$data['kecamatan_name']?> 
<?=$data['kotamadya_name']?> 
<?=$data['province_name']?>
						
                        </textarea>
						<br>
                    <label for="educationlevel">
                         <?=$LANG['myprofile_educationlevel']?>
                    </label>
                    <select name="educationlevel" id="educationlevel">
                        <option value="1">
                             <?=$LANG['myprofile_juniorhighschool']?>
                        </option>
                        <option value="2">
                             <?=$LANG['myprofile_seniorhighschool']?>
                        </option>
                        <option value="3">
                             <?=$LANG['myprofile_diploma']?>
                        </option>
                        <option value="4">
                             <?=$LANG['myprofile_bachelordegree']?>
                        </option>
                        <option value="0">
                             <?=$LANG['myprofile_other']?>
                        </option>
                    </select>
        
 
			<input type="submit" name="submit" value="<?=$LANG['myprofile_savebutton']?>"  />

			</form>	
            </div>
            <div data-theme="a" data-role="footer" data-position="fixed">
            </div>
        </div>
		






		
<?
}else {
//UPDATE MYPROFILE


//$appUrl = "http://ec2-107-20-14-148.compute-1.amazonaws.com/infokerja_core/";
$urlUpdateMyProfile = $CORE_URL."update_subscriber.php?subscriber_id=".$subscriber_id."&name=".urlencode($_REQUEST['fullname']);
$urlUpdateMyProfile .= "&gender=".urlencode($_REQUEST['gender']);
$urlUpdateMyProfile .= "&edu_id=".urlencode($_REQUEST['educationlevel']);
$urlUpdateMyProfile .= "&place_birth=".urlencode($_REQUEST['pob']);
$urlUpdateMyProfile .= "&birthday=".urlencode($_REQUEST['dob']);
$urlUpdateMyProfile .= "&idcard=".urlencode($_REQUEST['idcard']);
$urlUpdateMyProfile .= "&date_update=".urlencode(date("Y-m-d"));



//get all subscribed job categories
$ch = curl_init($urlUpdateMyProfile);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, CURL_TIMEOUT);
$jsonData = curl_exec($ch);
$data = json_decode($jsonData, TRUE);





?>
        <div data-role="page" id="page15">
            <div data-theme="a" data-role="header" align="left">
<div>
    <a href="request.php" data-transition="slide"><img border="0" src="images/kerjalokal.gif" alt="Kerja Lokal" style="float:left; display:inline; margin-left:14px; margin-top:11px;" /></a>
</div>      
				<h3 style="margin-left:14px">
                    <?=$LANG['myprofile_title']?>
                </h3>
            </div>
			<div data-role="content" style="text-align=center">
			<div align=center>
			<?=$LANG['myprofile_updatestatus']?>: <?=$data['status']?><br/>
			<?=$LANG['myprofile_updatemessage']?>: <?=(empty($data['msg']) && $data['status']==1)?$LANG['myprofile_updatesuccessmsg']:$data['msg']?><br/>
			</div>
                <a data-role="button" data-transition="fade" href="request.php?act=myprofile">
                     <?=$LANG['myprofile_backtomyprofile']?>
                </a>
                
            </div>
		</div>
<?

}






//end is login
}
else {

include("content_login.php");

}
?>		