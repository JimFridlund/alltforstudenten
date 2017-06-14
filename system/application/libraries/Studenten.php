<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
/**
 * @package Studenten
 * @copyright 2010, iDenta labs
 * @author Kristoffer Lidman
 */
class Studenten {
    
	var $CI;

    function Studenten()
    {
        $this->CI =& get_instance();

        // Ladda diverse nödvändigt
        $this->CI->load->database();
    }
	
	// Räkna hur många kommuner ett län har
	function count_kommuner($id)
	{
		$sql_check = "SELECT 'parent', COUNT(parent) AS total FROM list_regions WHERE parent = '$id'";
		$query = $this->CI->db->query($sql_check);
		
		// Skapa lista
		if($query->num_rows() > 0) {
			$row = $query->row(); 
			return $row->total;
		} else {
			return "0";
		}
	}
	
	// Räkna hur många kommuner en annons ligger i
	function count_kommuner_to_ad($id)
	{
		$sql_check = "SELECT 'region_id', COUNT(region_id) AS total FROM ads_relations WHERE ad_id = '$id'";
		$query = $this->CI->db->query($sql_check);
		
		// Skapa lista
		if($query->num_rows() > 0) {
			$row = $query->row(); 
			return $row->total;
		} else {
			return "0";
		}
	}
	
	// Hämta en kommuns namn
	function get_kommun_name($id)
	{
		$sql_check = "SELECT title FROM list_regions WHERE id = '$id' LIMIT 1";
		$query = $this->CI->db->query($sql_check);
		
		if($query->num_rows() > 0) {
			$row = $query->row(); 
			return $row->title;
		} else {
			return "Saknas";
		}
	}
	
	// Hämta första kategorirelationen
	function get_first_relation($id)
	{
		$sql = "SELECT list_regions.title FROM ads_relations LEFT JOIN list_regions ON ads_relations.region_id = list_regions.id WHERE ads_relations.ad_id = '$id' ORDER BY ads_relations.sort_order ASC LIMIT 1";
		$query = $this->CI->db->query($sql);
		
		if($query->num_rows() > 0) {
			$row = $query->row();
			return $row->title;
		} else {
			return "Saknas";
		}
	}
	
	// Hämta en läns namn via kommun
	function get_kommun_parent_name($id)
	{
		$sql_check = "SELECT parent FROM list_regions WHERE id = '$id' LIMIT 1";
		$query = $this->CI->db->query($sql_check);
		
		if($query->num_rows() > 0) {
			$row = $query->row();
			
			$parent = $row->parent;
			
			$sql = "SELECT title FROM list_regions WHERE id = '$parent' LIMIT 1";
			$query2 = $this->CI->db->query($sql);
			
			if($query2->num_rows() > 0) {
				$row2 = $query2->row();
				
				return $row2->title;
			} else {
				return "Saknas";
			}
		} else {
			return "Saknas";
		}
	}
	
	// Hämta ett läns permalink via kommun
	function parent_permalink($parent)
	{
		$sql_check = "SELECT permalink FROM list_regions WHERE id = '$parent' LIMIT 1";
		$query = $this->CI->db->query($sql_check);
		
		if($query->num_rows() > 0) {
			$row = $query->row(); 
			return $row->permalink;
		}
	}
	
	// Hämta ett läns id via kommun
	function get_parent_id($child)
	{
		$sql_check = "SELECT parent FROM list_regions WHERE id = '$child' LIMIT 1";
		$query = $this->CI->db->query($sql_check);
		
		if($query->num_rows() > 0) {
			$row = $query->row(); 
			return $row->parent;
		}
	}
	
	// Hämta ett kommun + markera vald
	function get_kommun_list($lan, $id)
	{
		$str = ""; $selected = ' selected="selected"';
		
		$sql_check = "SELECT id, title FROM list_regions WHERE parent = '$lan' ORDER BY sort_order ASC";
		$query = $this->CI->db->query($sql_check);
		
		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
				if($row->id == $id) { 
					$str .= '<option value="'.$row->id .'" selected="selected">' . $row->title . '</option>' . "\n";
				} else {
					$str .= '<option value="'.$row->id .'">' . $row->title . '</option>' . "\n";
				}
			}
		} else {
			$str = "";
		}
		
		return $str;
	}
	
	// Plocka fram lat och lang till adress. (t.ex $geocode->Latitude)
	Function geocode($address)
	{
		// URL-decoda
		$address = urlencode($address);
		
		// Bygg URL för api
		$url = 'http://maps.google.com/maps/api/geocode/json?sensor=false&address='.$address;
		
		// Kör hårt
		$xmlstr = file_get_contents($url);
		$xmlstr = json_decode($xmlstr, true);
		$xmlstr = $xmlstr['results'][0]['geometry']['location'];
		
		$result = array(
			'latitude' => $xmlstr['lat'],
			'longitude' => $xmlstr['lng']
		);

		return $result;
	}
	
	// Hämta marginal som används som subsitut till vertical-align
	function vertical_align($height, $box_height)
	{
		$margin = ($box_height - $height) / 2;
		
		return round($margin);
	}
	
	// Skala om bilder
	function resize_dimensions($goal_width,$goal_height,$width,$height) {
		$return = array('width' => $width, 'height' => $height);
	   
		// If the ratio > goal ratio and the width > goal width resize down to goal width
		if ($width/$height > $goal_width/$goal_height && $width > $goal_width) {
			$return['width'] = $goal_width;
			$return['height'] = $goal_width/$width * $height;
		}
		// Otherwise, if the height > goal, resize down to goal height
		else if ($height > $goal_height) {
			$return['width'] = $goal_height/$height * $width;
			$return['height'] = $goal_height;
		}
	   
		return $return;
	} 
	
	// Generera en snygg permalänk
	function make_permalink($str)
    {
		$separator = '-';
		$str = strtolower(htmlentities($str, ENT_COMPAT, 'UTF-8'));
		$str = preg_replace('/&(.)(acute|cedil|circ|grave|ring|tilde|uml);/', "$1", $str);
		$str = preg_replace('/([^a-z0-9]+)/', $separator, html_entity_decode($str, ENT_COMPAT, 'UTF-8'));
		$str = trim($str, $separator);
		
		return $str;
	}
	
	// Korta ner en text till x antal tecken
	function shorten($string, $chars = 10)
    { 
		if(strlen($string) >= $chars) {
			return mb_substr($string, 0, $chars,'UTF-8')."&hellip;"; 
		} else {
			return $string;
		}
	}
	
	// Generera ett lösenord
	function generate_password($l = 8)
	{
		$rand = "";
		$c = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";  
			srand((double)microtime()*1000000);  
			for($i=0; $i<$l; $i++) {  
			$rand.= $c[rand()%strlen($c)];  
		}  
		
		return $rand;  
	}
	
	// Kolla om det är en URL
	function is_url($url)
	{
		return ( ! preg_match('/^(http|https|ftp):\/\/([A-Ö0-9][A-Ö0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $url)) ? FALSE : TRUE;
	}
	
	// Generera en brödsmula
	function make_breadcrumb()
	{
		// Samla parametrar
		$base = $this->CI->uri->segment(1);
		$start = '<div><a href="'.base_url().'" title="Startsidan">Hem</a></div>';
		$divider = '<img src="'.base_url().'public/images/arrow_bc.png" alt="" />';
		$crumb = "";
		
		// Kolla så inte ID är tom
		if(empty($base)) {
			exit("fel");
		}
		
		// Ta reda på var vi börjar
		switch ($base) {
		
			// Det kommun & län-sida
			case 'visa':
			
				$lan = $this->CI->uri->segment(2);
				$kommun = $this->CI->uri->segment(3);
				
				$sql_check = "SELECT title, permalink FROM list_regions WHERE permalink = '$lan' LIMIT 1";
				$query = $this->CI->db->query($sql_check);
				
				if($query->num_rows() > 0) {
					$row = $query->row();
					
					
					// Har vi kommun också?
					if($kommun) {
					
						$sql_check2 = "SELECT title FROM list_regions WHERE permalink = '$kommun' LIMIT 1";
						$query2 = $this->CI->db->query($sql_check2);
						
						if($query2->num_rows() > 0) {
							$row2 = $query2->row();
							
							// Bygg ihop resten av smulan
							$crumb .=  $divider .  '<div><a href="'.base_url().'visa/'. $row->permalink .'">' . $row->title . '</a></div>' . $divider . '<div>' . $row2->title . '</div>';
						}
					
					} else {
					
						// Bygg ihop resten av smulan
						$crumb .=  $divider .  '<div>' .  $row->title . '</div>';
					}

					
					// Returnera något
					return $start . $crumb;
				}  else {
					return $start . $divider . "Saknas";
				}
			break;
			default:
				echo "Whut?";
		}
	}
	
	// Hämta en regions namn
	function region_name($id)
	{
		$sql_check = "SELECT id, title FROM region_list WHERE id = '$id' LIMIT 1";
		$query = $this->CI->db->query($sql_check);
		
		if($query->num_rows() > 0) {
			$row = $query->row(); 
			return $row->title;
		} else {
			return "Saknas";
		}
	}
	
	// Spara sökvärden i sessionen, om dem inte redan finns där (kräver att input="name" är samma som session name)
	function search_session($field)
	{
		// Plocka värdet
		$this->CI->session->set_userdata($field, $this->CI->input->post($field));
		return $this->CI->session->userdata($field);
	}
}