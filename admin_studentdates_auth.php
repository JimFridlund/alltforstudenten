<?php
// public_html/admin_studentdates_auth.php
// HTTP Basic Auth direkt i PHP (ingen session/cookies). PHP 5.6-kompatibel.

$ADMIN_USER = 'info@weblings.se';
$ADMIN_PASS = 'FridlundGren1';

function auth_get_basic_userpass() {
    $user = '';
    $pass = '';

    if (isset($_SERVER['PHP_AUTH_USER'])) {
        $user = (string)$_SERVER['PHP_AUTH_USER'];
        $pass = isset($_SERVER['PHP_AUTH_PW']) ? (string)$_SERVER['PHP_AUTH_PW'] : '';
        return array($user, $pass);
    }

    // Ibland (FastCGI) hamnar auth här:
    $hdr = '';
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) $hdr = (string)$_SERVER['HTTP_AUTHORIZATION'];
    else if (isset($_SERVER['Authorization'])) $hdr = (string)$_SERVER['Authorization'];
    else if (function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        if (isset($headers['Authorization'])) $hdr = (string)$headers['Authorization'];
    }

    if ($hdr !== '' && stripos($hdr, 'basic ') === 0) {
        $b64 = trim(substr($hdr, 6));
        $decoded = base64_decode($b64);
        if ($decoded !== false) {
            $parts = explode(':', $decoded, 2);
            $user = isset($parts[0]) ? (string)$parts[0] : '';
            $pass = isset($parts[1]) ? (string)$parts[1] : '';
            return array($user, $pass);
        }
    }

    return array('', '');
}

function auth_require_basic($ADMIN_USER, $ADMIN_PASS) {
    list($u, $p) = auth_get_basic_userpass();

    if ($u === $ADMIN_USER && $p === $ADMIN_PASS) {
        return; // ok
    }

    header('WWW-Authenticate: Basic realm="Admin Studentdatum"');
    header('HTTP/1.0 401 Unauthorized');
    echo "401";
    exit;
}