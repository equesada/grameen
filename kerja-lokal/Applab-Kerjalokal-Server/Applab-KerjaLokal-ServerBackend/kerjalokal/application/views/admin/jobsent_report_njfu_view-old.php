<?php	
	//include_once("jobsent_report_njfu_search.php");
	
	
	
	
	echo "<div style=\"display:table;\">";
	echo "<div class=\"row\">";
	echo "<div class=\"cell table_head\">No</div>";
	echo "<div class=\"cell table_head\"><a href=\"".base_url()."admin/jobsent_report_njfu/manage/$page/$next_order"."_jobsend_id/\">Send ID</a></div>";
	echo "<div class=\"cell table_head\"><a href=\"".base_url()."admin/jobsent_report_njfu/manage/$page/$next_order"."_date_send1/\">Send Date</a></div>";
	echo "<div class=\"cell table_head\"><a href=\"".base_url()."admin/jobsent_report_njfu/manage/$page/$next_order"."_sent_time1/\">Send Time</a></div>";
	//echo "<div class=\"cell table_head\">Subscription ID</div>";
	echo "<div class=\"cell table_head\"><a href=\"".base_url()."admin/jobsent_report_njfu/manage/$page/$next_order"."_rel_id/\">Subscription ID</a></div>";
	echo "<div class=\"cell table_head\"><a href=\"".base_url()."admin/jobsent_report_njfu/manage/$page/$next_order"."_jobcat_key/\">Subscription Category</a></div>";
	echo "<div class=\"cell table_head\">Subscription Type</div>";
	echo "<div class=\"cell table_head\"><a href=\"".base_url()."admin/jobsent_report_njfu/manage/$page/$next_order"."_subscriber_id/\">Job Seeker ID</a></div>";
	echo "<div class=\"cell table_head\"><a href=\"".base_url()."admin/jobsent_report_njfu/manage/$page/$next_order"."_mdn/\">Job Seeker MDN</a></div>";
	echo "<div class=\"cell table_head\"><a href=\"".base_url()."admin/jobsent_report_njfu/manage/$page/$next_order"."_jobseeker_name/\">Job Seeker Name</a></div>";
	echo "<div class=\"cell table_head\"><a href=\"".base_url()."admin/jobsent_report_njfu/manage/$page/$next_order"."_jobseeker_distance/\">Job Seeker Kota-madya</a></div>";
	echo "<div class=\"cell table_head\" width=\"100%\">Job-Title</div>";
	echo "<div class=\"cell table_head\"><a href=\"".base_url()."admin/jobsent_report_njfu/manage/$page/$next_order"."_title/\">Job Title</a></div>";
	echo "<div class=\"cell table_head\"><a href=\"".base_url()."admin/jobsent_report_njfu/manage/$page/$next_order"."_jobcat_key/\">Job Category</a></div>";
	echo "<div class=\"cell table_head\"><a href=\"".base_url()."admin/jobsent_report_njfu/manage/$page/$next_order"."_job_kotamadya/\">Distance</a></div>";
	//echo "<div class=\"cell table_head\"><a href=\"".base_url()."admin/jobsent_report_njfu/manage/$page/$next_order"."_company_name/\">Company</a></div>";
	echo "<div class=\"cell table_head\"><a href=\"".base_url()."admin/jobsent_report_njfu/manage/$page/$next_order"."_status/\">Status</a></div>";
	echo "<div class=\"cell table_head\"><a href=\"".base_url()."admin/jobsent_report_njfu/manage/$page/$next_order"."_date_send2/\">Sent Date</a></div>";
	echo "<div class=\"cell table_head\"><a href=\"".base_url()."admin/jobsent_report_njfu/manage/$page/$next_order"."_sent_time2/\">Sent Time</a></div>";
	echo "<div class=\"cell table_head\">Manage</div>";
	echo "</div>";
	
	foreach ($results as $jobcat)
	{
		echo "<div class=\"row\">";
		echo "<div class=\"cell table_cell\">".++$offset."</div>";
		echo "<div class=\"cell table_cell\">".$jobcat["jobsend_id"]."</div>";
		echo "<div class=\"cell table_cell\">".str_replace("-","/",$jobcat["date_send1"])."</div>";
		echo "<div class=\"cell table_cell\">".$jobcat["sent_time1"]."</div>";
		echo "<div class=\"cell table_cell\">".$jobcat["rel_id"]."</div>";
		echo "<div class=\"cell table_cell\">".$jobcat["jobcat_key"]."</div>";
		echo "<div class=\"cell table_cell\">APP</div>";
		echo "<div class=\"cell table_cell\">".$jobcat["subscriber_id"]."</div>";
		echo "<div class=\"cell table_cell\">".$jobcat["mdn"]."</div>";
		echo "<div class=\"cell table_cell\">".$jobcat["jobseeker_name"]."</div>";
		echo "<div class=\"cell table_cell\">".$jobcat["jobseeker_kotamadya"]."</div>";
		echo "<div class=\"cell table_cell\">".$jobcat["description"]."</div>";
		echo "<div class=\"cell table_cell\">".$jobcat["title"]."</div>";
		echo "<div class=\"cell table_cell\">".$jobcat["jobcat_key"]."</div>";
		echo "<div class=\"cell table_cell\">".$jobcat["jobseeker_distance"]."</div>";
		//echo "<div class=\"cell table_cell\">".$jobcat["company_name"]."</div>";
		if($jobcat["status"]==2) 
			$status =  "Success";
		else $status = "Failed";
		echo "<div class=\"cell table_cell\">".$status."</div>";
		echo "<div class=\"cell table_cell\">".$jobcat["date_send2"]."</div>";
		echo "<div class=\"cell table_cell\">".$jobcat["sent_time2"]."</div>";
		echo "<div class=\"cell table_cell\"><span style=\"cursor:pointer; color:#00f;\" onclick=\"manage_jobsent(".$jobcat["rel_id"].");\">Resend</span></div>";		
		echo "</div>";
		
	}
	echo "</div><br />"; // CLOSE TABLE

		echo "<ul id=\"pagination-flickr\">";
		if ($nopage > 1) echo "<li class=\"previous\"><a href=\"".base_url()."admin/jobsent_report_njfu/manage/".($nopage - 1)."/$order\">&lt;&lt; Previous</a></li>";
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
					echo "<li><a href=\"".base_url()."admin/jobsent_report_njfu/manage/$page/$order\">$page</a></li>";
				$showPage = $page;
			}
		}
		if ($nopage < $npage) echo "<li class=\"next\"><a href=\"".base_url()."admin/jobsent_report_njfu/manage/".($nopage + 1)."/$order\">Next &gt;&gt;</a></li>";
		echo "</ul>";
	
		echo "<br><br><br>Export to &nbsp;&nbsp;<a href=\"".base_url()."admin/jobsent_report_njfu/save_csv/$order\">CSV</a>";
		echo "&nbsp;&nbsp;&nbsp;<a href=\"".base_url()."admin/jobsent_report_njfu/save_xls/$order\">XLS</a>";
?>
