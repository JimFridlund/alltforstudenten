<?php
class Ads_model extends Model
{
	function Ads_model()
	{
		parent::Model();
	}

	function get_ads_to($region_id = 0, $type = "")
	{
		if(empty($type) OR empty($region_id)) return FALSE;

		if($type == "studenten") {
			$sql_cat = "SELECT id, title FROM list_categories ORDER BY sort_order ASC";
		} else {
			$sql_cat = "SELECT id, title FROM bal_categories ORDER BY sort_order ASC";
		}

		$query = $this->db->query($sql_cat);

		if($query->num_rows() > 0) {
			foreach($query->result_array() as $data) {

				$category_id = $data['id'];

				$ad = $this->_get_company($region_id, $type, $category_id);

				$result[] = array(
					'category_title' => $data['title'],
					'category_id' => $data['id'],
					'company_title' => $ad[0],
					'company_id' => $ad[1],
					'logo' => $ad[2],
					'tel' => $ad[3],
					'www' => $ad[4],
					'text_info' => $ad[5],
					'sort_id_ad' => $ad[6],
					'orderid' => $ad[7],
					'seller' => $ad[8],
					'boka_text' => $ad[9],
					'date_expire' => $ad[10]
				);
			}
		}

		return $result;
	}

	function _get_company($region_id = 0, $type = "", $category_id = 0)
	{
		if($type == "studenten") {

			// NYTT: koppla via ads_categories istället för list_ads.category
			$sql = "
				SELECT
					a.id,
					a.title,
					a.logo,
					a.adress,
					a.postnr,
					a.ort,
					a.tel,
					a.www,
					a.text_info,
					a.sort_id_ad,
					a.orderid,
					a.seller,
					a.boka_text,
					a.date_expire
				FROM list_ads a
				INNER JOIN ads_relations r ON r.ad_id = a.id
				INNER JOIN ads_categories ac ON ac.ad_id = a.id
				WHERE r.region_id = ?
				  AND ac.category_id = ?
				ORDER BY a.sort_id_ad DESC
				LIMIT 1
			";
			$query = $this->db->query($sql, array((int)$region_id, (int)$category_id));

		} else {

			$sql_check = "SELECT id, title, logo, adress, postnr, ort, tel, www, text_info, sort_id_ad, orderid, seller, boka_text, date_expire
						  FROM bal_ads
						  WHERE id_region = '$region_id' AND category = '$category_id'
						  ORDER BY sort_id_ad DESC
						  LIMIT 1";
			$query = $this->db->query($sql_check);
		}

		$company = ""; $id = ""; $logo = ""; $tel = ""; $www = ""; $text_info = ""; $sort_id_ad = ""; $orderid = ""; $seller = ""; $boka_text = ""; $date_expire = "";

		if($query->num_rows() > 0) {
			foreach($query->result() as $ad) {
				$id = $ad->id;
				$company = $ad->title;
				$logo = $ad->logo;
				$tel = $ad->tel;
				$www = $ad->www;
				$text_info = $ad->text_info;
				$sort_id_ad = $ad->sort_id_ad;
				$orderid = $ad->orderid;
				$seller = $ad->seller;
				$boka_text = $ad->boka_text;
				$date_expire = isset($ad->date_expire) ? $ad->date_expire : "";
			}
		}

		return array($company, $id, $logo, $tel, $www, $text_info, $sort_id_ad, $orderid, $seller, $boka_text, $date_expire);
	}
}
?>