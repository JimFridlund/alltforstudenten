<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
| 	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['scaffolding_trigger'] = 'scaffolding';
|
| This route lets you set a "secret" word that will trigger the
| scaffolding feature for added security. Note: Scaffolding must be
| enabled in the controller in which you intend to use it.   The reserved 
| routes must come before any wildcard or regular expression routes.
|
*/

$route['default_controller'] = "main";
$route['scaffolding_trigger'] = "";

$route['visa/:any'] = "visa/lan";


// Undersidor
$route['bal-student'] = "pages/bal_student";
$route['bal-student/studentplakat'] = "pages/studentplakat";
$route['bal-student/studentklader'] = "pages/studentklader";
$route['bal-student/balklader'] = "pages/balklader";
$route['bal-student/transport'] = "pages/transport";
$route['bal-student/fotograf'] = "pages/fotograf";
$route['bal-student/studentmossa'] = "pages/studentmossa";
$route['bal-student/presenter'] = "pages/presenter";
$route['bal-student/catering'] = "pages/catering";
$route['bal-student/studentflak'] = "pages/studentflak";
$route['bal-student/skor'] = "pages/skor";
$route['bal-student/makeup'] = "pages/makeup";
$route['bal-student/blommor'] = "pages/blommor";
$route['bal-student/frisor'] = "pages/frisor";
$route['bal-student/hogskoleprovet'] = "pages/hogskoleprovet";
$route['bal-student/slips'] = "pages/slips";

$route['om-oss'] = "pages/om_oss";
$route['om-oss/medarbetare'] = "pages/medarbetare";
$route['om-oss/webbplatsen'] = "pages/webbplatsen";
$route['om-oss/kontakt'] = "pages/kontakt";

/* End of file routes.php */
/* Location: ./system/application/config/routes.php */