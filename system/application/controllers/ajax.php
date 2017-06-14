<?php
/**
 * @package Studenten 2011
 * @copyright 2010, iDenta labs
 * @author Kristoffer Lidman
 */
class Ajax extends Controller {

	function Ajax()
	{
		parent::Controller();
		
		// Make sure this is an ajax-call
		if (!IS_AJAX) { redirect(''); }
	}
	
	function index()
	{
		redirect('');
	}
	
	// Generera karta
	function generate_map()
	{
		// Vi ska hämta koordinaterna till denna annons
		$id = $this->input->post('id');
		$id = explode("_", $id);
		$id = $id[1];
		
		// Hämta
		$sql_check = "SELECT lati, longi FROM list_ads WHERE id = '$id' LIMIT 1";
		$query = $this->db->query($sql_check);
		
		if($query->num_rows() > 0) {
			$row = $query->row();
			echo $row->lati .'<|>'. $row->longi;
		} else {
			echo "";
		}
	}
	
	// Visa enkel lista med kommuner som en annons visas i
	function list_regions_to()
	{
		$id = $this->input->post('id');
		$id = explode("_", $id);
		$id = $id[1];
		$str = "";
		
		// Ingen vald?
		if($id == "") {
			echo "";
		} else {
			
			$sql_check = "SELECT region_id FROM ads_relations WHERE ad_id = '$id' ORDER BY sort_order ASC";
			$query = $this->db->query($sql_check);
			
			if($query->num_rows() > 0) {
				
				foreach($query->result() as $row){
					$str .= '<li>' . $this->studenten->get_kommun_name($row->region_id) . '</li>' . "\n";
				}
				
			}
			
			// Skicka data
			echo $str;
		}
	}
	
	// Populera kommunlista efter man valt län
	function populate_kommun()
	{
		$id = $this->input->post('id');
		$str = '<option value="" id="del">Ingen kommun vald</option>';
		
		// Ingen vald?
		if($id == "") {
			echo '<option value="" id="del">Ingen kommun vald</option>';
		} else {
			
			$sql_check = "SELECT title, id FROM list_regions WHERE parent = '$id' ORDER BY sort_order ASC";
			$query = $this->db->query($sql_check);
			
			if($query->num_rows() > 0) {
				
				foreach($query->result() as $row){
					$str .= '<option value="'.$row->id .'">' . $row->title . '</option>' . "\n";
				}
				
			} else {
				$str = '<option value="" id="del">Ingen kommun vald</option>';
			}
			
			// Skicka data
			echo $str;
		}
	}

	// Flytta kategori
	function category_order()
	{
		// Loopa
		foreach($this->input->post('item') as $key => $value) 
		{
			$data = array(
				'sort_order' => $key
			);
			
			// Update order
			$this->db->where('id', $value);
			$this->db->update('list_categories', $data);
		}
	}
	
	// Flytta län
	function lan_order()
	{
		// Loopa
		foreach($this->input->post('item') as $key => $value) 
		{
			$data = array(
				'sort_order' => $key
			);
			
			// Update order
			$this->db->where('id', $value);
			$this->db->update('list_regions', $data);
		}
	}
	
	// Flytta kommun
	function kommun_order()
	{
		// Loopa
		foreach($this->input->post('item') as $key => $value) 
		{
			$data = array(
				'sort_order' => $key
			);
			
			// Update order
			$this->db->where('id', $value);
			$this->db->update('list_regions', $data);
		}
	}
}