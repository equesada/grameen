<?php
require "conf.php";
$id = $_GET["_value"];
$loc = file_get_contents($base_url."ik_core/get_location_by_loc_id.php?id=$id");
echo $loc;

?>