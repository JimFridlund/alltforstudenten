<?php
/**
 * @package Studenten 2011
 * @copyright 2010, iDenta labs
 * @author Kristoffer Lidman
 */
class Settings_model extends Model 
{
	function Settings_model()
	{
		parent::Model();
	}
	
	// Uppdatera inställningar
	function save_settings()
	{
		foreach (array_keys($_POST) as $key) 
		{
			$val = $this->input->post($key, true);
			
			$sql = "UPDATE settings SET setting_value = '$val' WHERE setting_name = '$key'";
			$this->db->query($sql);
        } 
	}
}
?>