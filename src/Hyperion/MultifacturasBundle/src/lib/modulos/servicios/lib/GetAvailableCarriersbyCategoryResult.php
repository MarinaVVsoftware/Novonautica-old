<?php
// <!-- phpDesigner :: Timestamp [11/03/2016 05:57:49 p. m.] -->
/**
 * MultiFacturas.com 2016
 * Author: I.R.G.
 */
class GetAvailableCarriersbyCategoryResult {
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
        $array = $array[__CLASS__];
        if(!is_array($array))
            return array();

        // Se verifica que contenga el elemento 'Carrier'
        if(!array_key_exists('Carrier', $array)) {
            throw new Exception(sprintf("%s::%s No se encontro el elemento '%s'", __CLASS__, __FUNCTION__, 'Carrier'));
        }
        
        // Carriers a devolver
        $carriers = array();
        
        // Se extraen los carrier
        $array = $array['Carrier'];
        
        // Si es uno solo
        if(array_key_exists('CarrierId', $array))
        {
            array_push($carriers, Carrier::fromArray($array));
        }
        else
        {        
            // Se recorren los productos
            foreach($array as $carrier) {
    
                // Se crea el objeto ProductExtended
                $car = Carrier::fromArray($carrier);
    
                // Se agrega al arreglo
                array_push($carriers, $car);
            }
        }

        // Se devuelve el arreglo de ProductExtended
        return $carriers;
    }
}