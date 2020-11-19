<?php

require_once __DIR__ . '/Utilities.php';

/**
 * Enrrutamiento para las solicitudes
 */
$routes = [
    'properties' => 'HomeController@listProperties',
    'load-data' => 'HomeController@loadData',
    'cities' => 'HomeController@listCities',
    'types' => 'HomeController@listTypes'
];

Utilities::router($routes, $action);