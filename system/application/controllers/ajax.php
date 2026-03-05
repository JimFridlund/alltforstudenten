<?php
class Ajax extends Controller
{
	function Ajax()
	{
		parent::Controller();
	}

	function populate_kommun()
	{
		$id = (int)$this->uri->segment(3);
		if(empty($id) OR !is_numeric($id)) exit("fel");

		$sql_check = "SELECT id, title FROM list_regions WHERE parent = '$id' ORDER BY sort_order ASC";
		$query = $this->db->query($sql_check);

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
				echo "<option value=\"".$row->id."\">".$row->title."</option>\n";
			}
		}
	}

	// NYTT: JSON-lista med kommuner i ett län
	function kommun_list()
	{
		$lan_id = (int)$this->uri->segment(3);
		if(empty($lan_id)) {
			$this->_json(array('ok'=>false,'error'=>'missing lan_id'));
			return;
		}

		$sql = "SELECT id, title FROM list_regions WHERE parent = ? ORDER BY sort_order ASC";
		$q = $this->db->query($sql, array($lan_id));

		$out = array();
		if($q && $q->num_rows() > 0){
			foreach($q->result() as $r){
				$out[] = array('id'=>(int)$r->id, 'title'=>$r->title);
			}
		}

		$this->_json(array('ok'=>true,'items'=>$out));
	}

	// NYTT: JSON-lista med ALLA kommuner i Sverige
	function kommun_list_all()
	{
		$sql = "SELECT id, title FROM list_regions WHERE parent <> 0 ORDER BY parent ASC, sort_order ASC";
		$q = $this->db->query($sql);

		$out = array();
		if($q && $q->num_rows() > 0){
			foreach($q->result() as $r){
				$out[] = array('id'=>(int)$r->id, 'title'=>$r->title);
			}
		}

		$this->_json(array('ok'=>true,'items'=>$out));
	}

	function _json($arr)
	{
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($arr);
	}
}
?>