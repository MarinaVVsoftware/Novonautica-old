<?php
// <!-- phpDesigner :: Timestamp [11/03/2016 05:58:00 p. m.] -->
/**
 * MultiFacturas.com 2016
 * Author: I.R.G.
 */
class GetAllProductExtendListResult
{
    public static function fromArray($array){
        // Se verifica que llegue un array
        if(!is_array($array)) {
            throw new Exception(sprintf("%s::%s El parámetro debe ser un array", __CLASS__, __FUNCTION__));
        }

        // Se verifica que tenga como primer elemento 'GetAllProductExtendListResult'
        if(!array_key_exists(__CLASS__, $array)) {
            throw new Exception(sprintf("%s::%s No se encontro el elemento '%s'", __CLASS__, __FUNCTION__, __CLASS__));
        }

        // Se obtiene la informacion de los ProductExtended
        $array = $array['GetAllProductExtendListResult'];

        // Se verifica que contenga el elemento 'ProductExtended'
        if(!array_key_exists('ProductExtended', $array)) {
            throw new Exception(sprintf("%s::%s No se encontro el elemento 'ProductExtended'", __CLASS__, __FUNCTION__));
        }

        // Se obtiene la información de los Productos
        $array = $array['ProductExtended'];

        // Arreglo de Objetos ProductExtended
        $productsExtended = array();

        // Se recorren los productos
        foreach($array as $producto) {

            // Se crea el objeto ProductExtended
            $prod = ProductExtended::fromArray($producto);

            // Se agrega al arreglo
            array_push($productsExtended, $prod);
        }

        // Se devuelve el arreglo de ProductExtended
        return $productsExtended;
    }
}