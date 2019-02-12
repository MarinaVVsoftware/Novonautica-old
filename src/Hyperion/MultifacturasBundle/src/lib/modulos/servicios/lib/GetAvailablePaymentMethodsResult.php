<?php
// <!-- phpDesigner :: Timestamp [11/03/2016 06:00:38 p. m.] -->
/**
 * MultiFacturas.com 2016
 * Author: I.R.G.
 */
class GetAvailablePaymentMethodsResult {
    public static function fromArray($array) {
        /*
         * array (size=1)
          'GetAvailablePaymentMethodsResult' =>
            array (size=1)
              'string' =>
                array (size=4)
                  0 => string 'Efectivo' (length=8)
                  1 => string 'Transferencia' (length=13)
                  2 => string 'Cheque' (length=6)
                  3 => string 'N/A' (length=3)
         */

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