<?php if (!defined('BASEPATH')) exit('No direct access allowed.');
class Backend_model extends Model
{
	function Backend_model()
	{
		parent::Model();
	}

	function get_ads($row_count = 0, $offset = 0, $sort_order = 0)
	{
		$this->db->select('*');
		$this->db->from('list_ads');
		$this->db->order_by('date_added', "desc");
		$this->db->limit($row_count, $offset);
		$query = $this->db->get();

		$this->db->select('id');
		$this->db->from('list_ads');
		$count = $this->db->get();

		if ($query->num_rows() > 0)
		{
			return array('results' => $query->result(), 'count' => $count->num_rows());
		}
	}

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

	// NYTT: hämta valda kategorier för annons (ads_categories)
	function get_ad_categories($id)
	{
		$this->db->select('category_id');
		$this->db->from('ads_categories');
		$this->db->where('ad_id', (int)$id);
		$query = $this->db->get();

		$out = array();
		if ($query && $query->num_rows() > 0) {
			foreach($query->result() as $r){
				$out[] = (int)$r->category_id;
			}
		}
		return $out;
	}

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

	function get_all_schools($row_count = 0, $offset = 0, $sort_order = 0)
	{
		$this->db->order_by('title', "asc");
		$this->db->limit($row_count, $offset);
		$query = $this->db->get('list_schools');

		$this->db->select('id');
		$this->db->from('list_schools');
		$count = $this->db->get();

		if ($query->num_rows() > 0)
		{
			return array('results' => $query->result(), 'count' => $count->num_rows());
		}
	}

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

				$list .= '<option value="">' . $data->title . "</option>\n";
				$id = $data->id;

				$sql_check = "SELECT id, title FROM list_regions WHERE parent = '$id' ORDER BY sort_order ASC";
				$query2 = $this->db->query($sql_check);

				if($query2->num_rows() > 0) {
					foreach($query2->result() as $row) {

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