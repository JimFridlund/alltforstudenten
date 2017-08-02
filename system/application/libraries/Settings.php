<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Settings {
    
    var $CI;
    var $option;
    
    /**
     * Constructor
     *
     * @access public
     */
    function Settings()
    {
        $this->CI =& get_instance();
        $this->option = array();

        // Ladda DB hantering
        $this->CI->load->database();

        // Hämta globala variabler
        $this->_init();
    }

    // ------------------------------------------------------------------------    
    
    /**
     * Hämta all inställningar
     *
     * @access private
     */
    function _init()
    {        
        $this->CI->db->select('setting_name, setting_value');
        $query = $this->CI->db->get('settings');

        if ($query->num_rows() == 0)
        {
            show_error('Could not get settings from DB');
        }

        foreach($query->result() as $row)
        {
            $this->option[$row->setting_name] = $row->setting_value;
        }
    }
} 

?>