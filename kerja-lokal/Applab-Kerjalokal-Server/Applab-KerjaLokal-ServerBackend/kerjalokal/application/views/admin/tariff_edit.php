
<?php
	echo form_open($form_action);
	echo "<div class='row'><div class='cell_key'>".form_label('Area prefix', 'tarif')."</div>\n";
	echo "<div class='cell_val'>".form_input("tarif", $tarif ,"size=40 maxlength=100").form_error('tarif', '<div class="form_error" style="color:red">', '</div>')."</div></div>\n";
	echo "<div class='row'><div class='cell_key'></div><div class='cell_val'>".form_submit("submit", "Submit")."</div>";
	echo form_close();
?>
