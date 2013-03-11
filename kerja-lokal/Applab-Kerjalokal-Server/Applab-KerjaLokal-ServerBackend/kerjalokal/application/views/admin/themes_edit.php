<h3>Add Themes</h3>
<div class="table">
<?php
	echo form_open(base_url()."admin/themes/template/add");
	echo "<div class=\"row\">\n";
	echo "<div class=\"cell_key\">".form_label('New Themes', 'title')."</div>\n";
	echo "<div class=\"cell_val\">".form_input("title", "" ,"size=40 maxlength=100")."</div>\n";
	echo "</div>\n";
	
	echo "<div class=\"row\">\n";
	echo "<div class=\"cell_key\">".form_label('Creator', 'creator')."</div>\n";
	echo "<div class=\"cell_val\">".form_input("creator", "" ,"size=40 maxlength=100")."</div>\n";
	echo "</div>\n";
	
	echo "<div class=\"row\">\n";
	echo "<div class=\"cell_key\">".form_label('Description', 'desc')."</div>\n";
	echo "<div class=\"cell_val\">".form_textarea("desc", "")."</div>\n";
	echo "</div>\n";
	
	echo "<div class=\"row\">\n";
	echo "</div>\n</div>\n";
	//echo form_hidden("creator", "yudha");
	echo form_hidden("country_id", "ID");
	echo form_hidden("is_active", "1");
	echo form_hidden("is_current", "0");	
	echo form_submit("submit", "Submit");
	echo form_close();
?>

<hr>
<h3>Manage Themes</h3>
<?php echo form_open(base_url()."admin/themes/template/update"); ?>
<div class="table">
<div class="row">
	<div class="cell table_head" style="width:50px">ID</div>
	<div class="cell table_head">Name</div>
	<div class="cell table_head">Status</div>
	<div class="cell table_head">Delete</div>
</div>
<?php	
	$i = 1;
	foreach ($results as $industry)
	{
		echo "<div class=\"row\">\n";
		echo "<div class=\"cell table_cell\">".$i++."</div>\n";
		echo "<div class=\"cell table_cell\">".$industry["name"]."</div>\n";		
		$data = array(
			'name'        => 'is_current',
			'id'          => $industry["id"],
			'value'       => $industry["id"],
			'checked'	  => ($industry["is_current"] == "1") ? " checked":"",
		);
		echo "<div class=\"cell table_cell\">".form_radio($data)."</div>\n";
		echo "<div class=\"cell table_cell\"><a href=\"".base_url()."admin/themes/template/delete/".$industry["id"]."\">Delete</a></div>\n";
		echo "</div>\n";
		
	}
	echo "</div>";
	echo form_submit("submit", "Submit");
	echo form_close();
?>
</div>