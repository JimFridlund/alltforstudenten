<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Routes (CodeIgniter 1.x korrekt wildcard-syntax)
| -------------------------------------------------------------------------
*/

$route['default_controller'] = "main";
$route['scaffolding_trigger'] = "";

/* Backend */
$route['backend'] = "backend";
$route['backend/(:any)'] = "backend/$1";
$route['backend/(:any)/(:any)'] = "backend/$1/$2";
$route['backend/(:any)/(:any)/(:any)'] = "backend/$1/$2/$3";

/* Visa län/kommun
   Exempel:
   /visa/stockholms-lan
   /visa/stockholms-lan/stockholms-stad
*/
$route['visa/(:any)'] = "visa/lan/$1";
$route['visa/(:any)/(:any)'] = "visa/lan/$1/$2";

/* Balen */
$route['balen'] = "pages/balen";
$route['balen/(:any)'] = "pages/catchall/balen/$1";

/* Kontakt */
$route['kontakt'] = "pages/kontakt";
$route['kontakt/(:any)'] = "pages/kontakt/$1";
$route['kontakt/skicka'] = "pages/kontakt_submit";

/* Om oss */
$route['om-oss'] = "pages/om_oss";
$route['om-oss/medarbetare'] = "pages/medarbetare";
$route['om-oss/webbplatsen'] = "pages/webbplatsen";
$route['om-oss/kontakt'] = "pages/kontakt";

/* Backwards compatibility */
$route['bal-student'] = "pages/bal_student";
$route['bal-student/(:any)'] = "pages/catchall/bal-student/$1";

/* 404 */
$route['404_override'] = '';
$route[''] = $route['default_controller'];