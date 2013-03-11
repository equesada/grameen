  <?
/*
This code is owned by Grameen Applab Indonesia

Copying and re-using the code is prohibited without permission from  Grameen Applab Indonesia

-April 2012-
-Ramot Lubis-

*/
  
  $ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, CURL_TIMEOUT);
$jsonData = curl_exec($ch);
$data = json_decode($jsonData, TRUE);

$job_cat_title = $_REQUEST['job_cat_title'];
//print_r ($data);
//$strShow = "";

$strShow =  $data['msg'];
if (empty($strShow))
$strShow =  $data['comp_name']." ".$data['title']."<br>".$data['description']."<br>Lokasi: ".$data['kecamatan_name']." ".$data['kotamadya_name']." ".$data['province_name']."<br>Hubungi: ".$data['comp_cp']." ".$data['comp_email']." ".$data['comp_tel'];
	


$job_id =  $data['job_id'];
$loc_id = $data['loc_id']; 



curl_close($ch);

//die((string)$strShow);

  
  ?>
 



		        <div data-role="page" id="page18">
            <div data-theme="a" data-role="header" align="left">
<div>
    <a href="request.php" data-transition="slide"><img border="0" src="images/kerjalokal.gif" alt="Kerja Lokal" style="float:left; display:inline; margin-left:14px; margin-top:11px;" /></a>
</div>                

				<h3 style="margin-left:14px">
                    <?=$LANG['jobdetails_jobdetails']?>
                </h3>
            </div>
            <div data-role="content">
              <?=$strShow
?>
                    
                </p>
			<div style=" text-align:center"><?=$LANG['jobcategory_joblocationmap']?></div>
    <img style=" text-align:center" id="jobmap" />

        </div>



<?

require ("conf.php");
 
 

$url3 = $CORE_URL."get_location_by_id.php?key=loc_id&value=".$loc_id."&order=loc_id";
//echo "URL: ".$url3;

  $ch = curl_init($url3);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, CURL_TIMEOUT);
$jsonData = curl_exec($ch);
$data = json_decode($jsonData, TRUE);
$row = $data["results"][0];
//print_r($row);
		$center = "Jakarta, Indonesia";
		$marker = "";
		if($row['loc_lat']) {
			$center = $row['loc_lat'].",".$row['loc_lng'];
			$marker = "&markers=color:blue%7Clabel:J%7C".$center;
		}
		
?>
<script>
$(document).ready(function(){

 document.getElementById('jobmap').src="http://maps.googleapis.com/maps/api/staticmap?center=<?=$center.$marker?>&size=400x400&zoom=13&sensor=false";

});

</script>

