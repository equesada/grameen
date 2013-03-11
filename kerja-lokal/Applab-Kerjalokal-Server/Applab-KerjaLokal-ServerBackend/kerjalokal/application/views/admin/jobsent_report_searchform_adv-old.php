<?php
	$list_jobcat[0] = "ALL";
	foreach ($jobcats["results"] as $a)
	{ $list_jobcat[$a["jobcat_id"]] = $a["jobcat_title"]; }

	$list_company[0] = "ALL";
	foreach ($companies as $company)
	{ $list_company[$company["comp_id"]] = $company["company_name"]; }
	
	// lokasi KOTAMADYA
	$list_location1[0] = "All";
	foreach ($locations['KOTAMADYA'] as $location1)
	{ $list_location1[$location1["loc_id"]] = $location1["name"]; }	
		
	//print_r($list_location);
?>

<script src="<?php echo base_url(); ?>js/jquery.ui.core.js"></script>
<script src="<?php echo base_url(); ?>js/jquery.ui.widget.js"></script>
<script src="<?php echo base_url(); ?>js/jquery.ui.datepicker.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/datePicker.css">
<script type="text/javascript">
$(function() {
	$( ".date-pick" ).datepicker({ dateFormat: 'yy-mm-dd' });
	//getter
	var dateFormat = $( ".date-pick" ).datepicker( "option", "dateFormat" );
	//setter
	$( ".date-pick" ).datepicker( "option", "dateFormat", 'yy-mm-dd' );
});
</script>
<?php //echo $list_status; ?>
<!--form action="<?php echo base_url(); ?>admin/jobpost/manage" method="post"-->
<form action="<?php echo $form_submit; ?>" method="post">
<!--form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post"-->
<table style="border:solid; width:83%">
<tr>
<td style="width:34%">
<!--<div class="table" style=" border:1px">
<div class="row">
<div class="cell">
-->
<div style="display:inline; width:30%;">
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;"><b>Server Sent Date</b></div>
		<div class="cell table_cell" style="border:0px; text-align:left;">&nbsp;</div>
	</div>
	<div class="row">
    <?php //if($search['date_send1']['from_date']!='_') echo $search['date_send1']['[to_date]']; 	
	if($search['date_send1']!='_' && $search['date_send1']!='') list($date_send1_from, $date_send1_to) = explode(":", $search['date_send1']);
	if($search['date_send2']!='_' && $search['date_send2']!='') list($date_send2_from, $date_send2_to) = explode(":", $search['date_send2']);
	?>
		<div class="cell table_cell" style="border:0px; text-align:right;"><i>From</i></div>
		<div class="cell table_cell" style="border:0px; text-align:left;"><input type="text" name="date_send1[from_date]" size="10" value="<?php if(isset($date_send1_from)) echo $date_send1_from ?>" maxlength="100" class="date-pick" id="SubDateFrom"></div>
	</div>
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;"><i>To</i></div>
		<div class="cell table_cell" style="border:0px; text-align:left;"><input type="text" name="date_send1[to_date]" size="10" value="<?php if(isset($date_send1_to)) echo $date_send1_to ?>" maxlength="50" class="date-pick" id="SubDateTo"></div>
	</div>	
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;"><b>SMSC Sent Date</b></div>
		<div class="cell table_cell" style="border:0px; text-align:left;">&nbsp;</div>
	</div>
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;"><i>From</i></div>
		<div class="cell table_cell" style="border:0px; text-align:left;"><input type="text" name="date_send2[from_date]" size="10" value="<?php if(isset($date_send2_from)) echo $date_send2_from ?>" maxlength="100" class="date-pick" id="SubDateFrom2"></div>
	</div>
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;"><i>To</i></div>
		<div class="cell table_cell" style="border:0px; text-align:left;"><input type="text" name="date_send2[to_date]" size="10" value="<?php if(isset($date_send2_to)) echo $date_send2_to ?>" maxlength="50" class="date-pick" id="SubDateTo2"></div>
	</div>		
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;">SMSC Status</div>
		<?php $opsi = array(
                  '1' => 'Success',
                  '2' => 'DMS' ); ?>        
		<div class="cell table_cell" style="border:0px; text-align:left;"><?php echo form_dropdown("sms_status", $opsi,$search['sms_status']); ?></div>
	</div>
</div>
</td>
<td width="41%">
<!--pisah 
</div>
<div class="cell">
<!--pisah -->
<div style="display:inline; width:1%;">
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;"><b>Subscription</b></div>
		<div class="cell table_cell" style="border:0px; text-align:left;">&nbsp;</div>
	</div>	
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;">Category</div>
		<div class="cell table_cell" style="border:0px; text-align:left;"><?php echo form_dropdown("jobcat_id", $list_jobcat,$search['jobcat_id']); ?></div>
	</div>
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;">Type</div>
		<?php $opsi2 = array(
		          ''  => 'ALL',
                  'APP'  => 'APP',
                  'SMS'    => 'SMS' ); ?>   		
		<div class="cell table_cell" style="border:0px; text-align:left;"><?php echo form_dropdown("type", $opsi2,$search['type']); ?></div>        		
	</div>
	<div class="row">
    <div class="cell table_cell" style="border:0px; text-align:right;">Kotamadya</div>
    <div class="cell table_cell" style="border:0px; text-align:left;">
        <?php echo form_dropdown("seekerkodya", $list_location1,$search['seekerkodya']); ?>
    </div>
	</div>    
	
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;">Job Seeker Name</div>
		<div class="cell table_cell" style="border:0px; text-align:left;">
		<?php echo form_input("jobseeker_name", $search['jobseeker_name']); ?></div>
	</div>
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;">Job Seeker MDN</div>
		<div class="cell table_cell" style="border:0px; text-align:left;">
		<?php echo form_input("mdn", $search['mdn']); ?>
		</div>
	</div>	
</div>
</td>
<td width="25%">
<!--pisah 
</div>
<div class="cell">
<!--pisah -->
<div style="display:inline; width:1%;">
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;"><b>Job Info</b></div>
		<div class="cell table_cell" style="border:0px; text-align:left;"></div>
	</div>
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;">Category</div>
		<div class="cell table_cell" style="border:0px; text-align:left;"><?php echo form_dropdown("jobcat_id2", $list_jobcat,$search['jobcat_id2']); ?></div>
	</div>
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;">Company</div>
		<div class="cell table_cell" style="border:0px; text-align:left;"><?php echo form_dropdown("comp_id", $list_company,$search['comp_id']); ?></div>
	</div>
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;">Kotamadya</div>
		<div class="cell table_cell" style="border:0px; text-align:left;">
			<?php echo form_dropdown("jobkodya", $list_location1,$search['jobkodya']); ?>
		</div>
	</div>
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;">Title</div>
		<div class="cell table_cell" style="border:0px; text-align:left;"><?php echo form_input("job_title",$search['job_title']); ?></div>
	</div>	
</div>
</td>
</tr>
<tr>
<td>
<!--pisah 
</div>
</div>  </div>
<!--pisah -->
<div class="row">
	<div class="cell table_cell" style="border:0px; text-align:right;"><!--input type="hidden" name="status" value="<?php //echo $status; ?>"--></div>
	<div class="cell table_cell" style="border:0px; text-align:left;"><input type="submit" name="submit" value="View"></div>
</div>
</td>
</tr>
</table>


</form>