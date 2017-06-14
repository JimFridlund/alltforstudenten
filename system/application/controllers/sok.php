<?php
/**
 * @package Studenten 2011
 * @copyright 2010, iDenta labs
 * @author Kristoffer Lidman
 */
class Sok extends Controller {

	function Sok()
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
		// Spara sökfrasen i en cookie
		if($_POST AND $this->input->post('s_value') != "Skola eller kommun") {
			$this->session->set_userdata('fritext', $this->input->post('s_value'));
		}
		
		// Hämta länen
		$data['lan'] = $this->region_model->get_regions();
		
		// Till view
		$data['s'] = ($this->input->post('s_value') == ""  ? $this->session->userdata('fritext') : $this->input->post('s_value'));
		$data['result'] = $this->region_model->get_search();
		
		// SEO
		$data['meta_title'] = "Sökresultat för ".$data['s'];
		$data['meta_desc'] = "Din sökning för strängen ".$data['s'];
		$data['meta_kw'] = "";
		
		// Visa sidan
		$this->load->view('view_search', $data);
	}
}
?>