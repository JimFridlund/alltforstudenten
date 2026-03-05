<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['base_url'] = 'https://www.alltforstudenten.se/';
$config['index_page'] = '';
$config['uri_protocol']	= 'AUTO';

$config['url_suffix'] = '';

$config['language']	= 'swedish';
$config['charset'] = 'UTF-8';
$config['enable_hooks'] = FALSE;

$config['subclass_prefix'] = 'MY_';

/*
| VIKTIGT: Tillåt ?=& så att ?v=2 inte kan trigga "disallowed characters"
*/
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-\?&=';

$config['enable_query_strings'] = FALSE;
$config['controller_trigger'] = 'c';
$config['function_trigger'] = 'm';
$config['directory_trigger'] = 'd';

$config['log_threshold'] = 1;
$config['log_path'] = '';
$config['log_date_format'] = 'Y-m-d H:i:s';

/*
| Om du hade en tidigare här: behåll den gamla nyckeln istället.
*/
$config['encryption_key'] = 'KEEP_YOUR_OLD_KEY_HERE_IF_YOU_HAVE_ONE';

$config['sess_cookie_name']		= 'ci_session';
$config['sess_expiration']		= 7200;
$config['sess_expire_on_close']	= FALSE;
$config['sess_encrypt_cookie']	= FALSE;
$config['sess_use_database']	= FALSE;
$config['sess_table_name']		= 'ci_sessions';
$config['sess_match_ip']		= FALSE;
$config['sess_match_useragent']	= TRUE;
$config['sess_time_to_update']	= 300;

$config['cookie_prefix']	= '';
$config['cookie_domain']	= '';
$config['cookie_path']		= '/';
$config['cookie_secure']	= TRUE;

$config['global_xss_filtering'] = FALSE;

$config['csrf_protection'] = FALSE;
$config['csrf_token_name'] = 'csrf_test_name';
$config['csrf_cookie_name'] = 'csrf_cookie_name';
$config['csrf_expire'] = 7200;

$config['compress_output'] = FALSE;

@date_default_timezone_set('Europe/Stockholm');

$config['rewrite_short_tags'] = FALSE;