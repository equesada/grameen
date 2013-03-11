
<div style="display:table; width:100%;">
<?php
	
	$list_subscriber = array("0" => "--");
	$list_jobcat = array("0" => "--");
	
	foreach ($subscribers as $a)
	{ $list_subscriber[$a["subscriber_id"]] = $a["subscriber_name"]; }
	foreach ($jobcats as $a)
	{ $list_jobcat[$a["jobcat_id"]] = $a["jobcat_title"]; }
	
	echo form_open(base_url()."admin/jobseeker/addjob/submit");
	echo "<div class='right_section'><b>ADD JOB CATEGORY TO JOB SUBSCRIBER</b></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Subscriber', 'subscriber_id')."</div>\n";
	echo "<div class='cell_val'>".form_dropdown("subscriber_id", $list_subscriber)."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Job Category', 'jobcat_id')."</div>\n";
	echo "<div class='cell_val'>".form_dropdown("jobcat_id", $list_jobcat)."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Date start', 'date_active')."</div>\n";
	echo "<div class='cell_val'><div>".form_input("date_active", $date_active ,"size=40 maxlength=100 class='date-pick'")."</div></div></div>";
	echo "<div class='row'><div class='cell_key'>".form_label('Date end', 'date_expired')."</div>\n";
	echo "<div class='cell_val'><div>".form_input("date_expired", $date_expired ,"size=40 maxlength=100 class=date-pick")."</div></div></div>";
	echo "<div class='row'><div class='cell_key'></div><div class='cell_val'>".form_submit("submit", "Submit")."</div></div>";
	
	echo form_close();

	echo form_open(base_url()."admin/jobseeker/addjob/submit");
	echo "<div class='right_section'><b>UPDATES JOB SUBSCRIBER's SUBSCRIPTION</b></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Subscriber', 'subscriber_id')."</div>\n";
	echo "<div class='cell_val'>".form_dropdown("subscriber_id", $list_subscriber)." <input type=\"button\" value=\"Get Subscription Category\" id=\"btn\" /></div></div>\n";
	echo "<div class='row'><div class='cell_key'>Subscription</div>\n";
	echo "<div class='cell_val'><div id='jobcats'></div>";
	echo "</div></div>\n";

	echo "<div class='row'><div class='cell_key'></div><div class='cell_val'>".form_submit("submit", "Submit")."</div></div>";
	//  <input type=\"button\" value=\"Get Existing Record\" id=\"comp_btn\" />
	echo form_close();


	?>
</div>
<script>
$(function(){
//onchange=\"get_company_by_comp_id(this.value)\"
	$('#comp_btn').css('cursor','pointer').click(function()
	{
		get_company_by_comp_id($(this).prev().val());
		//alert($(this).prev().val());
	});
});
</script>