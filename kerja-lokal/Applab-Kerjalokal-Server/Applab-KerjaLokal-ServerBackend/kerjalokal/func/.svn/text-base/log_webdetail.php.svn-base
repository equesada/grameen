<?php require "conf.php"; ?>
<html>
<head>
</head>
<body style="margin:0px; font-family:Courier New">
<div style="text-align:left; width:600px; height:400px; overflow:scroll;">
<center><a href="#" class="close">close</a><br><br></center>
<?php
	$webdms = isset($_GET["webdms"]) ? $_GET["webdms"] : "web";
	$id = isset($_GET["id"]) ? $_GET["id"] : 0;
	$json = $core_url."get_log_by_log_id.php?webdms=$webdms&country_id=".$_SESSION["curr_country"]."&id=$id";
	$data = file_get_contents($json);
	//echo $data;
	$data = json_decode($data, TRUE);
	//echo "<pre>"; print_r($data); echo "</pre>";
?>
<b>URL : </b><br>
<?php echo $base_url.$data["filename"]."?".$data["param"]; ?><hr>
<b>JSON : </b><br>
<?php echo $data["response"]; ?><hr>

<?php echo "<pre>"; print_r(json_decode($data["response"], TRUE)); echo "</pre>";?>
<center><a href="#" class="close">close</a><br><br></center>
</div>
</body>
</html>