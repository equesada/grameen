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
$strShow = "";
$CORE_URL = "http://180.243.231.8:8085/infokerja_core/";
		foreach($data["results"] as $jobcat)
		{
			//if ($jobcat["STATUS"] == 1) {
				$totalJobs = 0;
				$jobcat_id = $jobcat['jobcat_id'];        
				$jobcat_title = $jobcat['jobcat_title'];
				$tempId = (111);		
				
				//$url2 = $CORE_URL."get_last_10_njobs.php?subscriber_id=".$_SESSION['subscriber_id']."&jobcat_id=".$jobcat_id;
				$url2 = $CORE_URL."get_last_10_njobs.php";
				//echo $url2;
				$ch2 = curl_init($url2);
				curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch2, CURLOPT_CONNECTTIMEOUT, CURL_TIMEOUT);
				$jsonData2 = curl_exec($ch2);
				$data2 = json_decode($jsonData2, TRUE);
				$totalJobs = $data2[$jobcat_id]["n_qualified_job"];
				curl_close($ch2);

				
				$strShow .= '
						<li data-theme="c">
							<a href="request.php?act=joblist&job_cat_id='.$jobcat_id.'&job_cat_title='.$jobcat_title.'" data-transition="slide">
								'.$jobcat_title.' ('.$totalJobs.' '.$LANG['jobcategory_jobs'].') 
							</a>
						</li>			
				
				';           
				++$idAdder;	
						
		}
 



curl_close($ch);

//die((string)$strShow);

  
  ?>
  <div data-role="page" id="page3">
            <div data-theme="a" data-role="header" align="left">
<div>
    <a href="request.php" data-transition="slide"><img border="0" src="images/kerjalokal.gif" alt="Kerja Lokal" style="float:left; display:inline; margin-left:14px; margin-top:11px;" /></a>
</div>                

				<h3 style="margin-left:14px">
                     <?=$LANG['jobcategory_myjobinfo']?>
                </h3>
            </div>
            <div data-role="content">
                <ul data-role="listview" data-divider-theme="b" data-inset="false">
                    <li data-role="list-divider" role="heading">
                         <?=$LANG['jobcategory_jobcategories']?>
                    </li>
<?=$strShow
?>
                </ul>
            </div>
        </div>