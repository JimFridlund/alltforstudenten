<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Settings {

  var $CI;
  var $option;

  function Settings()
  {
    $this->CI =& get_instance();
    $this->option = array();

    $this->CI->load->database();
    $this->_init();
  }

  function _init()
  {
    $this->CI->db->select('setting_name, setting_value');
    $query = $this->CI->db->get('settings');

    if ($query && $query->num_rows() > 0) {
      foreach ($query->result() as $row) {
        $this->option[$row->setting_name] = $row->setting_value;
      }
    }

    // Säkerställ nyckeln finns
    if (!isset($this->option['student_year'])) {
      $this->option['student_year'] = '';
    }

    // Auto-år om tomt: 1 juli switch
    $this->option['student_year_effective'] = $this->get_student_year();
  }

  // Returnerar år som ska användas på sidan
  function get_student_year()
  {
    $manual = isset($this->option['student_year']) ? trim((string)$this->option['student_year']) : '';
    if ($manual !== '' && preg_match('/^\d{4}$/', $manual)) {
      return (int)$manual;
    }

    // Auto: om datum >= 1 juli => nästa år, annars i år
    $now = time();
    $y = (int)date('Y', $now);

    $switch = mktime(0,0,0,7,1,$y); // 1 juli kl 00:00
    if ($now >= $switch) {
      return $y + 1;
    }
    return $y;
  }
}