<?php
class Mmain extends CI_Model{
	
	function __construct()
	{
		// Call the Model constructor
		parent::__construct();

		$result = file_get_contents(CORE_URL."get_theme_current.php");
		$result = json_decode($result, true);
		
		//print_r($result);
		
		$theme['template'] = "themes/".$result["folder"]."/main.php";
		$theme['parser'] = "parser";
		$theme['parser_method'] = "parse";
		$theme['parse_template'] = TRUE;
		$theme_details = file_get_contents(base_url()."themes/".$result["folder"]."/theme_details.xml");
		$theme_details = simplexml_load_string($theme_details);
		$positions = (array)$theme_details->positions;
		
		$theme["regions"] = array();
		
		foreach ($positions["position"] as $pos)
		{ array_push($theme["regions"],$pos); }
		$this->template->add_template($result["folder"], $theme, TRUE);
		$this->template->set_template($result["folder"]);

	}
	
}
?>