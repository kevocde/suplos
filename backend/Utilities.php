<?php

/**
 * Funciones y utilizades generales para el sistema
 * 
 * @author Kevin Guzmán <kevindanielguzmen98@gmail.com>
 */

final class Utilities
{
    const ACTION_POS = 'a';
    const DEFAULT_ACTION = 'index';

    /**
     * Realiza un enrrutamiento básico utilizando la acción pasada y el arreglo de rutas pasado por parámetros
     */
    public static function router($routes, $request)
    {
        $action = isset($_GET[static::ACTION_POS]) ? $_GET[static::ACTION_POS] : static::DEFAULT_ACTION;
        list($controller, $action) = explode('@', $routes[$action]);
        
        require_once __DIR__ . "/Controllers/$controller.php";
        
        echo call_user_func_array([$controller, $action], ['request' => $_REQUEST]);
    }

    /**
     * Pone las cabeceras y el parseo necesario para responder con un json
     */
    public static function json($array)
    {
        header('Content-Type: application/json');
        return json_encode($array);
    }

    /**
     * Realiza la conexión a la base de datos
     */
    public static function getConnection() {
        $mysqli = new mysqli('127.0.0.1', 'root', 'secret', 'intelcost_bienes');
        if ($mysqli->connect_errno) {
            die('Error al intentar conectar a la base de datos: ' . $mysqli->connect_error);
        }
        return $mysqli;
    }

    /**
     * Transforma un resultado Resultset en un arreglo asociativo
     */
    public static function getAssocData($result) {
        $data = [];
        while ($item = $result->fetch_assoc()) $data[] = $item;
        return $data;
    }

    /**
     * Realiza la consulta de los bienes almacenados en la base de datos
     * 
     * @return array
     */
    public static function getAllProperties()
    {
        $mysqli = static::getConnection();
        $result = $mysqli->query("SELECT * FROM bienes ORDER BY Id;");

        return $result->num_rows ? static::getAssocData($result) : [];
    }

    /**
     * Realiza una consulta de todas las ciudades disponibles para realizar el filtro
     * 
     * @return array
     */
    public static function getCities()
    {
        $mysqli = static::getConnection();
        $result = $mysqli->query("SELECT DISTINCT Ciudad FROM bienes");

        return $result->num_rows ? static::getAssocData($result) : [];
    }

    /**
     * Realiza una consulta de todas los tipos disponibles para realizar el filtro
     * 
     * @return array
     */
    public static function getTypes()
    {
        $mysqli = static::getConnection();
        $result = $mysqli->query("SELECT DISTINCT Tipo FROM bienes");

        return $result->num_rows ? static::getAssocData($result) : [];
    }
}