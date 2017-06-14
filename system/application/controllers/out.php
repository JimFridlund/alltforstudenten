<?php
/**
 * @package Studenten 2011
 * @copyright 2010, iDenta labs
 * @author Kristoffer Lidman
 */
class Out extends Controller {

	function Out()
	{
		parent::Controller();
	}
	
	// Håll dig borta
	function index()
	{
		$this->send();
	}
	
	// Skicka vidare
	function send()
	{
		// Ladda GET
		parse_str(substr(strrchr($_SERVER['REQUEST_URI'], "?"), 1), $_GET);
		
		// Plocka ihop diverse vars
		if(isset($_GET['url'])) { $url = $this->input->xss_clean($_GET['url']); }
		
		// Kolla url
		if(empty($url)) {
			exit('No URL supplied.');
		} else {
			//redirect($url.'/?utm_source=studenten2011.nu&utm_medium=redirect&utm_campaign=Studenten2011');
			$this->_add_view($url);
			
			redirect($url);
		}
	}
	
	function _add_view($url)
	{
		$sql = "UPDATE list_schools SET clicks=clicks+1 WHERE url='$url'";
		$this->db->query($sql);
	}
}