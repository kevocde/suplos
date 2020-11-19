<?php

require_once __DIR__ . '/Utilities.php';

/**
 * Enrrutamiento para las solicitudes
 */
$routes = [
    'properties' => 'HomeController@listProperties',
    'load-data' => 'HomeController@loadData',
    'cities' => 'HomeController@listCities',
    'types' => 'HomeController@listTypes',
    'properties-to-me' => 'HomeController@listPropertiesToMe',
    'add-to-me' => 'HomeController@addToMe',
    'remove-to-me' => 'HomeController@removeToMe'
];

Utilities::router($routes, $action);