<?php

final class HomeController
{
    public static function listProperties($request)
    {
        $city = isset($_REQUEST['city']) ? $_REQUEST['city'] : null;
        $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : null;

        $properties = array_map(function ($item) {
            return array_merge($item, [
                'Precio' => '$ ' . number_format($item['Precio'], 0)
            ]);
        }, Utilities::getAllProperties($city, $type));
        
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
            print_r($query);
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
}