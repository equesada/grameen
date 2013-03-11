<div style="display:table; width:100%;">
<?php
	$list_gender = array(
		"0" => "--",
		"M" => "Male",
		"F" => "Female"
	);
	
	$list_status = array(
		"0" => "--",
		"1" => "Active",
		"2" => "Inactive"
	);
	//echo "<pre>"; print_r($_SESSION); echo "</pre>";
	
	$list_edu["0"] = "--";
	$list_mentor["0"] = "--";
	$list_zip["0"] = "--";
	
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
	
	foreach ($mentors as $a)
	{ $list_mentor[$a["mentor_id"]] = $a["name"]." - ".$a["mdn"]; }
	
	echo form_open($form_submit)."\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Full Name', 'name')."</div>\n";
	echo "<div class='cell_val'>".form_input("name", $name ,"size=40 maxlength=100 id=name").form_error("name", "<div class='form_error'>", "</div>")."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Status', 'status')."</div>\n";
	echo "<div class='cell_val'>".form_dropdown("status", $list_status , $status).form_error("status", "<div class='form_error'>", "</div>")."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Mentor', 'mentor_id')."</div>\n";
	echo "<div class='cell_val'>".form_dropdown("mentor_id", $list_mentor , $mentor_id).form_error("mentor_id", "<div class='form_error'>", "</div>")."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Gender', 'gender')."</div>\n";
	echo "<div class='cell_val'>".form_dropdown("gender", $list_gender, $gender).form_error("gender", "<div class='form_error'>", "</div>")."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Latest Education', 'edu_id')."</div>\n";
	echo "<div class='cell_val'>".form_dropdown("edu_id", $list_edu, $edu_id).form_error("edu_id", "<div class='form_error'>", "</div>")."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('ID Card Number', 'idcard')."</div>\n";
	echo "<div class='cell_val'>".form_input("idcard", $idcard ,"size=40 maxlength=100")."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Place of birth', 'place_birth')."</div>\n";
	echo "<div class='cell_val'>".form_input("place_birth", $place_birth ,"size=40 maxlength=100")."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Date of birth', 'birthday')."</div>\n";
	echo "<div class='cell_val'><div>".form_input("birthday", $birthday ,"size=40 maxlength=100 class=date-pick")."</div>";
	echo form_error("birthday", "<div class='form_error'>", "</div>")."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('MDN', 'mdn')."</div>\n";
	echo "<div class='cell_val'>".form_input("mdn", $mdn ,"size=40 maxlength=100").form_error("mdn", "<div class='form_error'>", "</div>")."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Minimum Salary', 'salary')."</div>\n";
	echo "<div class='cell_val'>Rp. ".form_input("salary", $salary ,"size=10 maxlength=20")."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Address', 'address1')."</div>\n";
	echo "<div class='cell_val'>".form_input("address1", $address1 ,"size=40 maxlength=100")."</div></div>\n";
	echo "<div class='row'><div class='cell_key'></div>\n";
	echo "<div class='cell_val'>".form_input("address2", $address2 ,"size=40 maxlength=100")."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('RT', 'rt')."</div>\n";
	echo "<div class='cell_val'>".form_input("rt", $rt ,"size=5 maxlength=5");
	echo "<img src=\"".base_url()."images/shim.gif\" width=20 height=1>".form_label('RW', 'rw')."<img src=\"".base_url()."images/shim.gif\" width=10 height=1>".form_input("rw", $rw ,"size=5 maxlength=5")."</div></div>\n";

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
	
	echo "<div class='cell_val'>";
			echo "<input type='text' id='lat' name='lat' />";
			echo "<input type='text' id='lng' name='lng' />";
			echo form_error("lat", "<div class='form_error'>", "</div>");
		echo '<div id="map_canvas" class="defaultMap" style="width:300px;height:250px;" ></div>';
		echo "</div>";
	echo "</div>";
	$useraccess = $_SESSION["userlevel"];
	echo form_hidden("subscriber_id",$subscriber_id);
	if($useraccess != "company"){
		echo "<div class='row'><div class='cell_key'><div class='cell_val'>".form_submit("submit", "Submit")."</div></div></div>";
	}
	echo form_close();
	
?>
</div>