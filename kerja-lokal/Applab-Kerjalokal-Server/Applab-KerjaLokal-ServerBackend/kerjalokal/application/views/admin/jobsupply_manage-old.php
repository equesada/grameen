<?php echo form_open(base_url()."admin/jobsupply"); ?>
<div class="table">
<div class="row">
	<div class="cell table_head" style="width:150px;">Job Category</div>
	<div class="cell table_head" style="width:100px;">Area</div>	
	<div class="cell table_head">Subscriber</div>
	<div class="cell table_head">Active Job</div>
	<div class="cell table_head">Target Job</div>
</div>
<?php
	
	foreach ($jobcats["results"] as $jobcat)
	{
		//echo "<pre>"; print_r($jobcat); echo "</pre>";
		
		list($approved_date_from, $approved_date_to) = explode(" ", $jobcat["date_add"]);
		//echo $approved_date_from."<hr>";
		//echo $approved_date_to."<hr>";
		
		
	
		echo "<div class=\"row\" style=\"border-bottom:2px solid #666;\">\n";
		echo "<div class=\"cell table_cell\">";
		//echo "<a href=\"".base_url()."admin/jobpost/manage/1/a_job_title/1/20/".$jobcat["jobcat_id"]."/_/".$jobcat["date_add"]."/_\">".$jobcat["jobcat_title"]."</a> (".$njob[$jobcat["jobcat_id"]][1]["current"].") </div>\n";
		echo "".$jobcat["jobcat_title"]." </div>\n";
		echo "<div class=\"cell\" style=\"border-right:1px solid #ccc;\">"; 
		echo "<div style=\"border-bottom:1px solid #ccc; padding:5px; \">Jakarta</div>";
		echo "<div style=\"border-bottom:1px solid #ccc; padding:5px; \">Depok</div>";
		echo "<div style=\"border-bottom:1px solid #ccc; padding:5px; \">Bogor</div>";
		echo "<div style=\"border-bottom:1px solid #ccc; padding:5px; \">Tangerang</div>";
		echo "<div style=\"border-bottom:1px solid #ccc; padding:5px; \">Bekasi</div>";
		echo "<div style=\"border-bottom:1px solid #ccc; padding:5px; \">Jumlah</div>";
		echo "</div>\n";
		
		echo "<div class=\"cell\">";
		echo "<div style=\"border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px; \"><a href=\"".base_url()."admin/jobseeker/manage/1/a_subscriber_name/20/1/".$jobcat["jobcat_id"]."/jakarta/\">".$nsubscriber[$jobcat["jobcat_id"]][2]["current"]."</a></div>";
		echo "<div style=\"border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px; \"><a href=\"".base_url()."admin/jobseeker/manage/1/a_job_title/20/1/".$jobcat["jobcat_id"]."/depok/\">".$nsubscriber[$jobcat["jobcat_id"]][4]["current"]."</a></div>";
		echo "<div style=\"border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px; \"><a href=\"".base_url()."admin/jobseeker/manage/1/a_job_title/20/1/".$jobcat["jobcat_id"]."/bogor/\">".$nsubscriber[$jobcat["jobcat_id"]][3]["current"]."</a></div>";
		echo "<div style=\"border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px; \"><a href=\"".base_url()."admin/jobseeker/manage/1/a_job_title/20/1/".$jobcat["jobcat_id"]."/banten/\">".$nsubscriber[$jobcat["jobcat_id"]][5]["current"]."</a></div>";
		echo "<div style=\"border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px; \"><a href=\"".base_url()."admin/jobseeker/manage/1/a_job_title/20/1/".$jobcat["jobcat_id"]."/bekasi/\">".$nsubscriber[$jobcat["jobcat_id"]][6]["current"]."</a></div>";
		echo "<div style=\"border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px; \">".$nsubscriber[$jobcat["jobcat_id"]][1]["current"]."</div>";
		echo "</div>\n";
		
		
		echo "<div class=\"cell\">"; 
		echo "<div style=\"border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px; \"><a href=\"".base_url()."admin/jobpost/manage/1/a_job_title/20/1/".$jobcat["jobcat_id"]."/jakarta/".date("Y-m-d")."/\">".$njob[$jobcat["jobcat_id"]][2]["current"]."</a></div>";		
		echo "<div style=\"border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px; \"><a href=\"".base_url()."admin/jobpost/manage/1/a_job_title/20/1/".$jobcat["jobcat_id"]."/depok/".date("Y-m-d")."/\">".$njob[$jobcat["jobcat_id"]][4]["current"]."</a></div>";
		echo "<div style=\"border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px; \"><a href=\"".base_url()."admin/jobpost/manage/1/a_job_title/20/1/".$jobcat["jobcat_id"]."/bogor/".date("Y-m-d")."/\">".$njob[$jobcat["jobcat_id"]][3]["current"]."</a></div>";
		echo "<div style=\"border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px; \"><a href=\"".base_url()."admin/jobpost/manage/1/a_job_title/20/1/".$jobcat["jobcat_id"]."/tangerang/".date("Y-m-d")."/\">".$njob[$jobcat["jobcat_id"]][5]["current"]."</a></div>";
		echo "<div style=\"border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px; \"><a href=\"".base_url()."admin/jobpost/manage/1/a_job_title/20/1/".$jobcat["jobcat_id"]."/bekasi/".date("Y-m-d")."/\">".$njob[$jobcat["jobcat_id"]][6]["current"]."</a></div>";
		echo "<div style=\"border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px; \">".$njob[$jobcat["jobcat_id"]][1]["current"]."</div>";
		echo "</div>\n";
		
		
		
		echo "<div class=\"cell\">";
		echo "<div style=\"border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px; \">".$njob[$jobcat["jobcat_id"]][2]["need"]."</div>";
		echo "<div style=\"border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px; \">".$njob[$jobcat["jobcat_id"]][4]["need"]."</div>";
		echo "<div style=\"border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px; \">".$njob[$jobcat["jobcat_id"]][3]["need"]."</div>";
		echo "<div style=\"border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px; \">".$njob[$jobcat["jobcat_id"]][5]["need"]."</div>";
		echo "<div style=\"border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px; \">".$njob[$jobcat["jobcat_id"]][6]["need"]."</div>";
		$val = $njob[$jobcat["jobcat_id"]][2]["need"] + $njob[$jobcat["jobcat_id"]][4]["need"] + $njob[$jobcat["jobcat_id"]][3]["need"] + $njob[$jobcat["jobcat_id"]][5]["need"] + $njob[$jobcat["jobcat_id"]][6]["need"];	
		echo "<div style=\"border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px; \">".$val."</div>";
		echo "</div>\n";
		echo "</div>\n";
	}
	$paging = "";	 
?>

<div class="row">
	<div class="cell table_head" style="width:150px;"></div>
	<div class="cell table_head" style="width:100px;">Total</div>
	<div class="cell table_head"></div>
	<div class="cell table_head"></div>
	<div class="cell table_head"></div>
</div>

</div>


