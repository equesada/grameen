<?
/*
This code is owned by Grameen Applab Indonesia

Copying and re-using the code is prohibited without permission from  Grameen Applab Indonesia

-May 2012-
-Ramot Lubis-

*/

error_reporting(E_ERROR | E_WARNING | E_PARSE);
$CORE_URL = "http://180.243.231.8:8085/infokerja_core/";

session_start();


$registerStep = $_REQUEST['step'];


if (empty($registerStep) || $registerStep=="register1") {


//check if this is re-attempt to register (after an error or something else)
if ($_REQUEST['act2']=="redo")
	$dataRegPrev = $_SESSION['dataReg'];
else
	$_SESSION['dataReg'] = ""; //set me free
?>

<!---REGISTRATION PAGE STEP 1-->

        <div data-role="page" id="page8">
            <div data-theme="a" data-role="header" align="left">
<div>
    <a href="request.php" data-transition="slide"><img border="0" src="images/kerjalokal.gif" alt="Kerja Lokal" style="float:left; display:inline; margin-left:14px; margin-top:11px;" /></a>
</div>                

				<h3 style="margin-left:14px">
                    <?=$LANG['registration_title']?>
                </h3>
            </div>
			<form action="request.php?act=appregister" method=post>
			<input name="step" value="register2" type="hidden" />
            <div data-role="content">
                <div>
                    <div align=right>
                        <?=$LANG['registration_step1']?>
                    </div>
                </div>
                <div data-role="fieldcontain">
                    <fieldset data-role="controlgroup">
                        <label for="textinput3">
                            <?=$LANG['registration_fullname']?>
                        </label>
                        <input id="textinput3" name="fullname" placeholder="" value="<?=(!empty($dataRegPrev['1']))?$dataRegPrev['1']['fullname']:""?>" type="text" />
                    </fieldset>
                </div>
                <div data-role="fieldcontain">
                    <fieldset data-role="controlgroup">
                        <label for="textinput35">
                            <?=$LANG['registration_mobilenumber']?>
                        </label>
                        <input id="textinput35"  name="mobilephone" placeholder="" value="<?=!empty($dataRegPrev['1'])?$dataRegPrev['1']['mobilephone']:""?>" type="tel" />
                    </fieldset>
                </div>
                <div data-role="fieldcontain">
                    <fieldset data-role="controlgroup">
                        <label for="textinput12">
                            <?=$LANG['registration_enterpin']?>
                        </label>
                        <input id="textinput12"  name="pin" placeholder="" value="" type="password" />
                    </fieldset>
                </div>
                <div data-role="fieldcontain">
                    <fieldset data-role="controlgroup">
                        <label for="textinput93">
                            <?=$LANG['registration_reenterpin']?>
                        </label>
                        <input id="textinput93"  name="pin2" placeholder="" value="" type="password" />
                    </fieldset>
                </div>
                <input type="submit" name="submit" value="<?=$LANG['registration_continue']?>"  />

				
				<div align="center">
                
                    <font size="1.5" face="arial" color="grey" align="center">
					<?
				if ($_SESSION['language'] == "english")
					echo '
					English &nbsp  I  &nbsp <a href="request.php?act=appregister&language=indonesia" >Bahasa</a>
					';
				else if ($_SESSION['language'] == "indonesia")
					echo '
					<a href="request.php?act=appregister&language=english" >English</a> &nbsp  I  &nbsp Bahasa
					';
				else
					echo '
					<a href="request.php?act=appregister&language=english" >English</a> &nbsp  I  &nbsp <a href="request.php?act=appregister&language=indonesia" >Bahasa</a>
					';
					
					
					?>
                    
                    </font>
               
                </div>				
				
            </div>
			</form>
        </div>

<?
}

//---REGISTRATION PAGE STEP 2
else if ($registerStep == "register2") {

//validate Mobile No

$url = $CORE_URL."get_subscriber_by_mdn.php?mdn=".trim($_REQUEST["mobilephone"]);

 $ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, CURL_TIMEOUT);
$jsonData = curl_exec($ch);
$data = json_decode($jsonData, TRUE);

$errorStep1 = false;
if ($data['totaldata'] != 0 ) {
$errorStep1 = true;
$errorMsg = $LANG['registration_errormsg1'];

}else if ($_REQUEST["pin"] != $_REQUEST["pin2"]){

$errorStep1 = true;
$errorMsg = $LANG['registration_errormsg2'];


}else {
	$errorStep1 = false;
	$dataReg = array ("1"=>array (
	"fullname" => $_REQUEST["fullname"],
	"mobilephone" => $_REQUEST["mobilephone"],
	"pin" => $_REQUEST["pin"]
	)
	);

	$_SESSION['dataReg'] = $dataReg;

}

if (!$errorStep1) {
?>

        <div data-role="page" id="page43">
            <div data-theme="a" data-role="header" align="left">
<div>
    <a href="request.php" data-transition="slide"><img border="0" src="images/kerjalokal.gif" alt="Kerja Lokal" style="float:left; display:inline; margin-left:14px; margin-top:11px;" /></a>
</div>                

				<h3 style="margin-left:14px">
                    <?=$LANG['registration_jobmatchlocation']?>
                </h3>
            </div>
            <div data-role="content">
                <div>
                    <div align="right">
                        <?=$LANG['registration_step2']?>
                    </div>
                </div>
				<form action="request.php?act=appregister" method=post>
				<input name="step" value="register3" type="hidden" />
                <div data-role="fieldcontain">
                    <fieldset data-role="controlgroup" data-type="vertical">
                        <legend>
                            <?=$LANG['registration_defineyourjobmatach']?>
                        </legend>
                        <input name="jobmatchon" id="radio10" value="gps" type="radio" />
                        <label for="radio10">
                            <?=$LANG['registration_currentgpslocation']?>
                        </label>
                        <input name="jobmatchon" id="radio11" value="zipcode" type="radio" />
                        <label for="radio11" checked>
                            <?=$LANG['registration_zipcode']?>
                        </label>
                    </fieldset>
                </div>
                <div>
                    <?=$LANG['registration_thisinformationwill']?>
                </div>
                <input type="submit" name="submit" value="<?=$LANG['registration_continue']?>"  />
				</form>
            </div>
        </div>


<?	
}else {
?>
       <div data-role="page" id="page43">
            <div data-theme="a" data-role="header" align="left">
<div>
    <a href="request.php" data-transition="slide"><img border="0" src="images/kerjalokal.gif" alt="Kerja Lokal" style="float:left; display:inline; margin-left:14px; margin-top:11px;" /></a>
</div>                

				<h3 style="margin-left:14px">
                    <?=$LANG['registration_errorregistration']?>
                </h3>
            </div>
            <div data-role="content">
                <div>
                    <div align="right">
                        <?=$LANG['registration_step1']?>
                    </div>
                </div>
				<?=$errorMsg?>
				<a data-role="button" data-transition="fade" href="request.php?act=appregister">
                    <?=$LANG['registration_backtoregistration']?>
                </a>
            </div>
        </div>



<?

}	




//---REGISTRATION PAGE STEP 3
}else if ($registerStep == "register3") {




if ($_REQUEST['jobmatchon'] == "gps") {
?>

         <div data-role="page" id="page27">
            <div data-theme="a" data-role="header" align="left">
		<div>
			<a href="request.php" data-transition="slide"><img border="0" src="images/kerjalokal.gif" alt="Kerja Lokal" style="float:left; display:inline; margin-left:14px; margin-top:11px;" /></a>
		</div>                

				<h3 style="margin-left:14px">
                    <?=$LANG['registration_jobmatchlocation']?>
                </h3>
            </div>
            <div data-role="content">
				<form action="request.php?act=appregister" method=post name=formstep3a>
				<input name="step" value="register4" type="hidden" />
                <div>
                    <div align="right">
                        <?=$LANG['registration_step3']?>
                    </div>
                </div>
                <div data-role="fieldcontain">
                    <fieldset data-role="controlgroup">
                        <label for="textinput38">
                            <?=$LANG['registration_entergpslat']?>
                        </label>
                        <input id="textinput38" name="gpslat" placeholder="" value="" type="text" />
                    </fieldset>
                </div>
               <div data-role="fieldcontain">
                    <fieldset data-role="controlgroup">
                        <label for="textinput39">
                            <?=$LANG['registration_entergpslng']?>
                        </label>
                        <input id="textinput39" name="gpslng" placeholder="" value="" type="text" />
                    </fieldset>
                </div>				
				
				<input type="submit" name="submit" value="<?=$LANG['registration_userasjobmatch']?>"  />
				</form>

     <script type="text/javascript" charset="utf-8" src="cordova-1.5.0.js"></script>
    <script type="text/javascript" charset="utf-8">
    function getGPSLocation() {
        navigator.geolocation.getCurrentPosition(onSuccess, onError);
    }

    // onSuccess Geolocation
    //
    function onSuccess(position) {
//        var element = document.getElementById('geolocation');
        document.formstep3a.gpslat.value =  position.coords.latitude;
        document.formstep3a.gpslng.value =  position.coords.longitude;
	}
	function onError(error) {
        alert('code: '    + error.code    + '\n' +
              'message: ' + error.message + '\n');
    }
	</script>

				
                <div>
                    <?=$LANG['registration_ifyoudontknowgpslat']?>
                </div>
                <a data-role="button" data-transition="fade" href="#" onClick="getGPSLocation()">
                    <?=$LANG['registration_detectgpslocation']?>
                </a>
            </div>
        </div>


<?


}else if ($_REQUEST['jobmatchon'] == "zipcode") {


?>

         <div data-role="page" id="page27">
            <div data-theme="a" data-role="header" align="left">
<div>
    <a href="request.php" data-transition="slide"><img border="0" src="images/kerjalokal.gif" alt="Kerja Lokal" style="float:left; display:inline; margin-left:14px; margin-top:11px;" /></a>
</div>                

				<h3 style="margin-left:14px">
                    <?=$LANG['registration_jobmatchlocation']?>
                </h3>
            </div>
            <div data-role="content">
				<form action="request.php?act=appregister" method=post>
				<input name="step" value="register4" type="hidden" />
                <div>
                    <div align="right">
                        <?=$LANG['registration_step3']?>
                    </div>
                </div>
                <div data-role="fieldcontain">
                    <fieldset data-role="controlgroup">
                        <label for="textinput38">
                            <?=$LANG['registration_enterzipcode']?>
                        </label>
                        <input id="textinput38" name="zipcode" placeholder="" value="" type="text" />
                    </fieldset>
                </div>
				
				
				<input type="submit" name="submit" value="<?=$LANG['registration_userasjobmatch']?>"  />
				</form>
                
				
                <div>
                    <?=$LANG['registration_ifyoudontknowzipcode']?>
                </div>
                <a data-role="button" data-transition="fade" href="request.php?act=appregister&step=register3b">
                    <?=$LANG['registration_detectzipcode']?>
                </a>
            </div>
        </div>
 



<?

}

}else if ($registerStep == "register3b") {


?>

       <div data-role="page" id="page40">
<?

$urlProvince = $CORE_URL."get_location_by_id.php?key=parent_id&value=0&order=loc_id";
$urlAll = $CORE_URL."get_location.php";

 $ch = curl_init($urlAll);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, CURL_TIMEOUT);
$jsonData = curl_exec($ch);
$data = json_decode($jsonData, TRUE);
$allLocation = $data["results"];

$arrKelurahan = array();
$arrKecamatan = array();
$arrKotamadya = array();
$arrProvince = array();

	$kel = $kec = $kot = $kota = 0;
	$jsKel = "\nvar arrKelurahan = {};";
	$jsKec = "\nvar arrKecamatan = {};";
	$jsKot = "\nvar arrKotamadya = {};";
	$jsProv = "\nvar arrProvince = {};";

foreach ($allLocation as $loc) {

	$parent_id = $loc['parent_id'];
	switch ($loc['loc_type']) {
		case "KELURAHAN":  if (!is_array($arrKelurahan[$parent_id])) {
								$arrKelurahan[$parent_id] = array();
								$jsKel .= "arrKelurahan['".$parent_id."'] = {};\n";
							}
							$arrKelurahan[$parent_id][] = $loc; 
							$jsKel .= "arrKelurahan['".$parent_id."']['".$loc['loc_id']."'] = '".$loc['name']."';\n";
							break;
		case "KECAMATAN":  if (!is_array($arrKecamatan[$parent_id])){
								$arrKecamatan[$parent_id] = array();
								$jsKec .= "arrKecamatan['".$parent_id."'] = {};\n";
							}
							$arrKecamatan[$parent_id][] = $loc; 
							$jsKec .= "arrKecamatan['".$parent_id."']['".$loc['loc_id']."'] = '".$loc['name']."';\n";
							break;
		case "KOTAMADYA":  if (!is_array($arrKotamadya[$parent_id])){
								$arrKotamadya[$parent_id] = array();
								$jsKot .= "arrKotamadya['".$parent_id."'] = {};\n";
							}
							$arrKotamadya[$parent_id][] = $loc; 
							$jsKot .= "arrKotamadya['".$parent_id."']['".$loc['loc_id']."'] = '".$loc['name']."';\n";
							
							break;
		case "KOTA":  if (!is_array($arrProvince[$parent_id])){
								$arrProvince[$parent_id] = array();
								$jsProv .= "arrProvince['".$parent_id."'] = {};\n";
							}
							$arrProvince[$parent_id][] = $loc; 
							$jsProv .= "arrProvince['".$parent_id."']['".$loc['loc_id']."'] = '".$loc['name']."';\n";
							
							break;
	
	}


}



$urlKotamadya = "http://180.243.231.8:8085/infokerja_core/get_location_by_id.php?key=loc_type&value=KOTAMADYA&order=loc_id";





echo '
<script>

function populateOptions(th) {
'.$jsKel.$jsKec.$jsKot.$jsProv.'
  

  if (th) {
		var val = th.options[th.selectedIndex].value;
		if (th.name == "province") {
		
			//alert("province code" + th.options[th.selectedIndex].value);
			document.formstep3.regency.options.length=0;
			var arrK = arrKotamadya[val];
			var i=0;
			for (var key in arrK) {
				
				document.formstep3.regency.options[i]=new Option(arrK[key], key, false, false);
				i++;
			}
		}else 		if (th.name == "regency") {
			//alert("regency code" + th.options[th.selectedIndex].value);
			document.formstep3.district.options.length=0;
			var arrK = arrKecamatan[val];
			var i=0;
			for (var key in arrK) {
				
				document.formstep3.district.options[i]=new Option(arrK[key], key, false, false);
				i++;
			}


			}else 		if (th.name == "district") {
			//alert("district code" + th.options[th.selectedIndex].value);
			document.formstep3.subdistrict.options.length=0;
			var arrK = arrKelurahan[val];
			var i=0;
			for (var key in arrK) {
				
				document.formstep3.subdistrict.options[i]=new Option(arrK[key], key, false, false);
				i++;
			}			
			
			
		}


	}
}


</script>
';




?>
	   
	   
            <div data-theme="a" data-role="header" align="left">
<div>
    <a href="request.php" data-transition="slide"><img border="0" src="images/kerjalokal.gif" alt="Kerja Lokal" style="float:left; display:inline; margin-left:14px; margin-top:11px;" /></a>
</div>                

				<h3 style="margin-left:14px">
                    <?=$LANG['registration_jobmatchlocation']?>
                </h3>
            </div>
            <div data-role="content">
			<form action="request.php?act=appregister" method=post name='formstep3'>
			<input name="step" value="register4" type="hidden" />
                <div>
                    <div align="right">
                        <?=$LANG['registration_step3']?>
                    </div>
                </div>
                <div>
                    <br />
                    <?=$LANG['registration_todeterminezipcode']?>
                </div>
                <div data-role="fieldcontain">
                    <label for="selectmenu15">
                        <?=$LANG['registration_province']?>
                    </label>
                    <select name="province" id="selectmenu15" onChange="populateOptions(this)">
                        <option value="option1">
                            <?=$LANG['registration_selectprovince']?>
                        </option>
					<?
						foreach ($arrProvince[0] as $prov)
						echo '
                       <option value="'.$prov['loc_id'].'">
                            '.$prov['name'].'
                        </option>						
						
						';
					
					?>
 
                    </select>
                </div>
                <div data-role="fieldcontain">
                    <label for="selectmenu16">
                        <?=$LANG['registration_regency']?>
                    </label>
                    <select name="regency" id="selectmenu16" onChange="populateOptions(this)">
                        <option value="option1">
                            <?=$LANG['registration_selectregency']?>
                        </option>
                    </select>
                </div>
                <div data-role="fieldcontain">
                    <label for="selectmenu17">
                        <?=$LANG['registration_district']?>
                    </label>
                    <select name="district" id="selectmenu17" onChange="populateOptions(this)">
                        <option value="option1">
                            <?=$LANG['registration_selectdistrict']?>
                        </option>
                    </select>
                </div>
                <div data-role="fieldcontain">
                    <label for="selectmenu22">
                        <?=$LANG['registration_subdistrict']?>
                    </label>
                    <select name="subdistrict" id="selectmenu22">
                        <option value="option1">
                            <?=$LANG['registration_selectsubdistrict']?>
                        </option>
                    </select>
                </div>
 
                <input type="submit" name="submit" value="<?=$LANG['registration_userasjobmatch']?>"  />
				</form>
            </div>
        </div>


<?




//---REGISTRATION PAGE STEP 4
}else if ($registerStep == "register4") {


$dataReg = $_SESSION['dataReg'];

if (!empty($_REQUEST["gpslat"]) && !empty($_REQUEST["gpslng"])) {

$urlsearch = $CORE_URL."get_location_by_nearest_gps.php?gps_lat=".urlencode(trim($_REQUEST["gpslat"]))."&gps_lng=".urlencode(trim($_REQUEST["gpslng"]));


}else if (!empty($_REQUEST["subdistrict"])) {
				//search location by kelurahan
$urlsearch = $CORE_URL."get_location_by_id.php?key=loc_id&value=".trim($_REQUEST["subdistrict"])."&order=loc_id&";


}else if (!empty($_REQUEST["zipcode"])) {
				//search location by zipcode
$urlsearch = $CORE_URL."get_location_by_id.php?key=zipcode&value=".trim($_REQUEST["zipcode"])."&order=loc_id";
					
}	
					
//echo $urlsearch;					
					$ch = curl_init(trim($urlsearch));
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, CURL_TIMEOUT);
					$jsonData = curl_exec($ch);
					$data = json_decode($jsonData, TRUE);
					
					
					//curl_close($ch);
					if ($data["nrows"] != 0) {
					
					
					
					$location = $data["results"][0];
					//$dataexist = ($data["nrows"]==0)?false:true;



					
					$dataReg2 = array();
					$dataReg2["zipcode"] =  $location["zipcode"];
					$dataReg2["pos_lng"] = $location['loc_lng'];
					$dataReg2["pos_lat"] = $location['loc_lat'];
					$dataReg2["subdistrict_code"] = $location['loc_id'];
					
					
					$dataKelurahan = $location['name'];
					
					$nrows = $data["nrows"];
					while (($nrows = $data["nrows"]) != 0) {
							$loc_id = $location['parent_id'];
							$url = $CORE_URL."get_location_by_id.php?key=loc_id&value=".$loc_id."&order=loc_id";

							$ch = curl_init($url);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, CURL_TIMEOUT);
							$jsonData = curl_exec($ch);
							$data = json_decode($jsonData, TRUE);
							$location = $data["results"][0];
							
							switch ($location['loc_type']) {
								case "KELURAHAN":  $dataKelurahan = $location['name'];
													break;
								case "KECAMATAN":  $dataKecamatan = $location['name'];
													break;
								case "KOTAMADYA":  $dataKotamadya = $location['name'];
													break;
								case "KOTA":  $dataProvince = $location['name'];
													break;
							
							}
							//curl_close($ch);

									
					}
					
					$dataReg2["province"] = $dataProvince;
					$dataReg2["regency"] = $dataKotamadya;
					$dataReg2["district"] = $dataKecamatan;
					$dataReg2["subdistrict"] = $dataKelurahan;
					
					$dataReg["2"] = $dataReg2;


					}else {
						$errorMsg = "<div align=center><font color='red'>".$LANG['registration_errormsg3']."<br><br></font></div>";

					
					}



/*
$dataReg = $_SESSION['dataReg'];
	$dataReg["2"] = array (
	"zipcode" => $_REQUEST["zipcode"],
	"pos_lng" => $_REQUEST["gpslng"],
	"pos_lat" => $_REQUEST["gpslat"],
	"province" => $_REQUEST["province"],
	"regency" => $_REQUEST["regency"],
	"district" => $_REQUEST["district"],
	"subdistrict" => $_REQUEST["subdistrict"]
	);
*/
	$_SESSION['dataReg'] = $dataReg;



?>


        <div data-role="page" id="page44">
            <div data-theme="a" data-role="header" align="left">
<div>
    <a href="request.php" data-transition="slide"><img border="0" src="images/kerjalokal.gif" alt="Kerja Lokal" style="float:left; display:inline; margin-left:14px; margin-top:11px;" /></a>
</div>                

				<h3 style="margin-left:14px">
                    <?=$LANG['registration_title']?>
                </h3>
            </div>
            <div data-role="content">
			<form action="request.php?act=appregister" method=post>
			<input name="step" value="register5" type="hidden" />
                <div>
                    <div align="right">
                        <?=$LANG['registration_step4']?>
                    </div>
                </div>
				
                <div data-role="fieldcontain">
                    <fieldset data-role="controlgroup">
                        <label for="textinput74">
						
                            <?=$LANG['registration_fullname']?>
                        </label>
                        <input id="textinput74" placeholder="" value="<?=$dataReg['1']['fullname']?>" type="text" readonly />
                    </fieldset>
                </div>
                <div data-role="fieldcontain">
                    <fieldset data-role="controlgroup">
                        <label for="textinput74">
                           <?=$LANG['registration_mobilephone']?>
                        </label>
                        <input id="textinput74" placeholder="" value="<?=$dataReg['1']['mobilephone']?>" type="text" readonly />
                    </fieldset>
                </div>
                <div data-role="fieldcontain">
                    <fieldset data-role="controlgroup">
                        <label for="textinput75">
                            PIN 
                        </label>
                        <input id="textinput75" placeholder="" value="<?=$dataReg['1']['pin']?>" type="text" readonly />
                    </fieldset>
                </div>
                <div data-role="fieldcontain">
                    <fieldset data-role="controlgroup">
                        <label for="textarea2">
                            <?=$LANG['registration_jmladdress']?>
                        </label>
                        <textarea id="textarea2" placeholder="" readonly><?=$dataReg['2']['subdistrict']."\n".$dataReg['2']['district']."\n".$dataReg['2']['regency']."\n".$dataReg['2']['province']?>
                        </textarea>
                    </fieldset>
                </div>
               <div data-role="fieldcontain">
                    <fieldset data-role="controlgroup">
                        <label for="textinput76">
                            <?=$LANG['registration_jmlgps']?>
                        </label>
						<input id="textinput76" name="gps" placeholder="" value="<?=$dataReg['2']['pos_lat'].";".$dataReg['2']['pos_lng']?>" type="text" readonly />
                    </fieldset>
                </div>				
               <div data-role="fieldcontain">
                    <fieldset data-role="controlgroup">
                        <label for="textinput77">
                            <?=$LANG['registration_jmlzipcode']?>
                        </label>
						<input id="textinput77" placeholder="" value="<?=$dataReg['2']['zipcode']?>" type="text" readonly />

 
                    </fieldset>
                </div>
				<?=$errorMsg?>
				<?=$LANG['registration_ifyouarenotsure']?>
                <a data-role="button" data-transition="fade" href="request.php?act=appregister&act2=redo" data-icon="arrow-r" data-iconpos="right">
                    <?=$LANG['registration_changeyourdata']?>
                </a>
				<? if (empty($errorMsg)) {?>
				<?=$LANG['registration_alldataiscorrect']?>
                <input type="submit" name="submit" value="<?=$LANG['registration_saveyourregistration']?>"  />
				<? }?>
				</form>
            </div>
        </div>




<?



//---REGISTRATION PAGE STEP 5 (save to database)

}else if ($registerStep == "register5") {

/* Call the API to save the registeration data into database.  */


$dataReg = $_SESSION['dataReg'];

	
$urlStr = "";

$urlStr .="name=".urlencode($dataReg['1']['fullname']);	
$urlStr .="&mdn=".urlencode($dataReg['1']['mobilephone']);	
$urlStr .="&pinweb=".urlencode($dataReg['1']['pin']);	
$urlStr .="&loc_id=".urlencode($dataReg['2']['subdistrict_code']);	
$urlStr .="&pos_lng=".urlencode($dataReg['2']['pos_lng']);	
$urlStr .="&pos_lat=".urlencode($dataReg['2']['pos_lat']);	

$urlStr .="&date_add=".urlencode(date("Y-m-d H:i:s"));	
$urlStr .="&date_update=".urlencode(date("Y-m-d H:i:s"));	
$urlStr .="&address1=".urlencode($dataReg['2']['subdistrict'].",".$dataReg['2']['district'].",".$dataReg['2']['regency'].",".$dataReg['2']['province']);	
	


$urlRegister = $CORE_URL."add_subscriber.php?".$urlStr;

$ch = curl_init($urlRegister);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, CURL_TIMEOUT);
$jsonData = curl_exec($ch);
$data = json_decode($jsonData, TRUE);
//$allLocation = $data["results"];
if (!empty($data['subscriber_id']))
$content = $LANG['registration_registrationissuccess'];
else
$content = $LANG['registration_errormsg4'];

//print_r ($data);


?>

        <div data-role="page" id="page44">
            <div data-theme="a" data-role="header" align="left">
<div>
    <a href="request.php" data-transition="slide"><img border="0" src="images/kerjalokal.gif" alt="Kerja Lokal" style="float:left; display:inline; margin-left:14px; margin-top:11px;" /></a>
</div>                

				<h3 style="margin-left:14px">
                    <?=$LANG['registration_title']?>
                </h3>
            </div>
            <div data-role="content">

                <div>
                    <div align="right">
                        <?=$LANG['registration_step4']?>
                    </div>
                </div>
                <?=$content?>
                <a data-role="button" data-transition="fade" href="request.php" data-icon="arrow-r" data-iconpos="right">
                    <?=$LANG['registration_backhome']?>
                </a>
  
                
            </div>
        </div>


<?
}




?>



