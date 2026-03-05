<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class School_gym_model extends Model
{
    function School_gym_model()
    {
        parent::Model();
    }

    function get_by_code($code)
    {
        $q = $this->db->get_where('schools_gym', array('skolenhetskod' => $code), 1);
        if ($q && $q->num_rows() > 0) {
            return $q->row_array();
        }
        return null;
    }
}