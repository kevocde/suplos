<?php

final class HomeController
{
    public static function listProperties($request)
    {
        $city = isset($request['city']) ? $request['city'] : null;
        $type = isset($request['type']) ? $request['type'] : null;

        $properties = array_map(function ($item) {
            return array_merge($item, [
                'Precio' => '$ ' . number_format($item['Precio'], 0)
            ]);
        }, Utilities::getAllProperties($city, $type));
        
        return Utilities::json($properties);
    }

    public static function listPropertiesToMe($request)
    {

        $properties = array_map(function ($item) {
            return array_merge($item, [
                'Precio' => '$ ' . number_format($item['Precio'], 0)
            ]);
        }, Utilities::getAllPropertiesToMe());
        
        return Utilities::json($properties);
    }

    /**
     * Función para cargar los datos del JSON a la base de datos
     * 
     * @todo Retirar, ya que el origen de los datos ya es la misma tabla
     */
    public static function loadData()
    {
        $properties = Utilities::getAllProperties();
        
        $mysqli = Utilities::getConnection();
        foreach ($properties as $property) {
            $query = sprintf(
                "INSERT INTO bienes(Id, Direccion, Ciudad, Telefono, Codigo_Postal, Tipo, Precio) VALUES (%s, '%s', '%s', '%s', '%s', '%s', %s);",
                $property['Id'],
                trim($property['Direccion']),
                trim($property['Ciudad']),
                trim($property['Telefono']),
                trim($property['Codigo_Postal']),
                trim($property['Tipo']),
                str_replace(['$', ',', ' '], '', $property['Precio'])
            );
            $mysqli->query($query);
        }
    }

    /**
     * Retorna el listado de las ciudades disponibles para el filtro
     * 
     * @return string
     */
    public static function listCities($request)
    {
        return Utilities::json(Utilities::getCities());
    }

    /**
     * Retorna el listado de los tipos disponibles para el filtro
     * 
     * @return string
     */
    public static function listTypes($request)
    {
        return Utilities::json(Utilities::getTypes());
    }

    /**
     * Añade uno de los bienes a los míos
     * 
     * @return string
     */
    public static function addToMe($request)
    {
        $propertyId = isset($request['property']) ? $request['property'] : null;
        if ($propertyId) {
            Utilities::addProperty($propertyId);
        }

        return Utilities::json([]);
    }

    /**
     * Remueve uno de los bienes a los míos
     * 
     * @return string
     */
    public static function removeToMe($request)
    {
        $propertyId = isset($request['property']) ? $request['property'] : null;
        if ($propertyId) {
            Utilities::removeProperty($propertyId);
        }

        return Utilities::json([]);
    }
}