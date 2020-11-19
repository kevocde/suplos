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
        $mysqli = new mysqli('us-cdbr-east-02.cleardb.com', 'b53c2758ed7b2d', 'ed638a88', 'heroku_a133d8557df7b00');
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
    public static function getAllProperties($city = null, $type = null)
    {
        $queryWhere = "WHERE 1=1";
        
        if ($city) $queryWhere .= sprintf(" AND Ciudad='%s'", $city);
        if ($type) $queryWhere .= sprintf(" AND Tipo='%s'", $type);

        $mysqli = static::getConnection();
        $result = $mysqli->query("SELECT * FROM bienes ".  $queryWhere  ." ORDER BY Id;");

        return $result->num_rows ? static::getAssocData($result) : [];
    }

    /**
     * Realiza la consulta de los bienes almacenados en la base de datos que son mios
     * 
     * @return array
     */
    public static function getAllPropertiesToMe()
    {
        $mysqli = static::getConnection();
        $result = $mysqli->query("SELECT DISTINCT(bienes.Id), bienes.* FROM bienes INNER JOIN bienes_seleccionados ON bienes.Id = bienes_seleccionados.Id_bien ORDER BY bienes.Id;");

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

    /**
     * Añade la pripiedad a mis bienes
     */
    public static function addProperty($idProperty)
    {
        $mysqli = Utilities::getConnection();
        $query = sprintf(
            "INSERT INTO bienes_seleccionados(Id_bien) VALUES (%s);",
            $idProperty
        );
        $mysqli->query($query);
    }

    /**
     * Elimina la pripiedad a mis bienes
     */
    public static function removeProperty($idProperty)
    {
        $mysqli = Utilities::getConnection();
        $query = sprintf(
            "DELETE FROM bienes_seleccionados WHERE Id_bien = %s;",
            $idProperty
        );
        $mysqli->query($query);
    }
}