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
<form action="<?php echo $form_submit; ?>" method="post">
<table style="border:solid; width:83%">
<tr>
<td style="width:34%">

<div style="display:inline; width:30%;">
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;"><b>Date</b></div>
		<div class="cell table_cell" style="border:0px; text-align:left;">&nbsp;</div>
	</div>
	<div class="row">
    <?php //if($search['date_send1']['from_date']!='_') echo $search['date_send1']['[to_date]']; 	
	if($search['date']!='_' && $search['date']!='') list($date_send1_from, $date_send1_to) = explode(":", $search['date']);	
	?>
		<div class="cell table_cell" style="border:0px; text-align:right;"><i>From</i></div>
		<div class="cell table_cell" style="border:0px; text-align:left;"><input type="text" name="date[from_date]" size="10" value="<?php if(isset($date_send1_from)) echo $date_send1_from ?>" maxlength="100" class="date-pick" id="SubDateFrom"></div>
	</div>
	<div class="row">
		<div class="cell table_cell" style="border:0px; text-align:right;"><i>To</i></div>
		<div class="cell table_cell" style="border:0px; text-align:left;"><input type="text" name="date[to_date]" size="10" value="<?php if(isset($date_send1_to)) echo $date_send1_to ?>" maxlength="50" class="date-pick" id="SubDateTo"></div>
	</div>	
</div>
<div class="row">
	<div class="cell table_cell" style="border:0px; text-align:right;"><!--input type="hidden" name="status" value="<?php //echo $status; ?>"--></div>
	<div class="cell table_cell" style="border:0px; text-align:left;"><input type="submit" name="submit" value="View"></div>
</div>
</td>
</tr>
</table>
</form>