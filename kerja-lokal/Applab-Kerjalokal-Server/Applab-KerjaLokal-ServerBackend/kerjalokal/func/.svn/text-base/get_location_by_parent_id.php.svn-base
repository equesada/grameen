<?php
require "conf.php";
$id = isset($_GET["_value"]) ? $_GET['_value'] : "";
$area[0] = "--";

if ($id != "")
{
	$loc = file_get_contents($core_url."get_location_by_parent_id.php?id=$id");
	$loc = json_decode($loc, true);
	if (sizeof($loc) > 0)
	{
		foreach ($loc as $a)
		{ 
			if ($a["loc_type"] != "KELURAHAN")
				$area[$a["loc_id"]] = $a["name"]; 
			else
				$area[$a["loc_id"]] = $a["name"]; 
		}
	}
}
echo "[".json_encode($area)."]";

?>