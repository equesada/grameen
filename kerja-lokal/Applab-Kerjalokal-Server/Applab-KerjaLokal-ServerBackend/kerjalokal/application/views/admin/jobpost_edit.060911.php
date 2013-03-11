<div style="display:table; width:100%;">
<?php
	$list_gender = array(
		"0" => "--",
		"M" => "Male Only",
		"F" => "Female Only",
		"1" => "Male or Female"
	);
	
	if (($_SESSION["userlevel"] == "superadmin") || ($_SESSION["userlevel"] == "admin"))
		$list_status = array(
			"0" => "--",
			"1" => "Active",
			"2" => "Inactive",
			"3" => "Draft",
			"4" => "Waiting for Approval"
		);
	
	$list_jobtype = array(
		"0" => "--",
		"1" => "Full Time",
		"2" => "Part Time"
	);
	
	$list_exp = array("0" => "--");
	for ($a=1; $a <= 20; $a++)
	{ $list_exp[$a] = $a; }
	
	$list_expire_days = array("0" => "--");
	for ($a=1; $a <= 30; $a++)
	{ $list_expire_days[$a] = $a; }
	
	$list_age = array("0" => "--");
	for ($a=17; $a <= 65; $a++)
	{ $list_age[$a] = $a; }
	
	//echo "<pre>"; print_r($_SESSION); echo "</pre>";
	//NEW FROM YUDHA
   
   /*
   $d = $comp_id;
   if(isset($d) == 0){
        echo "koosong";
   }else{
        echo "ada";
   }
   
   foreach ($companies as $a)
	{ 	   
	   $company_list[$a["comp_id"]] = $a["company_name"]; 
       $comp_name = $a["company_name"];
       $comp_cp = substr($a["cp"],0,10);       
       $comp_tel = substr($a["tel"],0,10);
       $comp_fax = $a["fax"];
       $comp_email = $a["email"];
              
    }
    
    //EDIT BY YUDHA 29-05-11
    if($comp_id == 0){
        $list_company["0"] = "--";
    }else{
        foreach ($companies as $a)
    	{ 
    	   $company_list[$a["comp_id"]] = $a["company_name"]; 
           $comp_name = $a["company_name"];
           $comp_cp = substr($a["cp"],0,10);       
           $comp_tel = substr($a["tel"],0,10);
           $comp_fax = $a["fax"];
           $comp_email = $a["email"];
                  
        }
    }
   */
    
        
    //
    //$list_company[""] = $company_list;
	$list_zip["0"] = "--";
	$list_edu["0"] = "--";
	$list_jobcat["0"] = "--";
	
	//print_r($companies);
    //echo "<pre>"; print_r($companies); echo "</pre>";
	
	$list_province = array("0"=>"--");
	$list_kotamadya = array("0"=>"--");
	$list_kecamatan = array("0"=>"--");
	$list_kelurahan = array("0"=>"--");
	
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
	
	foreach ($educations as $a)
	{ $list_edu[$a["edu_id"]] = $a["edu_title"]; }
	
	foreach ($jobcats as $a)
	{ $list_jobcat[$a["jobcat_id"]] = $a["jobcat_title"]; }
	
	echo form_open($form_submit)."\n";
	
	if (($_SESSION["userlevel"] == "superadmin") || ($_SESSION["userlevel"] == "admin"))
		if ($status == 3) 
			if (strpos(current_url(),"/admin/jobpost/edit/") === TRUE)
				echo form_checkbox("del", $job_id)." Delete Job Posting";
	//echo $kelurahan."-".$zip;
    
    echo "<div class='right_section'><b>CONTACT</b></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Company Group', 'comp_id')."</div>\n";
	
	if (($_SESSION["userlevel"] == "superadmin") || ($_SESSION["userlevel"] == "admin"))
	{
		foreach ($companies as $a)
		{ 
		  $list_company[$a["comp_id"]] = $a["company_name"];           
        }
		echo "<div class='cell_val'>".form_dropdown("comp_id", $list_company, $comp_id)." <input type=\"button\" value=\"Get Existing Record\" id=\"btn\" /> ";        
		echo form_error("comp_id", "<div class='form_error'>", "</div>")."</div></div>\n";
	}
	else
	{	echo "<div class='cell_val'>".$company_name.form_hidden("comp_id",$_SESSION["comp_id"])."</div></div>"; }
	
	echo "<div class='row'><div class='cell_key'>".form_label('Company', 'comp_name')."</div>\n";
	echo "<div class='cell_val'>".form_input("comp_name", $comp_name ,"size=30 maxlength=30 id='comp_name'").form_error("comp_name", "<div class='form_error'>", "</div>")."</div></div>\n";
	
	echo "<div class='row'><div class='cell_key'>".form_label('Contact Person', 'comp_cp')."</div>\n";
	echo "<div class='cell_val'>".form_input("comp_cp", $comp_cp ,"size=30 maxlength=30 id='comp_cp'").form_error("comp_cp", "<div class='form_error'>", "</div>")."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Phone', 'comp_tel')."</div>\n";
	echo "<div class='cell_val'>".form_input("comp_tel", $comp_tel ,"size=30 maxlength=30 id='comp_tel'").form_error("comp_tel", "<div class='form_error'>", "</div>")."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Fax', 'comp_fax')."</div>\n";
	echo "<div class='cell_val'>".form_input("comp_fax", $comp_fax ,"size=30 maxlength=30 id='comp_fax'").form_error("comp_fax", "<div class='form_error'>", "</div>")."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('E-Mail', 'comp_email')."</div>\n";
	echo "<div class='cell_val'>".form_input("comp_email", $comp_email ,"size=40 maxlength=50 id='comp_email'").form_error("comp_email", "<div class='form_error'>", "</div>")."</div></div>\n";
    
    
	echo "<div class='right_section'><b>JOB INFORMATION</b></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Category', 'jobcat_id')."</div>\n";
	echo "<div class='cell_val'>".form_dropdown("jobcat_id", $list_jobcat ,$jobcat_id).form_error("jobcat_id", "<div class='form_error'>", "</div>")."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Job Title', 'title')."</div>\n";
	echo "<div class='cell_val'>".form_input("title", $title ,"size='40' maxlength='20' id='title'").form_error("title", "<div class='form_error'>", "</div>")."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Status', 'status')."</div>\n";

	if (($_SESSION["userlevel"] == "superadmin") || ($_SESSION["userlevel"] == "admin"))
		echo "<div class='cell_val'>".form_dropdown("status", $list_status , $status).form_error("status", "<div class='form_error'>", "</div>")."</div></div>\n";
	else
		echo "<div class='cell_val'>".form_checkbox("status", 3, $status == "3" ? TRUE : FALSE)." Draft</div></div>\n";

	echo "<div class='row'><div class='cell_key'>".form_label('Job Type', 'jobtype_id')."</div>\n";
	echo "<div class='cell_val'>".form_dropdown("jobtype_id", $list_jobtype, $jobtype_id)."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Salary', 'salary_min')."</div>\n";
	echo "<div class='cell_val'>From ".form_input("salary_min", $salary_min ,"size=10 maxlength=10")." To ".form_input("salary_max", $salary_max ,"size=10 maxlength=10")."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Description', 'description')."</div>\n";
	echo "<div class='cell_val'>".form_input("description", $description ,"size='40' maxlength='95' id='description'")." (max. 95 character)";
	echo form_error("description", "<div class='form_error'>", "</div>")."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>Default SMS</div>\n";
	echo "<div class='cell_val'><div id='default_sms'></div></div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Custom SMS', 'sms')."</div>\n";
	echo "<div class='cell_val'>".form_input("sms", $sms ,"size=40 maxlength=155 id=sms")."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Expired on', 'expire_days')."</div>\n";
	echo "<div class='cell_val'>".form_dropdown("expire_days", $list_expire_days, $expire_days)." days</div></div>\n";
	echo "<div class='row'><div class='cell_key'>Revision</div>\n";
	echo "<div class='cell_val'>$revision</div></div>\n";

	echo "<div class='right_section'><b>CANDIDATE REQUIREMENT</b></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Gender', 'gender')."</div>\n";
	echo "<div class='cell_val'>".form_dropdown("gender", $list_gender, $gender).form_error("gender", "<div class='form_error'>", "</div>")."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Education (min.)', 'edu_min')."</div>\n";
	echo "<div class='cell_val'>".form_dropdown("edu_min", $list_edu, $edu_min).form_error("edu_min", "<div class='form_error'>", "</div>")."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Experience (min.)', 'exp_min')."</div>\n";
	echo "<div class='cell_val'>".form_dropdown("exp_min", $list_exp, $exp_min)." years".form_error("exp_min", "<div class='form_error'>", "</div>")."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Age', 'age_min')."</div>\n";
	echo "<div class='cell_val'>Min ".form_dropdown("age_min", $list_age, $age_min);
	echo " &nbsp;&nbsp; Max ".form_dropdown("age_max", $list_age, $age_max)." years";
	echo form_error("age_min", "<div class='form_error'>", "</div>");
	echo form_error("age_max", "<div class='form_error'>", "</div>");
	echo "</div></div>\n";
	
	
	
	echo "<div class='right_section'><b>LOCATION</b></div>\n"; 
	$region_list = array(
		1 => "By city, kotamadya, etc.",
		2 => "By Zipcode"
	);
	echo "<div class='row'><div class='cell_key'>".form_label('Region details', 'region')."</div>\n";
	echo "<div class='cell_val'>".form_dropdown("region", $region_list , $region, "id=region")."</div></div>\n";
	echo "<div class='by' id='byRegion'>";
		echo "<div class='row'>";
			echo "<div class='cell_key'>".form_label('Province', 'province')."</div>\n";
			echo "<div class='cell_val'>".form_dropdown("province", $list_province , $province, "id=province").form_error("province", "<div class='form_error'>", "</div>")."</div>";
		echo "</div>\n";
		echo "<div class='row'>";
			echo "<div class='cell_key'>".form_label('Kotamadya', 'kotamadya')."</div>\n";
			echo "<div class='cell_val'>".form_dropdown("kotamadya", $list_kotamadya , $kotamadya, "id=kotamadya").form_error("kotamadya", "<div class='form_error'>", "</div>")."</div>";
		echo "</div>\n";
		echo "<div class='row'>";
			echo "<div class='cell_key'>".form_label('Kecamatan', 'kecamatan')."</div>\n";
			echo "<div class='cell_val'>".form_dropdown("kecamatan", $list_kecamatan , $kecamatan, "id=kecamatan").form_error("kecamatan", "<div class='form_error'>", "</div>")."</div>";
		echo "</div>\n";
		echo "<div class='row'>";
			echo "<div class='cell_key'>".form_label('Kelurahan', 'kelurahan')."</div>\n";
			echo "<div class='cell_val'>".form_dropdown("kelurahan", $list_kelurahan , $kelurahan, "id=kelurahan").form_error("kelurahan", "<div class='form_error'>", "</div>")."</div>";
		echo "</div>\n";		
	echo "</div>";
	echo "<div class='by' id='byZip' style='display:none'>";
		echo "<div class='row'>";
			echo "<div class='cell_key'>".form_label('Zip', 'zip')."</div>\n";
			echo "<div class='cell_val'>".form_dropdown("zip", $list_zip , $zip, "id=zip").form_error("zip", "<div class='form_error'>", "</div>")."</div>\n";
			echo "<input type='hidden' name='hdnZip' id='hdnZip' />";
		echo "</div>";
	echo "</div>";
	
	echo "<div class='row'><div class='cell_key'>".form_label('Map', 'Map')."</div>\n";
	
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
    echo form_hidden("revision", $revision);
	echo "<div class='row'><div class='cell_key'><div class='cell_val'>".form_submit("submit", "Submit")."</div></div></div>";
	
	echo form_close();
?>
</div>