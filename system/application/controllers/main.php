<?php
/**
 * @package Studenten 2011
 * @copyright 2010, iDenta labs
 * @author Kristoffer Lidman
 */
class Main extends Controller {

	function Main()
	{
		parent::Controller();
		
		$this->load->model('region_model');
	}
	
	function index()
	{
		$this->sidan();
	}
	
	function sidan()
	{
		// Hämta information
		$data['lan'] = $this->region_model->get_regions();
		
		$data['meta_title'] = $this->settings->option['meta_title'];
		$data['meta_desc'] = $this->settings->option['meta_desc'];
		$data['meta_kw'] = $this->settings->option['meta_kw'];
		
		// Visa sidan
		$this->load->view('view_frontpage', $data);
	}
}
?>