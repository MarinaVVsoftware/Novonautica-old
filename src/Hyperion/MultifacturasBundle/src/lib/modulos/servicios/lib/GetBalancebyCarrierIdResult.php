<?php
// <!-- phpDesigner :: Timestamp [11/03/2016 06:00:35 p. m.] -->
/**
 * MultiFacturas.com 2016
 * Author: I.R.G.
 */
class GetBalancebyCarrierIdResult {
    public static function fromArray($array) {
        // Se verifica que sea un array
        if(!is_array($array)) {
            throw new Exception(sprintf("%s::%s El parámetro debe ser un array.", __CLASS__, __FUNCTION__));
        }

        // Se verifica que exista el elemento 'GetBalancebyCarrierIdResult'
        if(!array_key_exists(__CLASS__, $array)) {
            throw new Exception(sprintf("%s::%s No se encontro el elemento '%s'.", __CLASS__, __FUNCTION__, __CLASS__));
        }

        // Se obtiene el arreglo de balances (Solo tiene uno)
        $arreglo = ArrayOfAccountBalance::fromXML($array[__CLASS__]);

        // Se retorna el balance
        return array_pop($arreglo);
    }
}