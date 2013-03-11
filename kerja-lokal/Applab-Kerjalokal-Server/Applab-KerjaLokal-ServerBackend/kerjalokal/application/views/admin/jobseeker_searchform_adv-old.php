<?php
	// lokasi KOTAMADYA
	$list_location1[0] = "All";
	foreach ($locations['KOTAMADYA'] as $location1)
	{ $list_location1[$location1["loc_id"]] = $location1["name"]; }	
	
	// lokasi KECAMATAN
	$list_location2[0] = "All";
	foreach ($locations['KECAMATAN'] as $location2)
	{ $list_location2[$location2["loc_id"]] = $location2["name"]; }
	
	// lokasi KELURAHAN
	$list_location3[0] = "All";
	foreach ($locations['KELURAHAN'] as $location3)
	{ $list_location3[$location3["loc_id"]] = $location3["name"]; }
	
	//print_r($list_location);
?>


<script src="<?php echo base_url(); ?>js/jquery.ui.core.js"></script>
<script src="<?php echo base_url(); ?>js/jquery.ui.widget.js"></script>
<script src="<?php echo base_url(); ?>js/jquery.ui.datepicker.js"></script>
<!--script src="<?php echo base_url(); ?>js/date.js"></script-->
<!--script src="<?php echo base_url(); ?>js/jquery.datePicker.js"></script-->
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/datePicker.css">
<script type="text/javascript">
$(function() {
	$( ".date-pick" ).datepicker({ dateFormat: 'yy-mm-dd' });
	//getter
	var dateFormat = $( ".date-pick" ).datepicker( "option", "dateFormat" );
	//setter
	$( ".date-pick" ).datepicker( "option", "dateFormat", 'yy-mm-dd' );
});

function searchLocationByProvince(a) {
	//alert(a);
	$.ajax({
	  url: "<?php echo base_url(); ?>admin/jobpost/ajax_get_location_by_id/"+a+"",
	  dataType: "json",
	  success: function(data){
			//$("#results").append(data);
			$(".kotamadya_id").removeAttr("disabled");
			selectValues = data;
			$(".kotamadya_id").html("");
			//$(".kotamadya_id").attr("selected",a);
			$.each(selectValues, function(key, value) {   
				 $('.kotamadya_id')
					  .append($('<option>', { value : key })
					  .text(value)); 					
			});
			//alert(this.a);  
		}
		
	});
}

function searchLocationByKotamadya(a) {	
	$.ajax({
	  url: "<?php echo base_url(); ?>admin/jobpost/ajax_get_location_by_id/"+a+"",
	  dataType: "json",
	  success: function(data){
			//$("#results").append(data);
			$(".kecamatan_id").removeAttr("disabled");
			selectValues = data;
			$(".kecamatan_id").html("");
			//$(".kecamatan_id").attr("selected",a);
			$.each(selectValues, function(key, value) {   
				 $('.kecamatan_id')
					  .append($('<option>', { value : key })
					  .text(value)); 
			});
		}
	});
}
function searchLocationByKecamatan(a) {
	$.ajax({
	  url: "<?php echo base_url(); ?>admin/jobpost/ajax_get_location_by_id/"+a+"",
	  dataType: "json",
	  success: function(data){
			//$("#results").append(data);
			$(".kelurahan_id").removeAttr("disabled");
			selectValues = data;
			$(".kelurahan_id").html("");
			//$(".kelurahan_id").attr("selected",a);
			$.each(selectValues, function(key, value) {   
				 $('.kelurahan_id')
					  .append($('<option>', { value : key })
					  .text(value)); 
			});
		}
	});		
}
function searchLocationByKelurahan(a) {
	$.ajax({
	  url: "<?php echo base_url(); ?>admin/jobpost/ajax_get_location_by_id/"+a+"",
	  dataType: "json",
	  success: function(data){
			//$("#results").append(data);
			$(".kelurahan_id").removeAttr("disabled");
			selectValues = data;
			$(".kelurahan_id").html("");
			//$(".kelurahan_id").attr("selected",a);
			$.each(selectValues, function(key, value) {   
				 $('.kelurahan_id')
					  .append($('<option>', { value : key })
					  .text(value)); 
			});
		}
	});		
}

</script>

<form action="<?php echo $form_submit; ?>" method="post">

<div style="display:inline; width:1%;">
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;">Status</div>
		<div class="cell table_cell" style="border:0px; text-align:left;"><?php echo form_dropdown("status", $list_status); ?></div>
	</div>
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;">Gender</div>
		<div class="cell table_cell" style="border:0px; text-align:left;"><?php echo form_dropdown("gender", $list_gender); ?></div>
	</div>
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;">Education</div>
		<div class="cell table_cell" style="border:0px; text-align:left;"><?php echo form_dropdown("education", $education); ?></div>
	</div>
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;">Name</div>
		<div class="cell table_cell" style="border:0px; text-align:left;">
		<?php echo form_input("jobseeker_name", ($jobseeker_name ? $_GET['jobseeker_name'] : '')); ?>
		</div>
	</div>
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;">MDN</div>
		<div class="cell table_cell" style="border:0px; text-align:left;">
		<?php echo form_input("mdn", ($mdn ? $_GET['mdn'] : '')); ?>
		</div>
	</div>
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;">Mentor</div>
		<div class="cell table_cell" style="border:0px; text-align:left;">
		<?php echo form_input("mentor", ($mentor ? $_GET['mentor'] : '')); ?>
		</div>
	</div>
	
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;"><i>Birth Year</i></div>
		<div class="cell table_cell" style="border:0px; text-align:left;"><input type="text" name="birthday[from_date]" size="14" maxlength="100" class="date-pick" id="AppDateFrom"></div>
		<div class="cell table_cell" style="border:0px; text-align:right;"><i>To</i></div>
		<div class="cell table_cell" style="border:0px; text-align:left;"><input type="text" name="birthday[to_date]" size="14" maxlength="50" class="date-pick" id="AppDateTo"></div>
	</div>
</div>	
<div style="display:inline; width:1%;">
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;"><b>Submit Date</b></div>
		<div class="cell table_cell" style="border:0px; text-align:left;">&nbsp;</div>
	</div>
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;"><i>From</i></div>
		<div class="cell table_cell" style="border:0px; text-align:left;"><input type="text" name="submit_date[from_date]" size="14" maxlength="100" class="date-pick" id="SubDateFrom"></div>
	</div>
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;"><i>To</i></div>
		<div class="cell table_cell" style="border:0px; text-align:left;"><input type="text" name="submit_date[to_date]" size="14" maxlength="50" class="date-pick" id="SubDateTo"></div>
	</div>	
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;"><b>Last Activity Date</b></div>
		<div class="cell table_cell" style="border:0px; text-align:left;">&nbsp;</div>
	</div>
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;"><i>From</i></div>
		<div class="cell table_cell" style="border:0px; text-align:left;"><input type="text" name="edit_date[from_date]" size="14" maxlength="100" class="date-pick" id="EditDateFrom"></div>
	</div>
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;"><i>To</i></div>
		<div class="cell table_cell" style="border:0px; text-align:left;"><input type="text" name="edit_date[to_date]" size="14" maxlength="50" class="date-pick" id="EditDateTo"></div>
	</div>	
</div>	
<div style="display:inline; width:1%;">
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;"><b>Location</b></div>
		<div class="cell table_cell" style="border:0px; text-align:left;"></div>
	</div>
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;">Province</div>
		<div class="cell table_cell" style="border:0px; text-align:left;">
		<div id="results"></div>	
			<?php 
				//alert(this.options[loc_title.selectedIndex].text);
				
				$attributes = 'onChange="searchLocationByProvince(this.value);$(\'#loc_title\').val(this.options[loc_id.selectedIndex].text);"';				
				echo form_dropdown("loc_id", $location,'',$attributes); 	
				//print_r($location);								
			?>
			<?
				echo "<input type=\"hidden\" id=\"loc_title\" name=\"loc_title\" value=\"\" />"							
			?>
			
			
		</div>
	</div>
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;">Kotamadya</div>
		<div class="cell table_cell" style="border:0px; text-align:left;">
			<?php 
				//$attributes = 'disabled class="kotamadya_id" onChange="searchLocationByKotamadya(this.value);"';
				//echo form_dropdown("loc_id[kotamadya_id]", $list_location1,'',$attributes); 				
				$attributes = 'disabled class="kotamadya_id" onChange="searchLocationByKotamadya(this.value);$(\'#loc_title\').val(this.options[kotamadya_id.selectedIndex].text);"';
				echo form_dropdown("kotamadya_id", $list_location1,'',$attributes); 
			?>
		</div>
	</div>
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;">Kecamatan</div>
		<div class="cell table_cell" style="border:0px; text-align:left;">
			<?php 
				$attributes = 'disabled class="kecamatan_id" onChange="searchLocationByKecamatan(this.value);$(\'#loc_title\').val(this.options[kecamatan_id.selectedIndex].text);"';
				echo form_dropdown("kecamatan_id", $list_location2,'',$attributes); 
			?>
		</div>
	</div>
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;">Kelurahan</div>
		<div class="cell table_cell" style="border:0px; text-align:left;">
			<?php 
				//$attributes = 'disabled class="kelurahan_id"';
				$attributes = 'disabled class="kelurahan_id" onChange="$(\'#loc_title\').val(this.options[kelurahan_id.selectedIndex].text);"';
				echo form_dropdown("kelurahan_id", $list_location3,'',$attributes); 
			?>
		</div>
	</div>
	
</div>	

<div class="row">
	<div class="cell table_cell" style="border:0px; text-align:right;"></div>
	<div class="cell table_cell" style="border:0px; text-align:left;"><input type="submit" name="submit" value="Submit"></div>
</div>	
	

</form>