<?php
// <!-- phpDesigner :: Timestamp [11/03/2016 05:57:39 p. m.] -->
/**
 * MultiFacturas.com 2016
 * Author: I.R.G.
 */
class GetAvailableCarriersResult
{
    public static function fromArray($array) {
        // Se verifica que llegue un array
        if(!is_array($array)) {
            throw new Exception(sprintf("%s::%s El parámetro debe ser un array", __CLASS__, __FUNCTION__));
        }

        // Se verifica que tenga como primer elemento 'GetAvailableCarriersResult'
        if(!array_key_exists(__CLASS__, $array)) {
            throw new Exception(sprintf("%s::%s No se encontro el elemento '%s'", __CLASS__, __FUNCTION__, __CLASS__));
        }

        // Se obtiene la informacion de los Carrier
        $array = $array['GetAvailableCarriersResult'];

        // Se verifica que contenga el elemento 'Carrier'
        if(!array_key_exists('Carrier', $array)) {
            throw new Exception(sprintf("%s::%s No se encontro el elemento 'Carrier'", __CLASS__, __FUNCTION__));
        }

        // Se obtiene la información de los todos Carrier
        $array = $array['Carrier'];

        // Arreglo de Objetos ProductExtended
        $carriers = array();

        // Se recorren los productos
        foreach($array as $carrier) {

            // Se crea el objeto ProductExtended
            $car = Carrier::fromArray($carrier);

            // Se agrega al arreglo
            array_push($carriers, $car);
        }

        // Se devuelve el arreglo de ProductExtended
        return $carriers;
    }
}