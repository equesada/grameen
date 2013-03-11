  <?
  
/*
This code is owned by Grameen Applab Indonesia

Copying and re-using the code is prohibited without permission from  Grameen Applab Indonesia

-April 2012-
-Ramot Lubis-

*/

  
require ("conf.php");  
  $ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, CURL_TIMEOUT);
$jsonData = curl_exec($ch);
$data = json_decode($jsonData, TRUE);

$job_cat_title = $_REQUEST['job_cat_title'];

$strShow = "";

$arrColor = array("red", "blue", "green", "yellow", "magenta");

		$counter = 0;
		
		$mapCenter="Jakarta, Indonesia";
		foreach($data["results"] as $jobinfo)
		{
			$counter++;
	        $job_id = $jobinfo['job_id'];        
	        $jobcat_id = $jobinfo['jobcat_id'];
			$loc_id = $jobinfo['loc_id'];  

			if (strlen($jobinfo['comp_name']) > 12) {
				$companyName = substr($jobinfo['comp_name'], 0, 12)."...";
			}else
				$companyName = $jobinfo['comp_name'];
			
			
	        $job_msg = $jobinfo['title']." (".$companyName.")";
	        $log_id = $jobinfo['log_id'];

	        $tempId = (111);	

			if ($_REQUEST['map'] != 1) {
				$strShow .= '
						<li data-theme="c">
							<a href="request.php?act=jobdetails&job_cat_id='.$jobcat_id.'&job_id='.$job_id.'&log_id='.$log_id.'" data-transition="slide">
								'.$counter.'. '.$job_msg.'
							</a>
						</li>			
				
				';   
			}else {
						//this should be replaced with Web service call API. Not directly connect to DB
						//temporary solution though!
						//this database is in Application server (might not be in production db server)
						$query = "select loc_lat as lat, loc_lng as lng from location where loc_id = '".$loc_id."'";
						//echo $query."\n";
						$result = mysql_query($query);
						$row = mysql_fetch_assoc($result);

						
						//$marker = "";
						
						if($row['lat']) {
							$idxArrColor = $counter % 5;
							$center = trim($row['lat']).",".trim($row['lng']);
							if ($counter==1)
								$mapCenter = $center;
							$strShow .= "markers=color:".$arrColor[$idxArrColor]."%7Clabel:".$counter."%7C".$center."&";
						}
		
			
				//$strShow .= 'markers=color:blue%7Clabel:S%7C40.702147,-74.015794&';
						
				
				  

			}			
			++$idAdder;		                	
		}
 

 


curl_close($ch);

//die((string)$strShow);
//$strShow = trim($strShow);
 
if ($_REQUEST['map'] == 1 && !empty($strShow))
	$strShow = "<img src='http://maps.googleapis.com/maps/api/staticmap?center=".$mapCenter."&".$strShow."size=400x400&zoom=10&sensor=false'>";
 
if (strlen($strShow) == 0)
	$strShow = "<br><div align=center width=80%>Currently there is no Job Info for you in this category ".$job_cat_title."</div>";
 
  ?>
  <div data-role="page" id="page3">
            <div data-theme="a" data-role="header" align="left">
<div>
    <a href="request.php" data-transition="slide"><img border="0" src="images/kerjalokal.gif" alt="Kerja Lokal" style="float:left; display:inline; margin-left:14px; margin-top:11px;" /></a>
</div>                

				<h3 style="margin-left:14px">
                    <?=$LANG['joblist_myjobinfo']?>
                </h3>
            </div>
            <div data-role="content">
                <ul data-role="listview" data-divider-theme="b" data-inset="false">
                    <li data-role="list-divider" role="heading" >
					<?
					
					if ($_REQUEST['map'] == 1) {
						$strHeader = '                        <a data-transition="slide" href="request.php?act=joblist&job_cat_id='.$jobcat_id.'&job_cat_title='.trim($job_cat_title).'">'.trim($job_cat_title).' ('.$LANG['joblist_clicktojoblist'].')</a>';
					}else {
						$strHeader = '                        <a data-transition="slide" href="request.php?act=joblist&map=1&job_cat_id='.$jobcat_id.'&job_cat_title='.trim($job_cat_title).'">'.trim($job_cat_title).' ('.$LANG['joblist_clicktojobmap'].')</a>';
					
					}
					echo $strHeader;
					?>
					
					
                    </li>
<?=$strShow
?>
                </ul>
            </div>
        </div>