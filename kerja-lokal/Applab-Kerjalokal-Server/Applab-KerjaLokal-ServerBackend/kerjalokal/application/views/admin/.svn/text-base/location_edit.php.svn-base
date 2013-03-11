<h3>Edit Location</h3>
<div class="table">
<?php
	$list_countries = array();
	$list_province = array();
	$list_kotamadya = array();
	$list_kecamatan = array();
	$list_kelurahan = array();
	
	$loc_type = $loc_type == "KOTA" ? "PROVINCE" : $loc_type;
	
	foreach ($countries["results"] as $a)
	{ $list_country[$a["country_id"]] = $a["country_name"]; }
	
	if ($loc_type != "PROVINCE")
	{
		foreach ($provinces as $a)
		{ $list_province[$a["loc_id"]] = $a["name"]; }

		if ($loc_type != "KOTAMADYA")
		{
			foreach ($kotamadyas as $a)
			{ $list_kotamadya[$a["loc_id"]] = $a["name"]; }

			if ($loc_type != "KECAMATAN")
			{
				if ($kecamatan != "")
					foreach ($kecamatans as $a)
					{ $list_kecamatan[$a["loc_id"]] = $a["name"]; }
					
			}
		}
	}
	echo form_open(base_url()."admin/master/location/edit/1/$loc_id")."\n";
	echo "<div class=\"row\">\n";
	echo "<div class=\"cell_key\">".form_label('Country', 'country_id')."</div>\n";
	echo "<div class=\"cell_val\">".form_dropdown("country_id", $list_country, $country_id, "id=country")."</div>\n";
	echo "</div>\n";
	if ($loc_type != "PROVINCE")
	{
		echo "<div class=\"row\">\n";
		echo "<div class=\"cell_key\">".form_label('Province', 'province_id')."</div>\n";
		echo "<div class=\"cell_val\">".form_dropdown("province_id", $list_province, $province, "id=province")."</div>\n";
		echo "</div>\n";
		
		if ($loc_type != "KOTAMADYA")
		{
			echo "<div class=\"row\">\n";
			echo "<div class=\"cell_key\">".form_label('Kotamadya', 'kotamadya')."</div>\n";
			echo "<div class=\"cell_val\">".form_dropdown("kotamadya", $list_kotamadya, $kotamadya, "id=kotamadya")."</div>\n";
			echo "</div>\n";
			
			if ($loc_type != "KECAMATAN")
			{
				echo "<div class=\"row\">\n";
				echo "<div class=\"cell_key\">".form_label('Kecamatan', 'kecamatan')."</div>\n";
				echo "<div class=\"cell_val\">".form_dropdown("kecamatan", $list_kecamatan, $kecamatan, "id=kecamatan")."</div>\n";
				echo "</div>\n";
				echo "<div class=\"row\">\n<div class=\"cell_key\">$loc_type</div>\n<div class=\"cell_val\">$name</div>\n</div>\n";
			}
			else
			{ echo "<div class=\"row\">\n<div class=\"cell_key\">$loc_type</div>\n<div class=\"cell_val\">$name</div>\n</div>\n"; }
		}
		else
		{ echo "<div class=\"row\">\n<div class=\"cell_key\">$loc_type</div>\n<div class=\"cell_val\">$name</div>\n</div>\n"; }
	}
	else
	{ echo "<div class=\"row\">\n<div class=\"cell_key\">$loc_type</div>\n<div class=\"cell_val\">$name</div>\n</div>\n"; }
	echo "<div class=\"row\">\n";
	echo "<div class=\"cell_key\">".form_label("New $loc_type", "name")."</div>\n";
	echo "<div class=\"cell_val\">".form_input("name", $name ,"size=40 maxlength=100")."&nbsp;&nbsp;";
	echo form_checkbox("status", "2", $status == "2" ? TRUE : "")." Hide</div>\n";
	echo form_error("name", "<div class='form_error'>", "</div>");
	echo "</div>\n";
	if ($loc_type == "KELURAHAN")
	{
		echo "<div class=\"row\">\n";
		echo "<div class=\"cell_key\">".form_label('New Zip Code', 'zipcode')."</div>\n";
		echo "<div class=\"cell_val\">".form_input("zipcode", $zipcode ,"size=10 maxlength=10 id=zipcode")."</div>\n";
		echo "</div>\n";
		echo "<div class=\"row\">\n";
		echo "<div class=\"cell_key\"></div>\n";
		echo "<div class=\"cell_val\"><div id=\"map_canvas\" style=\"width:300px; height:300px;\"></div>";
		echo "</div>\n";
		echo "</div>\n";
		echo "<div class=\"row\">\n";
		echo "<div class=\"cell_key\"></div>\n";
		echo "<div class=\"cell_val\">".form_input("lat", "" ,"size=20 maxlength=50 id=lat")." ".form_input("lng", "" ,"size=20 maxlength=50 id=lng")."</div>\n";
		echo "</div>\n";
	}
	echo "</div>\n";
	$loc_type = $loc_type == "PROVINCE" ? "KOTA" : $loc_type;
	echo form_hidden("loc_id", "$loc_id");
	echo form_hidden("loc_type", "$loc_type");
	echo form_submit("submit", "Submit");
	echo form_close();
?>
</div>
