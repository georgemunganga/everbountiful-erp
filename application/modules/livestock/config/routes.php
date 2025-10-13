<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['sheds']                  = "livestock/livestock/sheds";
$route['sheds/(:num)']           = "livestock/livestock/sheds";
$route['sheds/create']           = "livestock/livestock/shed_form";
$route['sheds/edit/(:num)']      = "livestock/livestock/shed_form/$1";
$route['sheds/delete/(:num)']    = "livestock/livestock/shed_delete/$1";

$route['productions']                  = "livestock/livestock/productions";
$route['productions/(:num)']           = "livestock/livestock/productions";
$route['productions/create']           = "livestock/livestock/production_form";
$route['productions/edit/(:num)']      = "livestock/livestock/production_form/$1";
$route['productions/delete/(:num)']    = "livestock/livestock/production_delete/$1";

$route['livestock-groups']               = "livestock/livestock/livestock_groups";
$route['livestock-groups/(:num)']        = "livestock/livestock/livestock_groups";
$route['livestock-groups/create']        = "livestock/livestock/livestock_group_form";
$route['livestock-groups/edit/(:num)']   = "livestock/livestock/livestock_group_form/$1";
$route['livestock-groups/delete/(:num)'] = "livestock/livestock/livestock_group_delete/$1";

$route['livestocks']                  = "livestock/livestock/livestocks";
$route['livestocks/(:num)']           = "livestock/livestock/livestocks";
$route['livestocks/name/(:any)']      = "livestock/livestock/livestocks/$1";
$route['livestocks/name/(:any)/(:num)'] = "livestock/livestock/livestocks/$1";
$route['livestocks/create']           = "livestock/livestock/livestock_form";
$route['livestocks/edit/(:num)']      = "livestock/livestock/livestock_form/$1";
$route['livestocks/delete/(:num)']    = "livestock/livestock/livestock_delete/$1";

$route['feeds']                  = "livestock/livestock/feeds";
$route['feeds/(:num)']           = "livestock/livestock/feeds";
$route['feeds/create']           = "livestock/livestock/feed_form";
$route['feeds/edit/(:num)']      = "livestock/livestock/feed_form/$1";
$route['feeds/delete/(:num)']    = "livestock/livestock/feed_delete/$1";

$route['feed-usages']                  = "livestock/livestock/feed_usages";
$route['feed-usages/(:num)']           = "livestock/livestock/feed_usages";
$route['feed-usages/create']           = "livestock/livestock/feed_usage_form";
$route['feed-usages/edit/(:num)']      = "livestock/livestock/feed_usage_form/$1";
$route['feed-usages/delete/(:num)']    = "livestock/livestock/feed_usage_delete/$1";

$route['vaccines']                  = "livestock/livestock/vaccines";
$route['vaccines/(:num)']           = "livestock/livestock/vaccines";
$route['vaccines/create']           = "livestock/livestock/vaccine_form";
$route['vaccines/edit/(:num)']      = "livestock/livestock/vaccine_form/$1";
$route['vaccines/delete/(:num)']    = "livestock/livestock/vaccine_delete/$1";

$route['vaccine-usages']                  = "livestock/livestock/vaccine_usages";
$route['vaccine-usages/(:num)']           = "livestock/livestock/vaccine_usages";
$route['vaccine-usages/create']           = "livestock/livestock/vaccine_usage_form";
$route['vaccine-usages/edit/(:num)']      = "livestock/livestock/vaccine_usage_form/$1";
$route['vaccine-usages/delete/(:num)']    = "livestock/livestock/vaccine_usage_delete/$1";

$route['farm-reports']       = "livestock/livestock/farm_reports";
$route['farm-reports/data']  = "livestock/livestock/farm_reports_data";
$route['production-percentage'] = "livestock/livestock/production_percentage";

$route['stock']                = "livestock/stock/index";
$route['stock/consumption']    = "livestock/stock/consumption";
$route['stock/waste']          = "livestock/stock/waste";
$route['stock/transfers']      = "livestock/stock/transfers";
$route['stock/opening-balance']    = "livestock/stock/opening_balance";
$route['livestock/consumption'] = "livestock/stock/consumption";
$route['livestock/waste']       = "livestock/stock/waste";
$route['livestock/transfers']   = "livestock/stock/transfers";
$route['livestock/opening-balance'] = "livestock/stock/opening_balance";
