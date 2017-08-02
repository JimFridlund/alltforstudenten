<?php if (!defined('BASEPATH')) exit('No direct access allowed.');
/**
 * @package Studenten 2011
 * @copyright 2010, iDenta labs
 * @author Kristoffer Lidman
 */
class Ads_model extends Model 
{
	function Ads_model()
	{
		parent::Model();
	}
	
	// Hämta annonser i detta län
    function get_ads_to($region_id)
	{
		// Hämta kategorier
		$this->db->select('id');
		$this->db->select('title as category_title');
		$this->db->order_by('sort_order', "desc");
		$query = $this->db->get('list_categories');
		$main_array = array();
		
		foreach($query->result() as $row) {
			
			// Hämta företag i denna kategori
			$company = $this->_get_company($region_id, $row->id);
			
			
			
			// Bygg ihop arrayen
			$main_array[] = array(
				'category_title' => $row->category_title, 
				'company_id' => $company['company_id'],
				'sort_id' => "-".$company['sort_id_ad'],
				
				//För att aktivera efter standard
				//'sort_id' => "-".$company['company_id'],
				
				'company_title' => $company['company_title'],
				'text_info' => $company['text_info'],
				'logo' => $company['logo'],
				'tel' => $company['tel'], 
				'seller' => $company['seller'], 
				'date_expire' => $company['date_expire'], 
				'www' => $company['www'],
				'lati' => $company['lati'],
				'email' => $company['email'],
				'orderid' => $company['orderid'],
				'date_added' => $company['date_added'],
				'boka_text' => $company['boka_text'],
				'sort_id_ad' => "-".$company['sort_id_ad']
				
			
			);
		}

		// Skicka tillbaka en sorterad array
		return $this->_array_sort($main_array, 'sort_id', SORT_DESC);
	}
	
	// Hämta företag
	function _get_company($region_id, $category_id)
	{
		/*
		$this->db->select('tel, www, logo, lati');
		$this->db->select('id as company_id');
		$this->db->select('title as company_title');
		$this->db->where('region', $region_id);
		$this->db->where('category', $category_id);
		$this->db->limit(1);
		$query = $this->db->get('list_ads');
		*/

		$this->db->select('list_ads.tel, list_ads.boka_text, list_ads.sort_id_ad, list_ads.orderid, list_ads.date_added, list_ads.date_expire, list_ads.seller, list_ads.email, list_ads.www, list_ads.logo, list_ads.lati, list_ads.text_info');
		$this->db->select('list_ads.id as company_id');
		$this->db->select('list_ads.title as company_title');
		$this->db->from('list_ads');
		$this->db->where('list_ads.category', $category_id);
		$this->db->where('ads_relations.region_id', $region_id);
		$this->db->join('ads_relations', 'ads_relations.ad_id = list_ads.id');
		$this->db->order_by('list_ads.date_added', "asc");
		$this->db->limit(1);
		$query = $this->db->get();
		
		if ($query->num_rows() > 0)
		{
			$array = $query->result_array();
			return $array[0];
		}
	}
	
	// Sortera array
	function _array_sort($array, $on, $order=SORT_ASC)
	{
		$new_array = array();
		$sortable_array = array();

		if (count($array) > 0) {
			foreach ($array as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $k2 => $v2) {
						if ($k2 == $on) {
							$sortable_array[$k] = $v2;
						}
					}
				} else {
					$sortable_array[$k] = $v;
				}
			}

			switch ($order) {
				case SORT_ASC:
					asort($sortable_array);
				break;
				case SORT_DESC:
					arsort($sortable_array);
				break;
			}

			foreach ($sortable_array as $k => $v) {
				$new_array[$k] = $array[$k];
			}
		}

		return $new_array;
	}
}