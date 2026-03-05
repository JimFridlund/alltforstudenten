<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package Studenten 2011
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

      // Update med query binding (säkrare + mindre strul med tecken)
      $sql = "UPDATE settings SET setting_value = ? WHERE setting_name = ?";
      $this->db->query($sql, array($val, $key));
    }
  }
}