<?php
// <!-- phpDesigner :: Timestamp [11/03/2016 05:57:57 p. m.] -->
/**
 * MultiFacturas.com 2016
 * Author: I.R.G.
 */
class GetAvailableBanksResult {
    public static function fromArray($array) {
        // Se verifica que sea un arreglo
        if(!is_array($array)) {
            throw new Exception(sprintf("%s::%s El parámetro debe ser un array", __CLASS__, __FUNCTION__));
        }

        // Se verifica que exista el elemento 'GetAvailableBanksResult'
        if(!array_key_exists(__CLASS__, $array)){
            throw new Exception(sprintf("%s::%s No se encontró el elemento %s", __CLASS__, __FUNCTION__, __CLASS__));
        }

        // Se retorna el arreglo de bancos
        return $array[__CLASS__]['string'];
    }
}