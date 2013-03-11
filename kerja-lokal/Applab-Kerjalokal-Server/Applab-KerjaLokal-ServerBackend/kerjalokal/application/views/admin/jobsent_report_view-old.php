
<?php 	

	$card_type = '';
	for ($i=1; $i <= $this->uri->total_segments(); $i++) {
		//if ($this->uri->segment($i) == 'null' && in_array($this->uri->segment($i+1), array('AS', 'KH', 'SP')))
		$card_type = $this->uri->segment($i+1);
	}
	//echo "<pre>"; print_r($search); echo "</pre>";
	$filter = "";
	foreach($search as $key => $val) { 
		$filter .= (empty($val)) ? '_/' : $val ."/";
	}
	//echo "<pre>"; print_r($filter); echo "</pre>";
?>
<script type="text/javascript">
function result_row_default(a) {
	
	var urlpath = '<?php echo base_url(); ?>admin/jobsent_report/manage/<?php echo $page = 1; ?>/<?php echo $order; ?>/'+a+'/<?php echo $filter; ?>';
	$(location).attr('href', urlpath);	
}
</script>
<div class="divClearBoth">
	<?php echo $search_form; ?>
	<!--a href="<?php echo $search_link; ?>" id="search_form" rel="simpleDialog">Search</a-->
</div>
<?
echo "<br><br><br>Export to &nbsp;&nbsp;<a href=\"".base_url()."admin/jobsent_report/save_csv/$order/$filter\">CSV</a>";
?>
<div style="display:table; width:100%; clear:both">
<div class="row">
<?php
	echo form_open($form_submit);
?>
</div>
</div>
<?php 
if(!empty($nopage)):
echo "<ul id=\"pagination-flickr\">";
if ($nopage > 1) echo "<li class=\"previous\"><a href=\"".base_url()."admin/jobsent_report/manage/".($nopage - 1)."/$order/$default_row/$filter\">&lt;&lt; Previous</a></li>";
$showPage="";
for($page = 1; $page <= $npage; $page++)
{
	if ((($page >= $nopage - 3) && ($page <= $nopage + 3)) || ($page == 1) || ($page == $npage))
	{
		if (($showPage == 1) && ($page != 2))  echo "<li class=\"active2\">...</li>";
		if (($showPage != ($npage - 1)) && ($page == $npage))   echo "<li class=\"active2\">...</li>";
		if ($page == $nopage)
			echo "<li class=\"active\">".$page."</li>";
		else 
			echo "<li><a href=\"".base_url()."admin/jobsent_report/manage/$page/$order/$default_row/$filter\">$page</a></li>";
		$showPage = $page;
	}
}
if ($nopage < $npage) echo "<li class=\"next\"><a href=\"".base_url()."admin/jobsent_report/manage/".($nopage + 1)."/$order/$default_row/$filter\">Next &gt;&gt;</a></li>";
echo "</ul>";
endif;
?>
<div>Result/Pages 
<?php 
	$js = 'id="result_row" onChange="result_row_default(this.value);"';
	echo form_dropdown('result_row',$result_row, $default_row, $js);
	echo form_close();
	$page = 1;
?>
</div>


<?php	
	//include_once("jobsent_report_search.php");
		
	echo "<table class=\"table_style\">";
	echo "<tr>";
	echo "<td class=\"table_head\" rowspan=\"2\">No</td>";
	echo "<td class=\"table_head\" rowspan=\"2\"><a href=\"".base_url()."admin/jobsent_report/manage/$page/$next_order"."_jobsend_id/$default_row/$filter\">Send ID</a></td>";
	echo "<td class=\"table_head\" rowspan=\"2\"><a href=\"".base_url()."admin/jobsent_report/manage/$page/$next_order"."_date_send1/$default_row/$filter\">Send Date</a></td>";
	echo "<td class=\"table_head\" rowspan=\"2\"><a href=\"".base_url()."admin/jobsent_report/manage/$page/$next_order"."_sent_time1/$default_row/$filter\">Send Time</a></td>";
	echo "<td class=\"table_head\" colspan=\"7\">Subscription & Job Seeker Info</td>";
	echo "<td class=\"table_head\" colspan=\"5\">Job Info</td>";
	echo "<td class=\"table_head\" rowspan=\"2\"><a href=\"".base_url()."admin/jobsent_report/manage/$page/$next_order"."_dis/$default_row/$filter\">Distance (KM)</a></td>";
	echo "<td class=\"table_head\" colspan=\"3\">SMSC</td>";
	echo "<td class=\"table_head\" rowspan=\"2\">Manage</td>";
	echo "</tr><tr>";
	echo "<td class=\"table_head\"><a href=\"".base_url()."admin/jobsent_report/manage/$page/$next_order"."_rel_id/$default_row/$filter\">Subscription ID</a></td>";
	echo "<td class=\"table_head\"><a href=\"".base_url()."admin/jobsent_report/manage/$page/$next_order"."_jobcat_key/$default_row/$filter\">Subscription Category</a></td>";
	echo "<td class=\"table_head\">Subscription Type</td>";
	echo "<td class=\"table_head\"><a href=\"".base_url()."admin/jobsent_report/manage/$page/$next_order"."_subscriber_id/$default_row/$filter\">Job Seeker ID</a></td>";
	echo "<td class=\"table_head\"><a href=\"".base_url()."admin/jobsent_report/manage/$page/$next_order"."_mdn/$default_row/$filter\">Job Seeker MDN</a></td>";
	echo "<td class=\"table_head\"><a href=\"".base_url()."admin/jobsent_report/manage/$page/$next_order"."_jobseeker_name/$default_row/$filter\">Job Seeker Name</a></td>";
	echo "<td class=\"table_head\"><a href=\"".base_url()."admin/jobsent_report/manage/$page/$next_order"."_loc_title/$default_row/$filter\">Job Seeker Kotamadya</a></td>";
	echo "<td class=\"table_head\" width=\"100%\"><a href=\"".base_url()."admin/jobsent_report/manage/$page/$next_order"."_job_id/$default_row/$filter\">Job ID</a></td>";
	echo "<td class=\"table_head\"><a href=\"".base_url()."admin/jobsent_report/manage/$page/$next_order"."_title/$default_row/$filter\">Job Title</a></td>";
	echo "<td class=\"table_head\"><a href=\"".base_url()."admin/jobsent_report/manage/$page/$next_order"."_jobcat_key/$default_row/$filter\">Job Category</a></td>";
	echo "<td class=\"table_head\"><a href=\"".base_url()."admin/jobsent_report/manage/$page/$next_order"."_jobkodya/$default_row/$filter\">Job Kotamadya</a></td>";
	echo "<td class=\"table_head\"><a href=\"".base_url()."admin/jobsent_report/manage/$page/$next_order"."_company_name/$default_row/$filter\">Company</a></td>";
	echo "<td class=\"table_head\"><a href=\"".base_url()."admin/jobsent_report/manage/$page/$next_order"."_status/$default_row/$filter\">Status</a></td>";
	echo "<td class=\"table_head\"><a href=\"".base_url()."admin/jobsent_report/manage/$page/$next_order"."_date_send2/$default_row/$filter\">Sent Date</a></td>";
	echo "<td class=\"table_head\"><a href=\"".base_url()."admin/jobsent_report/manage/$page/$next_order"."_sent_time2/$default_row/$filter\">Sent Time</a></td>";
	echo "</tr>";
	
	
	foreach ($results as $jobcat)
	{
	  if (!empty($jobcat["jobsend_id"])) {
		//echo "<div class=\"row\">";
		echo "<tr>";
		echo "<td class=\"table_cell\">".++$offset."</td>";
		echo "<td class=\"table_cell\">".$jobcat["jobsend_id"]."</td>";
		echo "<td class=\"table_cell\">".str_replace("-","/",$jobcat["date_send1"])."</td>";
		echo "<td class=\"table_cell\">".$jobcat["sent_time1"]."</td>";
		echo "<td class=\"table_cell\">".$jobcat["rel_id"]."</td>";
		echo "<td class=\"table_cell\">".$jobcat["jobcat_key"]."</td>";
		$subtype=($jobcat["mentor_id"]=='0') ? "APP" : "SMS"; //$jobcat["mentor_id"].
		echo "<td class=\"table_cell\">".$subtype."</td>";
		echo "<td class=\"table_cell\">".$jobcat["subscriber_id"]."</td>";
		echo "<td class=\"table_cell\">".$jobcat["mdn"]."</td>";
		echo "<td class=\"table_cell\">".$jobcat["jobseeker_name"]."</td>";
		//echo "<td class=\"table_cell\">".$jobcat["loc_title"]."</td>";
		//echo "<td class=\"table_cell\">".str_replace('.',', ',$jobcat["loc_title"])."</td>\n";
		echo "<td class=\"table_cell\">".str_replace('.',', ',$jobcat["seeker_kodya"])."</td>\n";
		//echo "<td class=\"table_cell\">".$jobcat["description"]."</td>";
		echo "<td class=\"table_cell\">".$jobcat["job_id"]."</td>";
		echo "<td class=\"table_cell\">".$jobcat["title"]."</td>";		
		echo "<td class=\"table_cell\">".$jobcat["jobcat_key"]."</td>";
		echo "<td class=\"table_cell\">".str_replace('.',', ',$jobcat["job_kodya"])."</td>\n";		
		echo "<td class=\"table_cell\">".$jobcat["company_name"]."</td>";
		echo "<td class=\"table_cell\">".$jobcat["dis"]."</td>";
		if($jobcat["status"]==1) {
			$status =  "Success";
		} else {
			$status = "DMS";
		}
		echo "<td class=\"table_cell\">".$status."</td>";
		echo "<td class=\"table_cell\">".$jobcat["date_send2"]."</td>";
		echo "<td class=\"table_cell\">".$jobcat["sent_time2"]."</td>";
		echo "<td class=\"table_cell\"><span style=\"cursor:pointer; color:#00f;\" onclick=\"manage_jobsent(".$jobcat["rel_id"].");\">Resend</span></td>";				
		echo "</tr>";
	  }
	}
	echo "</table><br />"; // CLOSE TABLE
		
		echo "<ul id=\"pagination-flickr\">";
		if ($nopage > 1) echo "<li class=\"previous\"><a href=\"".base_url()."admin/jobsent_report/manage/".($nopage - 1)."/$order/$default_row/$filter\">&lt;&lt; Previous</a></li>";	
		$showPage="";
		for($page = 1; $page <= $npage; $page++)
		{
			if ((($page >= $nopage - 3) && ($page <= $nopage + 3)) || ($page == 1) || ($page == $npage))
			{
				if (($showPage == 1) && ($page != 2))  echo "<li class=\"active2\">...</li>";
				if (($showPage != ($npage - 1)) && ($page == $npage))   echo "<li class=\"active2\">...</li>";
				if ($page == $nopage)
					echo "<li class=\"active\">".$page."</li>";
				else 
					echo "<li><a href=\"".base_url()."admin/jobsent_report/manage/$page/$order/$default_row/$filter\">$page</a></li>";
				$showPage = $page;
			}
		}
		if ($nopage < $npage) echo "<li class=\"next\"><a href=\"".base_url()."admin/jobsent_report/manage/".($nopage + 1)."/$order/$default_row/$filter\">Next &gt;&gt;</a></li>";
		echo "</ul>";
	
		
		
?>
