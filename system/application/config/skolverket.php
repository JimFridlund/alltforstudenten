<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$config['skolverket_base_url'] = 'https://api.skolverket.se/skolenhetsregistret';
$config['skolverket_timeout_seconds'] = 10;

// En enkel “hemlig nyckel” så att ingen utom du kan köra importen via webben.
// Byt värdet direkt.
$config['skolverket_cron_token'] = 'FridlundGren1';