<?
/*
This code is owned by Grameen Applab Indonesia

Copying and re-using the code is prohibited without permission from  Grameen Applab Indonesia

-May 2012-
-Ramot Lubis-



The code here is similar to content_register.php, expect that the final action here is to update user profile
*/


error_reporting(E_ERROR | E_WARNING | E_PARSE);
$CORE_URL = "http://180.243.231.8:8085/infokerja_core/";

session_start();


$registerStep = $_REQUEST['step'];

if ($registerStep == "register2") {

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
				<form action="request.php?act=updateprofile" method=post>
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
				<form action="request.php?act=updateprofile" method=post name=formstep3a>
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
				<form action="request.php?act=updateprofile" method=post>
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
                <a data-role="button" data-transition="fade" href="request.php?act=updateprofile&step=register3b">
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
			<form action="request.php?act=updateprofile" method=post name='formstep3'>
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




	$_SESSION['dataReg'] = $dataReg;



/* Call the API to save the registeration data into database.  */


$dataReg = $_SESSION['dataReg'];

	
$urlStr = "";

$urlStr .="subscriber_id=".urlencode($_SESSION['subscriber_id']);	
$urlStr .="&loc_id=".urlencode($dataReg['2']['subdistrict_code']);	
$urlStr .="&pos_lng=".urlencode($dataReg['2']['pos_lng']);	
$urlStr .="&pos_lat=".urlencode($dataReg['2']['pos_lat']);	

$urlStr .="&date_update=".urlencode(date("Y-m-d H:i:s"));	
$urlStr .="&address1=".urlencode($dataReg['2']['subdistrict'].",".$dataReg['2']['district'].",".$dataReg['2']['regency'].",".$dataReg['2']['province']);	
	


$urlRegister = $CORE_URL."update_subscriber.php?".$urlStr;

$ch = curl_init($urlRegister);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, CURL_TIMEOUT);
$jsonData = curl_exec($ch);
$data = json_decode($jsonData, TRUE);
//$allLocation = $data["results"];
if ($data['status'] == "1")
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