<?php if (!defined('BASEPATH')) exit('No direct access allowed.');
/**
 * @package Studenten 2011
 * @copyright 2010, iDenta labs
 * @author Kristoffer Lidman
 */
class Region_model extends Model 
{
	function Region_model()
	{
		parent::Model();
	}
	
	// Hämta län
	function get_regions()
	{
		$this->db->select('*');
		$this->db->from('list_regions');
		$this->db->where('parent', 0);
		$this->db->order_by('sort_order', "asc");
		$query = $this->db->get();
		
		if ($query->num_rows() > 0)
		{
			return $query->result();
		} 
	}
	
	// Hämta enskilt län via permalänk
	function get_region_by_url($url)
	{
		$this->db->select('*');
		$this->db->from('list_regions');
		$this->db->where('parent', 0);
		$this->db->where('permalink', $url);
		$this->db->limit(1);
		$query = $this->db->get();
		
		if ($query->num_rows() > 0)
		{
			return $query->result();
		} 
	}
	
	// Hämta enskild kommun via permalänk
	function get_kommun_by_url($url)
	{
		$this->db->select('*');
		$this->db->from('list_regions');
		$this->db->where('permalink', $url);
		$this->db->limit(1);
		$query = $this->db->get();
		
		if ($query->num_rows() > 0)
		{
			return $query->result();
		} 
	}
	
	// Hämta kommuner till ett visst län
    function get_kommuner_to($id)
	{
		$this->db->where('parent', $id);
		$this->db->order_by('sort_order', "asc");
		$query = $this->db->get('list_regions');
		
		if ($query->num_rows() > 0)
		{
			return $query->result();
		}
	}
	
	// Hämta kommun via ID
    function get_kommun_by_id($id)
	{
		$this->db->where('id', $id);
		$this->db->limit(1);
		$query = $this->db->get('list_regions');
		
		if ($query->num_rows() > 0)
		{
			return $query->result();
		}
	}
	
	// Hämta skolor i en viss kommun
    function get_schools($id)
	{
		$this->db->where('parent_id', $id);
		$this->db->order_by('title', "asc");
		$query = $this->db->get('list_schools');
		
		if ($query->num_rows() > 0)
		{
			return $query->result();
		}
	}
	
	// Sök efter kommun/skola
	function get_search()
	{
		$this->db->select('*');
		$this->db->select('list_regions.title as region_title');
		$this->db->where('list_regions.parent !=', 0);
		
		$s = trim($this->input->post('s_value'));
		$searchTerms = explode(' ', $s);
		if(is_array($searchTerms) AND count($searchTerms) > 1) {
			$first = array_shift($searchTerms);
			$this->db->like('list_regions.title', $first);
		
			foreach($searchTerms as $term) {
				$term = trim($term);
				if(!empty($term)) {
					$this->db->or_like('list_regions.title', $term);
					$this->db->or_like('list_schools.title', $term);
				}
			}
		} else {
			$this->db->like('list_regions.title', $this->input->post('s_value'));
			$this->db->or_like('list_schools.title', $this->input->post('s_value'));
		}
		
		// Kommun
		//$this->db->like('list_regions.title', $this->input->post('s_value'));
		
		// Skola
		//$this->db->or_like('list_schools.title', $this->input->post('s_value'));

		$this->db->from('list_regions');
		$this->db->join('list_schools', 'list_schools.parent_id = list_regions.id', 'left');
		$this->db->group_by('list_regions.title');
		$this->db->order_by('list_regions.sort_order', "desc");
		$query = $this->db->get();
		
		// Skicka resultat
		if ($query->num_rows() > 0)
		{
			return $query->result();
		} 
	}
}