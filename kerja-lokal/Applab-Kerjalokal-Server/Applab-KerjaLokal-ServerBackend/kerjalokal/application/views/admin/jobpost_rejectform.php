<?php
	//$list_jobcat[0] = "ALL";
	//foreach ($jobcats["results"] as $a)
	//{ $list_jobcat[$a["jobcat_id"]] = $a["jobcat_title"]; }
	//<form action="<?php echo base_url(); ? >admin/jobpost/manage" method="post">
?>
<html>
<head>
</head>
<body>
<?php echo form_open($form_submit)."\n";?>
<div style="display:table; width:100%;">
<div class="row">
<div class="cell table_cell" style="border:0px; text-align:left;">Anda Akan Mereject Job ini </div>
</div>
<div class="row">
<div class="cell table_cell" style="border:0px; text-align:left;"><textarea name="reason" cols="20" rows="3" style="width:95%;border: 3px solid #cccccc;padding: 5px;"></textarea></div>
</div>
<input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
<input type="hidden" name="jobposter_id" value="<?php echo $jobposter_id; ?>">
<div class="row">
<div class="cell table_cell" style="border:0px; text-align:right;"><a href=# class=close style='padding: 6px;border:1px solid;background-color:#ccc'>Cancel</a>&nbsp;&nbsp;<input type="submit" name="submit" value="Reject"></div>
</div>
</div>
</body>
</html>