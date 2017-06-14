<?php if (!defined('BASEPATH')) exit('No direct access allowed.');
/**
 * @package Studenten 2011
 * @copyright 2010, iDenta labs
 * @author Kristoffer Lidman
 */
class Backend_model extends Model 
{
	function Backend_model()
	{
		parent::Model();
	}

	// Hämta alla annonser
	function get_ads($row_count = 0, $offset = 0, $sort_order = 0)
	{
		$this->db->select('*');
		$this->db->from('list_ads');
		$this->db->order_by('date_added', "desc");
		$this->db->limit($row_count, $offset);
		$query = $this->db->get();
		
		// Räkna antalet resultat utan limit (count)
		$this->db->select('id');
		$this->db->from('list_ads');
		$count = $this->db->get();
		
		if ($query->num_rows() > 0)
		{
			return array('results' => $query->result(), 'count' => $count->num_rows());
		} 
	}
	
	// Hämta enskild annons via ID
    function get_ad_by_id($id)
	{
		$this->db->where('id', $id);
		$this->db->limit(1);
		$query = $this->db->get('list_ads');
		
		if ($query->num_rows() > 0)
		{
			return $query->result();
		}
	}
	
	// Hämta relationer till en annons
    function get_relations_to($id)
	{
		$this->db->select('*');
		$this->db->where('ad_id', $id);
		$this->db->from('ads_relations');
		$this->db->order_by('sort_order', "asc");
		$query = $this->db->get();
		
		if ($query->num_rows() > 0)
		{
			return $query->result();
		}
	}
	
	// Hämta kategorier
	function get_categories()
	{
		$this->db->select('*');
		$this->db->from('list_categories');
		$this->db->order_by('sort_order', "asc");
		$query = $this->db->get();
		
		if ($query->num_rows() > 0)
		{
			return $query->result();
		} 
	}
	
	// Hämta enskild kategori via ID
    function get_category_by_id($id)
	{
		$this->db->where('id', $id);
		$this->db->limit(1);
		$query = $this->db->get('list_categories');
		
		if ($query->num_rows() > 0)
		{
			return $query->result();
		}
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
	
	// Hämta enskilt län via ID
    function get_region_by_id($id)
	{
		$this->db->where('id', $id);
		$this->db->limit(1);
		$query = $this->db->get('list_regions');
		
		if ($query->num_rows() > 0)
		{
			return $query->result();
		}
	}
	
	// Hämta parent-län till kommun
    function get_region_by_child($id)
	{
		$this->db->where('id', $id);
		$this->db->limit(1);
		$query = $this->db->get('list_regions');
		
		if ($query->num_rows() > 0)
		{
			$row = $query->row(); 
			return $row->parent;
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
	
	// Hämta alla skolor
    function get_all_schools($row_count = 0, $offset = 0, $sort_order = 0)
	{
		$this->db->order_by('title', "asc");
		$this->db->limit($row_count, $offset);
		$query = $this->db->get('list_schools');
		
		// Räkna antalet resultat utan limit (count)
		$this->db->select('id');
		$this->db->from('list_schools');
		$count = $this->db->get();
		
		if ($query->num_rows() > 0)
		{
			return array('results' => $query->result(), 'count' => $count->num_rows());
		} 
	}
	
	// Hämta skola via ID
    function get_school_by_id($id)
	{
		$this->db->where('id', $id);
		$this->db->limit(1);
		$query = $this->db->get('list_schools');
		
		if ($query->num_rows() > 0)
		{
			return $query->result();
		}
	}
	
	// Hämta län och kommuner
	function get_lan_kommuner($selected = "")
	{
		$this->db->select('id, title');
		$this->db->from('list_regions');
		$this->db->where('parent', 0);
		$this->db->order_by('sort_order', "asc");
		$query = $this->db->get();
		$list = ""; $make_selection = "";
		
		if ($query->num_rows() > 0) {
			foreach($query->result() as $data) {
			
				// Bygg
				$list .= '<option value="">' . $data->title . "</option>\n";
				$id = $data->id;
				
				// Hämta kommuner
				$sql_check = "SELECT id, title FROM list_regions WHERE parent = '$id' ORDER BY sort_order ASC";
				$query2 = $this->db->query($sql_check);
				
				if($query2->num_rows() > 0) {
					foreach($query2->result() as $row) {
						
						// Ska vi markera en kommun som selected?
						if($selected != "" AND $selected == $row->id) {
							$make_selection = ' selected="selected"';
						}
						
						$list .= '<option value="'. $row->id .'"'.$make_selection.'>--- ' . $row->title . "</option>\n";
						$make_selection = "";
					}
				}
			}
		}
		
		return $list;
	}
}