<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Skola extends Controller
{
    function Skola()
    {
        parent::Controller();
        $this->load->model('School_gym_model', 'school');
    }

    // URL: /skola/{skolenhetskod}/{slug}
    function index($code = '', $slug = '')
    {
        if ($code === '') {
            show_404();
            return;
        }

        $row = $this->school->get_by_code($code);
        if (!$row) {
            show_404();
            return;
        }

        // Bygg canonical slug (stabilt för SEO)
        $correct_slug = $this->_slugify(isset($row['namn']) ? $row['namn'] : '');

        // Canonical URL: alltid med korrekt slug
        $base = rtrim($this->config->item('base_url'), '/');
        $canonical = $base . '/skola/' . $code . '/' . $correct_slug;

        // Om slug är fel eller saknas -> 301 till canonical
        if ($slug !== $correct_slug) {
            header("Location: " . $canonical, true, 301);
            exit;
        }

        // SEO-title
        $kommun_namn = '';
        if (!empty($row['kommun_namn'])) $kommun_namn = $row['kommun_namn'];
        else if (!empty($row['locality'])) $kommun_namn = $row['locality'];

        $title = (isset($row['namn']) ? $row['namn'] : 'Skola');
        if ($kommun_namn) $title .= ' – ' . $kommun_namn;
        $title .= ' | AlltFörStudenten';

        // Bygg interna länkar (om vi har slugs)
        $lan_slug = !empty($row['lan_slug']) ? $row['lan_slug'] : '';
        $kommun_slug = !empty($row['kommun_slug']) ? $row['kommun_slug'] : '';

        $lan_url = '';
        $kommun_url = '';
        if ($lan_slug) {
            $lan_url = $base . '/visa/' . $lan_slug;
            if ($kommun_slug) {
                $kommun_url = $base . '/visa/' . $lan_slug . '/' . $kommun_slug;
            }
        }

        
        // =========================
        // Hantera enheter (A/B/C, 1/2/3) på ett besökarvänligt sätt
        // - Listor visar basnamn
        // - På skolsidan visar vi ev. länkar till fler enheter
        // =========================
        $base_name = isset($row['namn']) ? trim((string)$row['namn']) : '';
        // Ta bort suffix " A"/" B" eller " 1"/" 2" osv.
        $base_name = preg_replace('/\s+[A-ZÅÄÖ]$/u', '', $base_name);
        $base_name = preg_replace('/\s+\d+$/u', '', $base_name);
        $base_name = trim($base_name);

        $unit_links = array();
        if ($base_name !== '' && !empty($row['lan_slug']) && !empty($row['kommun_slug']) && isset($this->db)) {
            $like = $base_name . ' %';
            $q = $this->db->query(
                "SELECT skolenhetskod, namn
                 FROM schools_gym
                 WHERE is_gym = 1
                   AND lan_slug = ?
                   AND kommun_slug = ?
                   AND (namn = ? OR namn LIKE ?)
                 ORDER BY namn ASC",
                array($row['lan_slug'], $row['kommun_slug'], $base_name, $like)
            );
            if ($q) {
                foreach ($q->result_array() as $r) {
                    $c = isset($r['skolenhetskod']) ? trim((string)$r['skolenhetskod']) : '';
                    if ($c === '' || $c === $code) continue;
                    $nm = isset($r['namn']) ? trim((string)$r['namn']) : '';
                    $unit_links[] = array(
                        'code' => $c,
                        'name' => $nm,
                        'url'  => $base . '/skola/' . $c . '/' . $this->_slugify($nm)
                    );
                }
            }
        }

        $data = array();
        $data['school'] = $row;
        $data['canonical'] = $canonical;
        $data['page_title'] = $title;

        $data['lan_url'] = $lan_url;
        $data['lan_name'] = !empty($row['lan_namn']) ? $row['lan_namn'] : '';
        $data['kommun_url'] = $kommun_url;
        $data['kommun_name'] = $kommun_namn;
        $data['unit_links'] = $unit_links;

        $this->load->view('school_gym_single', $data);
    }

    function _slugify($str)
    {
        $str = trim((string)$str);

        // mbstring kan saknas på vissa PHP 5.6-installationer, så vi skyddar oss
        if (function_exists('mb_strtolower')) {
            $str = mb_strtolower($str, 'UTF-8');
        } else {
            $str = strtolower($str);
        }

        // ersätt åäö
        $str = str_replace(array('å','ä','ö','Å','Ä','Ö'), array('a','a','o','a','a','o'), $str);

        // bara a-z0-9 och mellanslag/bindestreck
        $str = preg_replace('/[^a-z0-9\-\s]/', '', $str);
        $str = preg_replace('/[\s\-]+/', '-', $str);
        $str = trim($str, '-');

        if ($str === '') $str = 'skola';
        return $str;
    }
}
