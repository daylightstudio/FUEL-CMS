<?php 
// $route[FUEL_ROUTE.'tools/search/'] = FUEL_FOLDER.'/module';
$route[FUEL_ROUTE.'tools/search'] = 'search/search_module';
$route[FUEL_ROUTE.'tools/search/(.*)'] ='search/search_module/$1';
$route[FUEL_ROUTE.'tools/search/reindex'] = 'search/search_module/reindex';
