<?php
	/*
	if ($reason != "")
	{
		echo "<div class=msg>Reject reason : <b>$reason</b></div>\n";
	}
	*/
	echo "<div style=\"display:table; width:100%;\">";
		$list_gender = array(
			"0" => "--",
			"M" => "Male Only",
			"F" => "Female Only",
			"1" => "Male or Female"
		);
		//if (($_SESSION["userlevel"] == "superadmin") || ($_SESSION["userlevel"] == "admin"))		
			$list_status = array(
				"0" => "--",
				"1" => "Active",
				"2" => "Inactive",
				"3" => "Draft",
				"4" => "Waiting for Approval",
				"5" => "Rejected"
			);
		$list_jobtype = array(
			"0" => "--",
			"1" => "Full Time",
			"2" => "Part Time"
		);
		
		for ($a=1; $a <= 20; $a++)
		{ $list_exp[$a] = $a; }
		
		$list_expire_days = array("0" => "--");
		for ($a=1; $a <= 30; $a++)
		{ $list_expire_days[$a] = $a; }
		
		$list_age = array("0" => "--");
		for ($a=17; $a <= 65; $a++)
		{ $list_age[$a] = $a; }
		if(isset($provinces)) {				 
			$list_zip["0"] = "--";
			$list_edu["0"] = "--";
			$list_jobcat["0"] = "--";
			$list_exp = array("0" => "--");

			foreach ($provinces as $a)
			{ $list_province[$a["loc_id"]] = $a["name"]; }
			
			if ($kotamadya != "")
				foreach ($kotamadyas as $a)
				{ $list_kotamadya[$a["loc_id"]] = $a["name"]; }
		
			if ($kecamatan != "")
				foreach ($kecamatans as $a)
				{ $list_kecamatan[$a["loc_id"]] = $a["name"]; }
		
			if ($kelurahan != "")
				foreach ($kelurahans as $a)
				{ $list_kelurahan[$a["loc_id"]] = $a["name"]; }
		
			foreach ($zips as $a)
			{ $list_zip[$a["loc_id"]] = $a["zipcode"]; }
			//{ $list_zip[$a["loc_id"]] = $a["name"]; }

			foreach ($educations as $a)
			{ $list_edu[$a["edu_id"]] = $a["edu_title"]; }
			
			foreach ($jobcats as $a)
			{ $list_jobcat[$a["jobcat_id"]] = $a["jobcat_title"]; }
		}	
    //echo "<pre>"; print_r($zips); echo "</pre>";
	
	
	echo form_open($form_submit)."\n";
	
	if (($_SESSION["userlevel"] == "superadmin") || ($_SESSION["userlevel"] == "admin"))
		if ($status == 3) 
			if (strpos(current_url(),"/admin/jobpost/edit/") === TRUE)
				echo form_checkbox("del", $job_id)." Delete Job Posting";
	//echo $kelurahan."-".$zip;
    
    echo "<div class='right_section'><b>CONTACT</b></div>\n";
	
	echo "<div class='cell_val'>".form_hidden("comp_id",$comp_id)."</div></div>"; 
	
	echo "<div class='row'><div class='cell_key'>".form_label('Company', 'comp_name')."</div>\n";
	echo "<div class='cell_val view'>$comp_name".form_hidden("comp_name", $comp_name ,"size=30 maxlength=30 id='comp_name'")."</div></div>\n";	
	echo "<div class='row'><div class='cell_key'>".form_label('Contact Person', 'comp_cp')."</div>\n";
	echo "<div class='cell_val view'>$comp_cp".form_hidden("comp_cp", $comp_cp ,"size=30 maxlength=30 id='comp_cp'")."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Phone', 'comp_tel')."</div>\n";
	echo "<div class='cell_val view'>$comp_tel".form_hidden("comp_tel", $comp_tel ,"size=30 maxlength=30 id='comp_tel'")."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Fax', 'comp_fax')."</div>\n";
	echo "<div class='cell_val view'>$comp_fax".form_hidden("comp_fax", $comp_fax ,"size=30 maxlength=30 id='comp_fax'")."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('E-Mail', 'comp_email')."</div>\n";
	echo "<div class='cell_val view'>$comp_email".form_hidden("comp_email", $comp_email)."</div></div>\n";  
	
	echo "<div class='right_section'><b>JOB INFORMATION</b></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Category', 'jobcat_id')."</div>\n";

	echo "<div class='cell_val view'>";
	if(isset($list_jobcat)) 
		echo $list_jobcat[$jobcat_id].form_hidden("jobcat_id", $jobcat_id);
	else
		echo $jobcat_id.form_hidden("jobcat_id", $jobcat_id);

	echo "</div></div>\n";
	
	echo "<div class='row'><div class='cell_key'>".form_label('Job Title', 'title')."</div>\n";
	echo "<div class='cell_val view'>$title".form_hidden("title", $title)."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Status', 'status')."</div>\n";
	echo "<div class='cell_val view'>$list_status[$status]".form_hidden("status", $status)."</div></div>\n";
	if ($status=='4' || $status=='1' || $status=='3') {
		echo "<div class='row'><div class='cell_key'>".form_label('Reason', '-')."</div>\n";
		echo "<div class='cell_val view'> - </div></div>\n";
	}else{
		echo "<div class='row'><div class='cell_key'>".form_label('Reason', 'reason')."</div>\n";
		echo "<div class='cell_val view'>$reason".form_hidden("reason", $reason)."</div></div>\n";
	}
	echo "<div class='row'><div class='cell_key'>".form_label('Job Type', 'jobtype_id')."</div>\n";
	echo "<div class='cell_val view'>";
	if(is_numeric($jobtype_id))
		echo $list_jobtype[$jobtype_id].form_hidden("jobtype_id", $jobtype_id);
	else
		echo $jobtype_id.form_hidden("jobtype_id", $jobtype_id);
	echo "</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Salary', 'salary_min')."</div>\n";
	echo "<div class='cell_val view'>$salary_min".form_hidden("salary_min", $salary_min)." - $salary_max".form_hidden("salary_max", $salary_max)."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Description', 'description')."</div>\n";
	echo "<div class='cell_val view'>$description".form_hidden("description", $description)."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>Default SMS</div>\n";
	$default_sms=$comp_name.', '.$title.', '.$description.', hub. '.$comp_cp.' '.$comp_tel;
	echo "<div class='cell_val view'>$default_sms</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Custom SMS', 'sms')."</div>\n";
	echo "<div class='cell_val view'>$sms".form_hidden("sms", $sms)."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Expired on', 'expire_days')."</div>\n";
	echo "<div class='cell_val view'>";
	if($expire_days>0)
		echo $expire_days;//.form_hidden("expire_days", $list_expire_days[$expire_days]);
	else
		echo "(0)";
	echo " days</div></div>\n";
	echo "<div class='row'><div class='cell_key'>Revision</div>\n";
	echo "<div class='cell_val view'>$revision".form_hidden("revision", $revision)."</div></div>\n";
	echo "<div class='right_section'><b>CANDIDATE REQUIREMENT</b></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Gender', 'gender')."</div>\n";
	echo "<div class='cell_val view'>";
	if($gender=="0" || $gender=="1" || $gender=="F" || $gender=="M") 
		echo $list_gender[$gender].form_hidden("gender", $gender);		
	else
		echo $gender.form_hidden("gender", $gender);
	echo "</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Education (min.)', 'edu_min')."</div>\n";
	echo "<div class='cell_val view'>";//print_r($educations);
	if(isset($educations))
		echo $list_edu[$edu_min].form_hidden("edu_min", $edu_min);
	else
		echo $edu_min.form_hidden("edu_min", $edu_min);
	echo "</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Experience (min.)', 'exp_min')."</div>\n";
	echo "<div class='cell_val view'>$exp_min years".form_hidden("exp_min", $exp_min)."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Age', 'age_min')."</div>\n";
	echo "<div class='cell_val view'>$age_min".form_hidden("age_min", $age_min)." - $age_max".form_hidden("age_max", $age_max)." years</div></div>\n";
	
	$region_list = array(
	1 => "By city, kotamadya, etc.",
	2 => "By Zipcode"
	);
	
	echo "<div class='right_section'><b>LOCATION</b></div>\n"; 
	echo "<div class='row'>";
		echo "<div class='cell_key'>".form_label('Region details', 'region')."</div>\n";
		echo "<div class='cell_val view'>$region_list[$region]".form_hidden("region", $region)."</div>";
	echo "</div>\n";

	echo "<div class='row'>";
		echo "<div class='cell_key'>".form_label('Province', 'province')."</div>\n";
		echo "<div class='cell_val view'>";
		if(isset($list_province))
			echo $list_province[$province].form_hidden("province", $province);
		else
			echo $province;
		echo "</div></div>\n";
	echo "<div class='row'>";
		echo "<div class='cell_key'>".form_label('Kotamadya', 'kotamadya')."</div>\n";
		echo "<div class='cell_val view'>";
		if(isset($list_kotamadya))
			echo $list_kotamadya[$kotamadya].form_hidden("kotamadya", $kotamadya);
		else
			echo $kotamadya;
		echo "</div></div>\n";
	echo "<div class='row'>";
		echo "<div class='cell_key'>".form_label('Kecamatan', 'kecamatan')."</div>\n";
		echo "<div class='cell_val view'>";
		if(isset($list_kecamatan))
			echo $list_kecamatan[$kecamatan].form_hidden("kecamatan", $kecamatan);
		else
			echo $kecamatan;
		echo "</div></div>\n";
	echo "<div class='row'>";
		echo "<div class='cell_key'>".form_label('Kelurahan', 'kelurahan')."</div>\n";
		echo "<div class='cell_val view'>";
		
		if(isset($list_kelurahan))
			echo $list_kelurahan[$kelurahan].form_hidden("kelurahan", $kelurahan);
		else
			echo $kelurahan.form_hidden("kelurahan", $a["loc_id"]);						
		echo "</div></div>\n";					
		//echo $kelurahan;
	echo "<div class='row'><div class='cell_key'>".form_label('Map', 'Map')."</div>\n";
	
	echo "<div class='by' id='byZip' style='display:none'>";
		echo "<div class='row'>";
			echo "<div class='cell_key'>".form_label('Zip', 'zip')."</div>\n";
			echo "<div class='cell_val'>".form_dropdown("zip", $list_zip , $zip, "id=zip").form_error("zip", "<div class='form_error'>", "</div>")."</div>\n";
			echo "<input type='hidden' name='hdnZip' id='hdnZip' />";
		echo "</div>";
	echo "</div>";
	
	echo "<div class='cell_val' id='byMap'>";
			echo "<input type='text' id='lat' name='lat' /> ";
			echo "<input type='text' id='lng' name='lng' />";
			echo form_error("lat", "<div class='form_error'>", "</div>");
		echo '<div id="map_canvas" class="defaultMap" style="width:300px;height:250px;" ></div>';
		echo "</div>";
	echo "</div>";

	echo form_hidden("jobposter_id",$_SESSION["userid"]);
	//echo form_hidden("job_id",$job_id); //NEW FOR SENT PARAMETER	
    echo form_hidden("country_id",$_SESSION["curr_country"]);
    echo form_hidden("date_add", $date_add);
    echo form_hidden("date_active", $date_active);
    echo form_hidden("expire_days", $expire_days);
    echo form_hidden("revision", $revision);
	echo "<input type='hidden' name='job_id' id='job_id' value='$job_id' />";
	echo "<input type='hidden' name='aksi' id='aksi' />";	
	echo "<div class='row'><div class='cell_key'>";
	if ($status=='4') {
		if (($_SESSION["userlevel"] == "superadmin") || ($_SESSION["userlevel"] == "admin")) {			
			echo "<div class=\"cell table_cell\" style=\"border:0px; text-align:right\"><a href=\"$reject_button\" id=\"reject_form\" class=\"linkbutton rel=\"simpleDialog\">Reject</a></div>"; 
			$jsnext = 'onClick="document.getElementById('."'aksi'".').value='."'approve'".';document.forms[0].submit()";';	
			//echo "<div class='cell_val'>".form_button("reject","Reject")."</div>";
			//echo "<div class='cell_val'>".form_button("btndraft","Save as Draft","onClick=".'"'."window.location='".base_url()."admin/jobpost/manage'".'"')."</div>";
			echo "<div class='cell_val'><input type=\"button\" value=\"Edit\" id=\"btnedit\" onClick=\"window.location='".base_url()."admin/jobpost/edit/".$job_id."'\"/></div>";	
			echo "<div class='cell_val'>".form_button("approve","Approve",$jsnext)."</div>";		
		} else {
		echo "<div class='cell_val'>".form_button("close","Back","onClick=".'"'."window.location='".base_url()."admin/jobpost/manage'".'"')."</div>";
		}
	} else if ($status=='1') {
		if (($_SESSION["userlevel"] == "superadmin") || ($_SESSION["userlevel"] == "admin")) {			
			$jsnext = 'onClick="document.getElementById('."'aksi'".').value='."'inactive'".';document.forms[0].submit()";';	
			echo "<div class='cell_val'><input type=\"button\" value=\"Edit\" id=\"btnedit\" onClick=\"window.location='".base_url()."admin/jobpost/edit/".$job_id."'\"/></div>";	
			//echo "<div class='cell_val'>".form_button("inactive","Inactive",$jsnext)."</div>";		
			echo "<div class=\"cell table_cell\" style=\"border:0px; text-align:right\"><a href=\"$reject_button_status\" id=\"reject_form_status\" class=\"linkbutton rel=\"simpleDialog\">Inactive</a></div>"; 
		} else {
		echo "<div class='cell_val'>".form_button("close","Back","onClick=".'"'."window.location='".base_url()."admin/jobpost/manage/1/d_date_add'".'"')."</div>";
		}		
	} else if ($status=='2' || $status=='5') {		
		echo "<div class='cell_val'>".form_button("close","Back","onClick=".'"'."window.location='".base_url()."admin/jobpost/manage/1/d_date_add'".'"')."</div>";
	} else {
		$jsdraft = 'onClick="document.getElementById('."'aksi'".').value='."'asdraft'".';document.forms[0].submit()";';
		$jsnext = 'onClick="document.getElementById('."'aksi'".').value='."'asubmit'".';document.forms[0].submit()";';			
		$jsapprove = 'onClick="document.getElementById('."'aksi'".').value='."'approve'".';document.forms[0].submit()";';	
		
		
		echo "<div class='cell_val'>".form_button("discard","Discard")."</div>";
		//echo "<div class='cell_val'>".form_button("btndraft","Save as Draft","onClick=".'"'."window.location='".base_url()."admin/jobpost/manage'".'"')."</div>";
		echo "<div class='cell_val'><input type=\"button\" value=\"Save as Draft\" id=\"btndraft\" $jsdraft/></div>";	
		echo "<div class='cell_val'>".form_button("simpan","Submit",$jsnext)."</div>";
		//echo "<div class='cell_val'>".form_button("submit", "Submit",)."</div>
		
		echo "<div class='cell_val'>".form_button("approve","Active",$jsapprove)."</div>";		
	}
	echo "</div></div>";
	
	echo form_close();
?>
</div>