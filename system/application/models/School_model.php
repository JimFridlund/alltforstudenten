<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class School_model extends Model
{
    function School_model()
    {
        parent::Model();
    }

    /**
     * Sparar (upsert) en skola.
     * $row är en assoc-array med nycklar:
     * skolenhetskod, namn, kommun, lan, postort, adress, lat, lng, raw_json
     */
    function upsert_school($row)
    {
        $now = date('Y-m-d H:i:s');

        $data = array(
            'skolenhetskod' => isset($row['skolenhetskod']) ? $row['skolenhetskod'] : '',
            'namn'          => isset($row['namn']) ? $row['namn'] : '',
            'kommun'        => isset($row['kommun']) ? $row['kommun'] : null,
            'lan'           => isset($row['lan']) ? $row['lan'] : null,
            'postort'       => isset($row['postort']) ? $row['postort'] : null,
            'adress'        => isset($row['adress']) ? $row['adress'] : null,
            'lat'           => isset($row['lat']) ? $row['lat'] : null,
            'lng'           => isset($row['lng']) ? $row['lng'] : null,
            'raw_json'      => isset($row['raw_json']) ? $row['raw_json'] : null,
            'updated_at'    => $now
        );

        // Finns redan?
        $q = $this->db->get_where('schools', array('skolenhetskod' => $data['skolenhetskod']), 1);
        if ($q && $q->num_rows() > 0) {
            $this->db->where('skolenhetskod', $data['skolenhetskod']);
            $this->db->update('schools', $data);
            return 'updated';
        }

        $this->db->insert('schools', $data);
        return 'inserted';
    }

    function count_schools()
    {
        return (int)$this->db->count_all('schools');
    }
}