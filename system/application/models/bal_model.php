<?php

class Bal_model extends Model {

function Bal_model()
{
parent::Model();
}

function get_category_by_slug($slug)
{

$query = $this->db->query("
SELECT *
FROM bal_categories
WHERE permalink = ?
LIMIT 1
", array($slug));

if($query->num_rows() == 0) return false;

return $query->row();

}


function get_ads_by_category($category_id)
{

$query = $this->db->query("
SELECT *
FROM bal_ads
WHERE category = ?
ORDER BY sort_id_ad DESC
", array($category_id));

return $query->result();

}

}