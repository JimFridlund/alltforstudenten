<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class SkolverketClient
{
    var $CI;
    var $base_url;
    var $timeout;

    function SkolverketClient()
    {
        $this->CI =& get_instance();
        $this->CI->load->config('skolverket');

        $this->base_url = rtrim($this->CI->config->item('skolverket_base_url'), '/');
        $this->timeout  = (int)$this->CI->config->item('skolverket_timeout_seconds');
        if ($this->timeout <= 0) $this->timeout = 10;
    }

    /**
     * Hämtar en sida JSON från Skolverket.
     * Returnerar assoc-array.
     */
    function get_json($path_with_query)
    {
        $url = $this->base_url . '/' . ltrim($path_with_query, '/');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));

        $body = curl_exec($ch);
        $err  = curl_error($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($body === false) {
            return array('ok' => false, 'error' => 'cURL error: ' . $err, 'http_code' => $code);
        }

        $json = json_decode($body, true);
        if ($json === null && json_last_error() !== JSON_ERROR_NONE) {
            return array('ok' => false, 'error' => 'JSON decode error', 'http_code' => $code, 'raw' => $body);
        }

        if ($code < 200 || $code >= 300) {
            return array('ok' => false, 'error' => 'HTTP ' . $code, 'http_code' => $code, 'data' => $json);
        }

        return array('ok' => true, 'http_code' => $code, 'data' => $json);
    }
}